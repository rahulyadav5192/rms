<?php

namespace App\Http\Controllers;

use App\DataTables\LeaveDataTable;
use App\Helper\Reply;
use App\Http\Requests\Leaves\ActionLeave;
use App\Http\Requests\Leaves\StoreLeave;
use App\Http\Requests\Leaves\UpdateLeave;
use App\Models\EmployeeDetails;
use App\Models\EmployeeLeaveQuota;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\LeaveSetting;
use App\Models\LeaveType;
use App\Models\User;
use App\Scopes\ActiveScope;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeaveController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.leaves';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('leaves', $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LeaveDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_leave');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));
    
        $reportingTo = User::with('employeeDetail')->whereHas('employeeDetail', function ($q) {
            $q->where('reporting_to', user()->id);
        })->get();
    
        $employee = User::allEmployees(null, true, ($viewPermission == 'all' ? 'all' : null));
        $this->employees = $reportingTo->merge($employee);
    
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');
        
        $this->leaveTypes = LeaveType::all();
    
        // ✅ Leaves already applied in this month
        $this->can_apply = DB::table('leaves')
            ->where('user_id', user()->id)
            ->where('status','!=','rejected')
            ->where('manager_status','!=','2')
            ->whereMonth('leave_date', $currentMonth)
            ->whereYear('leave_date', $currentYear)
            ->count();
    
        // ✅ Get leave rules from function
        
        $max_leave = $this->getLeaveDetails(user()->id, true); // returns numeric leaves_per_month
    
        // ✅ Leave bucket (remaining allowance for current month)
        $this->leave_bucket = max($max_leave - $this->can_apply, 0);
        $this->employee_det = DB::table('employee_details')->where('user_id', user()->id)->first();
        return $dataTable->render('leaves.index', $this->data);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->addPermission = user()->permission('add_leave');
        abort_403(!in_array($this->addPermission, ['all', 'added']));



        
        
        $this->employees = User::allEmployees(null, true, ($this->addPermission == 'all' ? 'all' : null));

        if ($this->addPermission == 'added') {
            $this->defaultAssign = User::with('leaveTypes', 'leaveTypes.leaveType')->findOrFail(user()->id);
            $this->leaveQuotas = $this->defaultAssign->leaveTypes;

        }
        else if (isset(request()->default_assign)) {
            $this->defaultAssign = User::with('leaveTypes', 'leaveTypes.leaveType')->findOrFail(request()->default_assign);
            $this->leaveQuotas = $this->defaultAssign->leaveTypes;

        }
        else {
            $this->leaveTypes = LeaveType::all();
        }
        if($this->addPermission != 'all'){
            $this->max_leave = $this->getLeaveDetails(user()->id);
        }else{
            $this->max_leave = 3;
        }

        if (request()->ajax()) {
            $this->pageTitle = __('modules.leaves.addLeave');
            $html = view('leaves.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }
        
        $employee = user();
    
        // ✅ Get allowed leaves using your updated function
        $max_leave = $this->getLeaveDetails($employee->id);
    
        // ✅ Count leaves already taken in this month
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
    
        $leavesThisMonth = Leave::where('user_id', $employee->id)
            ->where('status', 'approved')
            ->whereBetween('leave_date', [$currentMonthStart, $currentMonthEnd])
            ->count();
    
        // ✅ Branch 7 rule (Kolkata): Max 2 leaves per month after 3 months
        if ($employee->branch_id == 7) {
            $joiningDate = Carbon::parse($employee->employeeDetail->joining_date);
            $months = $joiningDate->diffInMonths(Carbon::now());
    
            if ($months < 3) {
                return Reply::error(__('You are not eligible for leaves until 3 months of service.'));
            }
    
            if ($leavesThisMonth >= 2) {
                return Reply::error(__('You can take a maximum of 2 leaves per month.'));
            }
        } else {
            // ✅ For other branches, enforce based on $max_leave
            if ($leavesThisMonth >= $max_leave) {
                return Reply::error(__('You have already reached the maximum leaves allowed for this month.'));
            }
        }
        
        

        $this->view = 'leaves.ajax.create';

        return view('leaves.create', $this->data);
    }

    
    
    public function getLeaveDetails($emp_id, $returnNumber = true)
    {
        $employee = DB::table('employee_details')->where('user_id', $emp_id)->first();
    
        if (!$employee) {
            return 0;
        }
    
        $joiningDate = Carbon::parse($employee->joining_date);
        $currentDate = Carbon::now();
        $months = $joiningDate->diffInMonths($currentDate);
    
        $leavesPerMonth = 0;
        $maxBucketedLeaves = 1;
    
        if ($months >= 6) {
            $leavesPerMonth = 1.5;
        } elseif ($months >= 3) {
            $leavesPerMonth = 1;
        }
    
        if($employee->branch_id == 7){
            if ($months >= 3) {
                $leavesPerMonth = 2; // Kolkata: max 2 leaves per month
            } else {
                $leavesPerMonth = 0; // Not eligible if <3 months
            }
            $maxBucketedLeaves = 2;
        }
    
        // If we only want number for calculations
        if ($returnNumber) {
            return $leavesPerMonth;
        }
    
        // Otherwise return full JSON (for API)
        $takenLeaves = Leave::where('user_id', $employee->user_id)->where('status', 'approved')->count();
        $availableLeaves = ($leavesPerMonth * $months) - $takenLeaves;
    
        return response()->json([
            'employee_id' => $employee->id,
            'name' => $employee->name,
            'joining_date' => $employee->joining_date,
            'months_since_joining' => $months,
            'leaves_per_month' => $leavesPerMonth,
            'total_leaves' => $leavesPerMonth * $months,
            'available_leaves' => $availableLeaves,
        ]);
    }



    /**
     * @param StoreLeave $request
     * @return array|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(StoreLeave $request)
    {
        $this->addPermission = user()->permission('add_leave');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('leaves.index');
        }

        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $employeeLeaveQuota = EmployeeLeaveQuota::whereUserId($request->user_id)->whereLeaveTypeId($request->leave_type_id)->first();

        $leaveApplyDate = $request->duration == 'multiple'
            ? Carbon::parse(explode(',', $request->multi_date)[0])
            : Carbon::createFromFormat($this->company->date_format, $request->leave_date);
    
        $requestedLeaveDays = ($request->duration == 'multiple')
            ? count(explode(',', $request->multi_date))
            : (($request->duration == 'first_half' || $request->duration == 'second_half') ? 0.5 : 1);
    
        $eligibility = $this->checkCasualLeaveEligibility($request->user_id, $request->leave_type_id, $leaveApplyDate, $requestedLeaveDays);
    
        if (!$eligibility['status']) {
            return Reply::error($eligibility['message']);
        }
        
        
        $totalAllowedLeaves = ($employeeLeaveQuota) ? $employeeLeaveQuota->no_of_leaves : $leaveType->no_of_leaves;
        $uniqueId = Str::random(16);

        if ($leaveType->monthly_limit > 0) {
            if ($request->duration != 'multiple') {
                $duration = match ($request->duration) {
                    'first_half', 'second_half' => 'half day',
                    default => $request->duration,
                };

                $leaveTaken = LeaveType::byUser($request->user_id, $request->leave_type_id, array('approved', 'pending'), $request->leave_date);

                $dateApplied = Carbon::createFromFormat($this->company->date_format, $request->leave_date);

                /** @phpstan-ignore-next-line */
                $currentMonthFullDay = Leave::whereBetween('leave_date', [$dateApplied->startOfMonth()->toDateString(), $dateApplied->endOfMonth()->toDateString()])
                    ->where('leave_type_id', $leaveType->id)
                    ->where('duration', '<>', 'half day')
                    ->whereIn('status', ['approved', 'pending'])
                    ->where('user_id', $request->user_id)
                    ->get()->count();

                /** @phpstan-ignore-next-line */
                $currentMonthHalfDay = Leave::whereBetween('leave_date', [$dateApplied->startOfMonth()->toDateString(), $dateApplied->endOfMonth()->toDateString()])
                    ->where('leave_type_id', $leaveType->id)
                    ->where('duration', 'half day')
                    ->whereIn('status', ['approved', 'pending'])
                    ->where('user_id', $request->user_id)
                    ->get()->count();

                /** @phpstan-ignore-next-line */
                $appliedLimit = ($currentMonthFullDay + ($currentMonthHalfDay / 2)) + (($duration == 'half day') ? 0.5 : 1);

                /** @phpstan-ignore-next-line */
                if (isset($leaveTaken[0]->leavesCount[0]) && ((($leaveTaken[0]->leavesCount[0]->count - ($leaveTaken[0]->leavesCount[0]->halfday * 0.5)) + (($duration == 'half day') ? 0.5 : 1)) > $totalAllowedLeaves)) {
                    return Reply::error(__('messages.leaveLimitError'));
                }

                if ($appliedLimit > $leaveType->monthly_limit) {
                    return Reply::error(__('messages.monthlyLeaveLimitError'));
                }


            }
            else {
                $dates = explode(',', $request->multi_date);

                $multiDates = [];

                foreach ($dates as $dateData) {
                    $leaveTaken = LeaveType::byUser($request->user_id, $request->leave_type_id, array('approved', 'pending'), Carbon::parse($dateData)->format(company()->date_format));

                    /** @phpstan-ignore-next-line */
                    if (isset($leaveTaken[0]->leavesCount[0]) && (($leaveTaken[0]->leavesCount[0]->count - ($leaveTaken[0]->leavesCount[0]->halfday * 0.5)) + count($multiDates)) > $totalAllowedLeaves) {
                        return Reply::error(__('messages.leaveLimitError'));
                    }
                    elseif (count($multiDates) > $totalAllowedLeaves) {
                        return Reply::error(__('messages.leaveLimitError'));
                    }

                    array_push($multiDates, Carbon::parse($dateData)->format('Y-m-d'));
                }


                foreach ($dates as $dateData) {
                    $dateApplied = Carbon::parse($dateData);

                    /** @phpstan-ignore-next-line */
                    $currentMonthFullDay = Leave::whereBetween('leave_date', [$dateApplied->startOfMonth()->toDateString(), $dateApplied->endOfMonth()->toDateString()])
                        ->where('leave_type_id', $leaveType->id)
                        ->where('duration', '<>', 'half day')
                        ->whereIn('status', ['approved', 'pending'])
                        ->where('user_id', $request->user_id)
                        ->get()->count();

                    /** @phpstan-ignore-next-line */
                    $currentMonthHalfDay = Leave::whereBetween('leave_date', [$dateApplied->startOfMonth()->toDateString(), $dateApplied->endOfMonth()->toDateString()])
                        ->where('leave_type_id', $leaveType->id)
                        ->where('duration', 'half day')
                        ->whereIn('status', ['approved', 'pending'])
                        ->where('user_id', $request->user_id)
                        ->get()->count();

                    /** @phpstan-ignore-next-line */
                    $appliedLimit = ($currentMonthFullDay + ($currentMonthHalfDay / 2)) + count($dates);

                    if ($appliedLimit > $leaveType->monthly_limit) {
                        return Reply::error(__('messages.monthlyLeaveLimitError'));
                    }
                }

            }

        }

        if ($request->duration == 'multiple') {

            session(['leaves_duration' => 'multiple']);

            $dates = explode(',', $request->multi_date);
            $multiDates = [];

            foreach ($dates as $dateData) {
                array_push($multiDates, Carbon::parse($dateData)->format('Y-m-d'));
            }

            $leaveApplied = Leave::select(DB::raw('DATE_FORMAT(leave_date, "%Y-%m-%d") as leave_date_new'))
                ->where('user_id', $request->user_id)
                ->where('status', '!=', 'rejected')
                ->whereIn('leave_date', $multiDates)
                ->pluck('leave_date_new')
                ->toArray();

            if (!empty($leaveApplied)) {
                return Reply::error(__('messages.leaveApplyError'));
            }

            /* check leave limit for the selected leave type start */
            $holidays = Holiday::select(DB::raw('DATE_FORMAT(date, "%Y-%m-%d") as holiday_date'))
                ->whereIn('date', $multiDates)
                ->pluck('holiday_date')->toArray();

            foreach ($dates as $date) {

                $dateInsert = Carbon::parse($date);

                if (!in_array($dateInsert, $holidays)) {
                    $leaveYear = Carbon::createFromFormat('m-Y', company()->year_starts_from.'-'.$dateInsert->copy()->year)->startOfMonth();

                    if ($leaveYear->gt($dateInsert)) {
                        $leaveYear = $leaveYear->subYear();
                    }

                    $userTotalLeaves = Leave::byUserCount($request->user_id, $leaveYear->year);
                    $remainingLeave = $employeeLeaveQuota->no_of_leaves - $userTotalLeaves;

                    if(($userTotalLeaves + .5) == $employeeLeaveQuota->no_of_leaves) {
                        return Reply::error(__('messages.multipleRemainingLeaveError', ['leaves' => $remainingLeave]));
                    }

                    if ($userTotalLeaves >= $employeeLeaveQuota->no_of_leaves) {
                        return Reply::error(__('messages.leaveLimitError'));
                    }
                }
            }


            /* check leave limit for the selected leave type end */

            $leaveId = '';

            foreach ($dates as $date) {

                $dateInsert = Carbon::parse($date)->format('Y-m-d');

                if (!in_array($dateInsert, $holidays)) {
                    $leave = new Leave();
                    $leave->user_id = $request->user_id;
                    $leave->unique_id = $uniqueId;
                    $leave->leave_type_id = $request->leave_type_id;
                    $leave->duration = $request->duration;
                    $leave->leave_date = $dateInsert;
                    $leave->reason = $request->reason;
                    $leave->status = ($request->has('status') ? $request->status : 'pending');
                    $leave->save();

                    $leaveId = $leave->id;
                    session()->forget('leaves_duration');
                }
            }

            return Reply::successWithData(__('messages.leaveApplySuccess'), ['leaveID' => $leaveId, 'redirectUrl' => $redirectUrl]);
        }

        $dateInsert = Carbon::createFromFormat($this->company->date_format, $request->leave_date)->format('Y-m-d');
        $leaveApplied = Leave::where('user_id', $request->user_id)->where('status', '!=', 'rejected')->whereDate('leave_date', $dateInsert)->first();
        $holiday = Holiday::select(DB::raw('DATE_FORMAT(date, "%Y-%m-%d") as holiday_date'))->where('date', $dateInsert)->first();

        if (!empty($leaveApplied) && $leaveApplied->duration != 'half day') {
            return Reply::error(__('messages.leaveApplyError'));
        }

        if (!is_null($holiday)) {
            return Reply::error(__('messages.holidayLeaveApplyError'));
        }

        /* check leave limit for the selected leave type start */
        $leaveYear = Carbon::createFromFormat('m-Y', company()->year_starts_from.'-'.Carbon::parse($dateInsert)->year)->startOfMonth();

        if ($leaveYear->gt(Carbon::parse($dateInsert))) {
            $leaveYear = $leaveYear->subYear();
        }

        $userTotalLeaves = Leave::byUserCount($request->user_id, $leaveYear->year);
        $remainingLeave = $employeeLeaveQuota->no_of_leaves - $userTotalLeaves;

        if(($userTotalLeaves + .5) == $employeeLeaveQuota->no_of_leaves && $request->duration == 'single') {
            return Reply::error(__('messages.multipleRemainingLeaveError', ['leaves' => $remainingLeave]));
        }

        if ($userTotalLeaves >= $employeeLeaveQuota->no_of_leaves && $request->duration == 'single') {
            return Reply::error(__('messages.leaveLimitError'));
        }

        /* check leave limit for the selected leave type end */

        $duration = match ($request->duration) {
            'first_half', 'second_half' => 'half day',
            default => $request->duration,
        };

        $leave = new Leave();
        $leave->user_id = $request->user_id;
        $leave->unique_id = $uniqueId;
        $leave->leave_type_id = $request->leave_type_id;
        $leave->duration = $duration;

        if ($duration == 'half day') {
            /* check leave limit for the selected leave type start */
            $dateInsert = Carbon::createFromFormat($this->company->date_format, $request->leave_date)->format('Y-m-d');

            $userHalfDaysLeave = Leave::where([
                ['user_id', $request->user_id],
                ['leave_type_id', $request->leave_type_id],
                ['status', '!=', 'rejected'],
                ['duration', $duration],
                ['half_day_type', $request->duration]
                ])->whereDate('leave_date', $dateInsert)->first();

            if (!is_null($userHalfDaysLeave)) {
                return Reply::error(__('messages.leaveApplyError'));
            }

            if ($userTotalLeaves >= $employeeLeaveQuota->no_of_leaves) {
                return Reply::error(__('messages.leaveLimitError'));
            }

            /* check leave limit for the selected leave type end */
            $leave->half_day_type = $request->duration;
        }

        $leave->leave_date = Carbon::createFromFormat($this->company->date_format, $request->leave_date)->format('Y-m-d');
        $leave->reason = $request->reason;
        $leave->status = ($request->has('status') ? $request->status : 'pending');
        $leave->save();

        return Reply::successWithData(__('messages.leaveApplySuccess'), ['leaveID' => $leave->id, 'redirectUrl' => $redirectUrl]);
    }
    
    
    
    private function checkCasualLeaveEligibility($userId, $leaveTypeId, $leaveApplyDate, $requestedLeaveDays)
    {
        $employeeDetail = EmployeeDetails::where('user_id', $userId)->first();
    
        if (!$employeeDetail || !$employeeDetail->joining_date) {
            return ['status' => false, 'message' => 'Employee joining date is not set.'];
        }
    
        $joiningDate = Carbon::parse($employeeDetail->joining_date);
        $monthsSinceJoining = $joiningDate->diffInMonths($leaveApplyDate);
    
        // Determine monthly CL rate
        if ($monthsSinceJoining < 3) {
            return ['status' => false, 'message' => 'You are not eligible for casual leave yet. CLs are granted after 3 months from your joining date.'];
        } elseif ($monthsSinceJoining < 12) {
            $monthlyCL = 1;
        } elseif ($monthsSinceJoining < 24) {
            $monthlyCL = 1.5;
        } else {
            $monthlyCL = 2;
        }
    
        // Get company leave year start
        $leaveYearStart = Carbon::createFromFormat('m-Y', company()->year_starts_from.'-'.$leaveApplyDate->year)->startOfMonth();
        if ($leaveYearStart->gt($leaveApplyDate)) {
            $leaveYearStart->subYear();
        }
    
        $workingMonthsThisYear = $joiningDate->gt($leaveYearStart)
            ? $joiningDate->diffInMonths($leaveApplyDate) + 1
            : $leaveYearStart->diffInMonths($leaveApplyDate) + 1;
    
        $maxCLThisYear = $monthlyCL * $workingMonthsThisYear;
    
        $userCLsTaken = Leave::where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->whereBetween('leave_date', [$leaveYearStart->toDateString(), $leaveApplyDate->toDateString()])
            ->whereIn('status', ['approved', 'pending'])
            ->get()
            ->reduce(function ($carry, $leave) {
                return $carry + ($leave->duration == 'half day' ? 0.5 : 1);
            }, 0);
    
        if (($userCLsTaken + $requestedLeaveDays) > $maxCLThisYear) {
            return ['status' => false, 'message' => "You have exceeded your casual leave quota for this year based on your tenure. Allowed: {$maxCLThisYear}, Already taken: {$userCLsTaken}."];
        }
    
        return ['status' => true];
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->leave = Leave::with('approvedBy', 'user')->where(function($q) use($id){
            $q->where('id', $id);
            $q->orWhere('unique_id', $id);
        })->firstOrFail();

        $this->reportingTo = EmployeeDetails::where('reporting_to', user()->id)->first();

        $this->viewPermission = user()->permission('view_leave');
        abort_403(!($this->viewPermission == 'all'
            || ($this->viewPermission == 'added' && user()->id == $this->leave->added_by)
            || ($this->viewPermission == 'owned' && user()->id == $this->leave->user_id)
            || ($this->viewPermission == 'both' && (user()->id == $this->leave->user_id || user()->id == $this->leave->added_by)) || ($this->reportingTo)
        ));

        $this->pageTitle = $this->leave->user->name;
        $this->reportingPermission = LeaveSetting::value('manager_permission');

        if (request()->ajax()) {
            $html = view('leaves.ajax.show', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        if($this->leave->duration == 'multiple' && !is_null($this->leave->unique_id) && (request()->type != 'single' || !request()->has('type'))){
            $this->multipleLeaves = Leave::with('type', 'user')->where('unique_id', $id)->orderBy('leave_date', 'DESC')->get();
            $this->view = 'leaves.ajax.multiple-leaves';
        }
        else {
            $this->view = 'leaves.ajax.show';
        }

        return view('leaves.create', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->leave = Leave::with('files')->findOrFail($id);
        $this->editPermission = user()->permission('edit_leave');
        abort_403(!(
            ($this->editPermission == 'all'
                || ($this->editPermission == 'added' && $this->leave->added_by == user()->id)
                || ($this->editPermission == 'owned' && $this->leave->user_id == user()->id)
                || ($this->editPermission == 'both' && ($this->leave->user_id == user()->id || $this->leave->added_by == user()->id))
            )
            && ($this->leave->status == 'pending')));

        $this->employees = User::allEmployees();
        $this->leaveTypes = LeaveType::all();

        $this->pageTitle = $this->leave->user->name;

        if ($this->editPermission == 'added') {
            $this->defaultAssign = user();

        }
        else if (isset(request()->default_assign)) {
            $this->defaultAssign = User::findOrFail(request()->default_assign);
        }

        if (request()->ajax()) {
            $html = view('leaves.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'leaves.ajax.edit';

        return view('leaves.create', $this->data);
    }

    /**
     * @param UpdateLeave $request
     * @param int $id
     * @return array|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function update(UpdateLeave $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $this->editPermission = user()->permission('edit_leave');

        abort_403(!($this->editPermission == 'all'
            || ($this->editPermission == 'added' && $leave->added_by == user()->id)
            || ($this->editPermission == 'owned' && $leave->user_id == user()->id)
            || ($this->editPermission == 'both' && ($leave->user_id == user()->id || $leave->added_by == user()->id))
        ));

        /* check leave limit for the selected leave type start */
        $leaveStartYear = Carbon::parse(now()->format((now(company()->timezone)->year) . '-'. company()->year_starts_from . '-01'));

        if($leaveStartYear->isFuture()){
            $leaveStartYear = $leaveStartYear->subYear();
        }

        $userFullDayLeaves = Leave::where([
            ['user_id', $request->user_id],
            ['leave_type_id', $request->leave_type_id],
            ['status', '!=', 'rejected'],
            ['status', '!=', 'pending'],
            ['duration', '!=', 'half day']
        ])->whereBetween('leave_date', [$leaveStartYear->copy()->toDateString(), $leaveStartYear->copy()->addYear()->toDateString()])
            ->count();
        $userHalfDayLeaves = Leave::where([
            ['user_id', $request->user_id],
            ['leave_type_id', $request->leave_type_id],
            ['status', '!=', 'rejected'],
            ['status', '!=', 'pending']
        ])->whereBetween('leave_date', [$leaveStartYear->copy()->toDateString(), $leaveStartYear->copy()->addYear()->toDateString()])
            ->where('duration', 'half day')->count();

        $userTotalLeaves = $userFullDayLeaves + ($userHalfDayLeaves / 2);

        $leaveQuota = EmployeeLeaveQuota::whereUserId($request->user_id)->whereLeaveTypeId($request->leave_type_id)->first();
        $userRemainingLeaves = $leaveQuota->no_of_leaves - $userTotalLeaves;


        if ((($userTotalLeaves + .5) == $leaveQuota->no_of_leaves) && $userTotalLeaves >= $leaveQuota->no_of_leaves) {
            return Reply::error(__('messages.multipleRemainingLeaveError', ['leaves' => $userRemainingLeaves]));
        }

        if ($userTotalLeaves >= $leaveQuota->no_of_leaves) {
            return Reply::error(__('messages.leaveLimitError'));
        }

        /* check leave limit for the selected leave type end */

        $leave->user_id = $request->user_id;
        $leave->leave_type_id = $request->leave_type_id;
        $leave->leave_date = Carbon::createFromFormat($this->company->date_format, $request->leave_date)->format('Y-m-d');
        $leave->reason = $request->reason;

        if ($request->has('reject_reason')) {
            $leave->reject_reason = $request->reject_reason;
        }

        if ($request->has('status')) {
            $leave->status = $request->status;
        }

        $leave->save();

        $uniqueID = $leave->unique_id;

        if($leave->duration == 'multiple' && !is_null($uniqueID)){
            $route = route('leaves.show', $leave->unique_id).'?tab=multiple-leaves';
        }
        else{
            $route = route('leaves.index');
        }

        return Reply::successWithData(__('messages.leaveAssignSuccess'), ['leaveID' => $leave->id, 'redirectUrl' => $route]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leave = Leave::findOrFail($id);
        $uniqueID = $leave->unique_id;

        $this->deletePermission = user()->permission('delete_leave');
        $this->deleteApproveLeavePermission = user()->permission('delete_approve_leaves');

        abort_403(!($this->deletePermission == 'all'
            || ($this->deletePermission == 'added' && $leave->added_by == user()->id)
            || ($this->deletePermission == 'owned' && $leave->user_id == user()->id)
            || ($this->deletePermission == 'both' && ($leave->user_id == user()->id || $leave->added_by == user()->id) || ($this->deleteApproveLeavePermission == 'none'))
        ));

        if(!is_null(request()->uniId) && request()->duration == 'multiple')
        {
            Leave::where('unique_id', request()->uniId)->delete();
        }
        else {
            Leave::destroy($id);
        }

        $totalLeave = $leave->duration == 'multiple' && !is_null($uniqueID) ? Leave::where('unique_id', $uniqueID)->count() : 0;

        if($totalLeave == 0){
            $route = route('leaves.index');
        }
        elseif(request()->type == 'delete-single' && !is_null($uniqueID) && $leave->duration == 'multiple'){
            $route = route('leaves.show', $leave->unique_id);
        }
        else{
            $route = '';
        }

        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => $route]);
    }

    public function leaveCalendar(Request $request)
    {
        $viewPermission = user()->permission('view_leave');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->pendingLeaves = Leave::where('status', 'pending')->count();
        $this->employees = User::allEmployees();
        $this->leaveTypes = LeaveType::all();
        $this->pageTitle = 'app.menu.calendar';
        $this->reportingPermission = LeaveSetting::value('manager_permission');

        if (request('start') && request('end')) {

            $leaveArray = array();

            $leavesList = Leave::join('users', 'users.id', 'leaves.user_id')
                ->join('leave_types', 'leave_types.id', 'leaves.leave_type_id')
                ->join('employee_details', 'employee_details.user_id', 'users.id')
                ->where('users.status', 'active')
                ->select('leaves.id', 'users.name', 'leaves.leave_date', 'leaves.status', 'leave_types.type_name', 'leave_types.color', 'leaves.leave_date', 'leaves.duration', 'leaves.status');

            if (!is_null($request->startDate)) {
                $startDate = Carbon::createFromFormat($this->company->date_format, $request->startDate)->toDateString();
                $leavesList->whereRaw('Date(leaves.leave_date) >= ?', [$startDate]);
            }

            if (!is_null($request->endDate)) {
                $endDate = Carbon::createFromFormat($this->company->date_format, $request->endDate)->toDateString();

                $leavesList->whereRaw('Date(leaves.leave_date) <= ?', [$endDate]);
            }

            if ($request->leaveTypeId != 'all' && $request->leaveTypeId != '') {
                $leavesList->where('leave_types.id', $request->leaveTypeId);
            }

            if ($request->status != 'all' && $request->status != '') {
                $leavesList->where('leaves.status', $request->status);
            }

            if ($request->searchText != '') {
                $leavesList->where('users.name', 'like', '%' . $request->searchText . '%');
            }

            if ($viewPermission == 'owned') {
                $leavesList->where(function ($q) {
                    $q->orWhere('leaves.user_id', '=', user()->id);

                    ($this->reportingPermission != 'cannot-approve') ? $q->orWhere('employee_details.reporting_to', user()->id) : '';
                });
            }

            if ($viewPermission == 'added') {
                $leavesList->where(function ($q) {
                    $q->orWhere('leaves.added_by', '=', user()->id);

                    ($this->reportingPermission != 'cannot-approve') ? $q->orWhere('employee_details.reporting_to', user()->id) : '';
                });
            }

            if ($viewPermission == 'both') {
                $leavesList->where(function ($q) {
                    $q->orwhere('leaves.user_id', '=', user()->id);

                    $q->orWhere('leaves.added_by', '=', user()->id);

                    ($this->reportingPermission != 'cannot-approve') ? $q->orWhere('employee_details.reporting_to', user()->id) : '';
                });
            }

            $leaves = $leavesList->get();

            foreach ($leaves as $key => $leave) {
                /** @phpstan-ignore-next-line */
                $title = ucfirst($leave->name);

                $leaveArray[] = [
                    'id' => $leave->id,
                    'title' => $title,
                    'start' => $leave->leave_date->format('Y-m-d'),
                    'end' => $leave->leave_date->format('Y-m-d'),
                    /** @phpstan-ignore-next-line */
                    'color' => $leave->color
                ];
            }

            return $leaveArray;
        }

        return view('leaves.calendar.index', $this->data);
    }

    public function applyQuickAction(Request $request)
    {
        switch ($request->action_type) {
        case 'delete':
            $this->deleteRecords($request);

            return Reply::success(__('messages.deleteSuccess'));
        case 'change-leave-status':
            $this->changeBulkStatus($request);

            return Reply::success(__('messages.updateSuccess'));
        default:
            return Reply::error(__('messages.selectAction'));
        }
    }

    protected function deleteRecords($request)
    {
        abort_403(user()->permission('delete_leave') != 'all');
        $leaves = Leave::whereIn('id', explode(',', $request->row_ids))->get();

        foreach($leaves as $leave)
        {
            if(!is_null($leave->unique_id) && $leave->duration == 'multiple')
            {
                Leave::where('unique_id', $leave->unique_id)->delete();
            }
            else {
                Leave::destroy($leave->id);
            }
        }
    }

    protected function changeBulkStatus($request)
    {
        abort_403(user()->permission('edit_leave') != 'all');

        $leaves = Leave::whereIn('id', explode(',', $request->row_ids))->get();

        foreach($leaves as $leave)
        {
            if(!is_null($leave->unique_id) && $leave->duration == 'multiple')
            {
                Leave::where('unique_id', $leave->unique_id)->update(['status' => $request->status]);
            }
            else {
                Leave::where('id', $leave->id)->update(['status' => $request->status]);
            }
        }

    }

    public function leaveAction(ActionLeave $request)
    {
        $this->reportingTo = EmployeeDetails::where('reporting_to', user()->id)->first();

        abort_403(!($this->reportingTo) && user()->permission('approve_or_reject_leaves') == 'none');

        $leave = Leave::findOrFail($request->leaveId);
        $leave->status = $request->action;

        if (isset($request->approveReason)) {
            $leave->approve_reason = $request->approveReason;
        }

        if (isset($request->reason)) {
            $leave->reject_reason = $request->reason;
        }

        $leave->approved_by = user()->id;
        $leave->approved_at = now()->toDateTimeString();

        $leave->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    public function preApprove(Request $request)
    {
        $this->reportingTo = EmployeeDetails::where('reporting_to', user()->id)->first();

        $leave = Leave::findOrFail($request->leaveId);
        $leave->manager_status_permission = $request->action;

        $leave->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    public function approveLeave(Request $request)
    {
        $this->reportingTo = EmployeeDetails::where('reporting_to', user()->id)->first();

        abort_403(!($this->reportingTo) && (user()->permission('approve_or_reject_leaves') == 'none'));

        $this->leaveAction = $request->leave_action;
        $this->leaveID = $request->leave_id;
        return view('leaves.approve.index', $this->data);
    }

    public function rejectLeave(Request $request)
    {
        $this->reportingTo = EmployeeDetails::where('reporting_to', user()->id)->first();

        abort_403(!($this->reportingTo) && (user()->permission('approve_or_reject_leaves') == 'none'));

        $this->leaveAction = $request->leave_action;
        $this->leaveID = $request->leave_id;

        return view('leaves.reject.index', $this->data);
    }

    public function personalLeaves()
    {
        $this->pageTitle = __('modules.leaves.myLeaves');

        $this->employee = User::with(['employeeDetail', 'employeeDetail.designation', 'employeeDetail.department', 'leaveTypes', 'leaveTypes.leaveType', 'country', 'employee'])
            ->withoutGlobalScope(ActiveScope::class)
            ->withCount('member', 'agents', 'tasks')
            ->findOrFail(user()->id);

        $this->leaveTypes = LeaveType::byUser(user()->id);
        $this->leavesTakenByUser = Leave::byUserCount(user()->id);
        $this->allowedLeaves = $this->employee->leaveTypes->sum('no_of_leaves');
        $this->employeeLeavesQuota = $this->employee->leaveTypes;
        $this->employeeLeavesQuotas = $this->employee->leaveTypes;
        $this->view = 'leaves.ajax.personal';

        return view('leaves.create', $this->data);
    }

    public function getDate(Request $request)
    {
        if ($request->date != null) {
            $date = Carbon::createFromFormat($this->company->date_format, $request->date)->toDateString();
            $users = Leave::where('leave_date', $date)->where('status', 'approved')->count();
        }
        else{
            $users = '';
        }

        return Reply::dataOnly(['status' => 'success', 'users' => $users]);
    }

    public function viewRelatedLeave(Request $request)
    {
        $this->multipleLeaves = Leave::with('type', 'user')->where('unique_id', $request->uniqueId)->orderBy('leave_date', 'DESC')->get();

        return view('leaves.view-multiple-related-leave', $this->data);
    }
    
    // manager leave manage 
    public function head_leave_manage($team_id){
        
        $this->leave = DB::table('leaves')
                        ->join('employee_details', 'leaves.user_id', '=', 'employee_details.user_id')
                        ->join('users', 'users.id', '=', 'employee_details.user_id')
                        ->where('employee_details.department_id', $team_id)
                        ->select('leaves.*','employee_details.*', 'users.name as user_name','leaves.id as leave_id')
                        ->latest('leaves.id')
                        ->get();
                        // return $this->leave;
        return view('leave_manager',$this->data);
    }
    
    public function manger_approve_leave($id){
        $this->update = DB::table('leaves')
                        ->where('id', $id)
                        ->update([
                            'manager_status' => 0,
                            'manager_status_changed_by' => user()->id
                            ]);
        return redirect()->back()->with('success','Leave Approved Successfully');
    }
    
    public function manger_reject_leave($id){
        $this->update = DB::table('leaves')
                        ->where('id', $id)
                        ->update([
                            'manager_status' => 2,
                            'manager_status_changed_by' => user()->id
                            ]);
        return redirect()->back()->with('success','Leave Rejected!');
    }
    
    public function kolkatta_team_leave_manage()
{
    $user = user(); // current logged-in user
    $employee = $user->employee->first();
    // return $employee;
    // Ensure employee details exist
    if (!in_array(auth()->user()->username, ['NIF0725407', 'NIF0525003', 'NIF0725347','NIFALPHA'])) {
        abort(403, 'Employee details not found.');
    }

    // Only allow branch_id = 7
    if ($employee->branch_id != 7 && user()->username != 'NIFALPHA') {
        abort(403, 'Access denied: You are not part of branch 7.');
    }

    // Mapping employee_id to allowed team_ids
    $accessTeams = [];

    switch ($employee->employee_id) {
        case 'NIF0725407':
            $accessTeams = [27];
            break;

        case 'NIF0525003':
            $accessTeams = [29, 31];
            break;
        case 'NIF0525370':
            $accessTeams = [43, 42,50];
            break;

        case 'NIF0725347':
            // all teams access — we’ll fetch dynamically below
            $accessTeams = 'all';
            break;
        case 'NIFALPHA':
            // all teams access — we’ll fetch dynamically below
            $accessTeams = 'all';
            break;

        default:
            abort(403, 'You do not have team leave access.');
    }

    // Build the query
    $query = DB::table('leaves')
        ->join('employee_details', 'leaves.user_id', '=', 'employee_details.user_id')
        ->join('users', 'users.id', '=', 'employee_details.user_id')
        ->where('employee_details.branch_id', 7)
        ->select('leaves.*', 'employee_details.*', 'users.name as user_name', 'leaves.id as leave_id')
        ->latest('leaves.id');

    // Filter by teams if not "all"
    if ($accessTeams !== 'all') {
        $query->whereIn('employee_details.department_id', $accessTeams);
    }

    $this->leave = $query->get();

    return view('leaves.kolkatta_manager', $this->data);
}

    
    

}
