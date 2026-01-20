<?php

namespace App\Http\Controllers;

use Artisan;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Helper\Files;
use App\Helper\Reply;
use App\Models\Leave;
use App\Models\Skill;
use App\Models\Module;
use App\Models\Ticket;
use App\Models\Country;
use App\Models\Passport;
use App\Models\RoleUser;
use App\Models\LeaveType;
use App\Models\Attendance;
use App\Models\Designation;
use App\Models\Branch;
use App\Scopes\ActiveScope;
use App\Traits\ImportExcel;
use App\Models\Appreciation;
use App\Models\Notification;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use App\Models\EmployeeSkill;
use App\Models\ProjectTimeLog;
use App\Models\UserInvitation;
use App\Imports\EmployeeImport;
use App\Jobs\ImportEmployeeJob;
use App\Jobs\ImportAttJob;
use App\Models\EmployeeDetails;
use App\Models\LanguageSetting;
use App\Models\TaskboardColumn;
use App\Models\UniversalSearch;
use App\DataTables\LeaveDataTable;
use App\DataTables\TasksDataTable;
use Illuminate\Support\Facades\DB;
use App\DataTables\TicketDataTable;
use App\Models\ProjectTimeLogBreak;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Facades\Excel;
use App\DataTables\ProjectsDataTable;
use App\DataTables\TimeLogsDataTable;
use App\DataTables\EmployeesDataTable;
use Maatwebsite\Excel\HeadingRowImport;
use App\Http\Requests\User\InviteEmailRequest;
use App\Http\Requests\Admin\Employee\StoreRequest;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use App\Http\Requests\Admin\Employee\ImportRequest;
use App\Http\Requests\Admin\Employee\UpdateRequest;
use App\Http\Requests\User\CreateInviteLinkRequest;
use App\Http\Requests\Admin\Employee\ImportProcessRequest;
use App\Models\VisaDetail;
use App\Exports\FilteredEmployeesExport;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Event;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Validator;


class SalaryController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Salary';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('employees', $this->user->modules));
            return $next($request);
        });
        
        
        
        
    }
    
    
    /**
     * @param EmployeesDataTable $dataTable
     * @return mixed|void
     */
     

    
    
    
    public function main()
    {
        $todayDate = Carbon::now()->subDay(25);
        $month = $todayDate->format('m');
        $year = $todayDate->format('Y');
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year); 
        
        $calculated = [];
        $all_emp = DB::table('employee_details')
            ->join('users', 'users.id', 'employee_details.user_id')
            ->join('teams', 'teams.id', 'employee_details.department_id')
            ->where('users.status', 'active')
            ->where('employee_details.branch_id', 1)
            ->orderBy('employee_details.department_id')
            // ->where('users.id' , 235)
            ->get();
    
        foreach ($all_emp as $employee) {
            $salary = $this->calculateSalary($employee, $month, $year);
            if($salary != 36){
                
                $e['emp'] = $employee->name;
                $e['emp_id'] = $employee->employee_id;
                $e['salary'] = $salary;
                $e['department'] = $employee->team_name;
                $e['total_hour'] = $daysInMonth * ($employee->employment_type === 'part_time' ? 5 : 9);
                
                $calculated[] = $e;
            }
            // store salary 
            // $this->insertSalaryData($employee->user_id, $month, $year, $salary);
            // return $salary;
        }
        // echo '<pre>';
        // print_r($data);
        $this->all = $calculated;
        return view('salary_page',$this->data);
    }
    
       
    
    
    
    
    function calculateSalary($employee, $month, $year)
    {
        $user_id = $employee->user_id;
    
        $attendances = DB::table('attendances')
            ->where('user_id', $user_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();
    
        $leaves = DB::table('leaves')
            ->where('user_id', $user_id)
            ->where('status', 'approved')
            ->where('manager_status', '0')
            ->whereYear('leave_date', $year)
            ->whereMonth('leave_date', $month)
            ->get();
    
        $holidays = DB::table('holidays')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();
    
        $totalWorkingHours = 0;
    
        // Calculate working hours based on attendance records
        foreach ($attendances as $attendance) {
            // $loginTime = Carbon::parse($attendance->clock_in_time);
            // $logoutTime = Carbon::parse($attendance->clock_out_time);
            $workingHours = $this->calculateWorkingHours($attendance->clock_in_time, $attendance->clock_out_time, $employee->employment_type);
            $totalWorkingHours += $workingHours;
        }
    
        // calculation for leave and holidays added into salary
        $deduction = $this->calculateHoursLeave($leaves, $holidays,$employee->employment_type);
        $totalWorkingHours += $deduction;
        
        
    
        // Add Sunday hours
        $sundays_hour = $this->countSundaysInMonth($year, $month, $employee->employment_type);
        $totalWorkingHours += $sundays_hour;
        
        // Add saturday hours
        if($employee->department_id == 6){
            $saturday_hour = $this->countSaturdaysInMonth($year, $month, $employee->employment_type);
            $totalWorkingHours += $saturday_hour;
        }
    
        return $totalWorkingHours;
    }
    
    
    
    
    
    
    
    
    function calculateWorkingHours($loginTime, $logoutTime, $employmentType)
    {
        // If login or logout time is not provided, return default values
        if ($loginTime === null || $logoutTime === null) {
            return ($employmentType === 'part_time') ? 2.5 : 4.5;
        }
    
        // Ensure login and logout times are Carbon instances
        $loginTime = Carbon::parse($loginTime);
        $logoutTime = Carbon::parse($logoutTime);
    
        // Calculate actual working hours based on the difference between login and logout times
         $interval = $logoutTime->diff($loginTime);
        $workingHours = $interval->h + ($interval->i / 60); // Total hours including fractional hours

        // Cap the working hours based on employment type
        if ($employmentType === 'part_time') {
            $workingHours = min($workingHours, 5.0);
        } else {
            $workingHours = min($workingHours, 9.0);
        }
    
        return $workingHours;
    }

    
    
    
    

    
    function calculateHoursLeave($leaves, $holidays,$employmentType)
    {
        $hours = 0;
        
        foreach ($leaves as $day) {
            // Add working hours for each day of leave
            $workingHours = ($employmentType === 'part_time') ? 5 : 9;
            $hours += $workingHours;
        }
        
         // Add additional working hours for holidays 
        foreach ($holidays as $holiday) {
            $holidayDate = Carbon::parse($holiday->date);

            
                // Add working hours for the holiday
                $holidayWorkingHours = ($employmentType === 'part_time') ? 5 : 9;
                $hours += $holidayWorkingHours;
            
        }
        return $hours;
    }
    
    function countSundaysInMonth($year, $month, $employmentType)
    {
        $firstDayOfMonth = Carbon::create($year, $month, 1);
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
    
        $sundays = 0;
    
        while ($firstDayOfMonth->lte($lastDayOfMonth)) {
            if ($firstDayOfMonth->isSunday()) {
                $sundays++;
            }
    
            $firstDayOfMonth->addDay();
        }
    
        if ($employmentType === 'part_time') {
            $hours = $sundays * 5;
        } else {
            $hours = $sundays * 9;
        }
    
        return $hours;
    }
    
    // saturday in month 
    
   function countSaturdaysInMonth($year, $month, $employmentType)
    {
        $firstDayOfMonth = Carbon::create($year, $month, 1);
        $lastDayOfMonth = $firstDayOfMonth->copy()->endOfMonth();
    
        $saturdays = 0;
    
        while ($firstDayOfMonth->lte($lastDayOfMonth)) {
            if ($firstDayOfMonth->isSaturday()) {
                $saturdays++;
            }
    
            $firstDayOfMonth->addDay();
        }
    
        if ($employmentType === 'part_time') {
            $hours = $saturdays * 5;
        } else {
            $hours = $saturdays * 9;
        }
    
        return $hours;
    }

    

    
    function insertSalaryData($user_id, $month, $year, $totalWorkingHours)
    {
        // You need to adjust this based on your actual table structure and fields
        DB::table('custom_salary')->insert([
            'user_id' => $user_id,
            'month' => $month,
            'year' => $year,
            'total_working_hours' => $totalWorkingHours,
            'created_at' => now(),
            'updated_at' => now(),
            // Add other fields as needed
        ]);
    
        // You may want to handle any additional logic or validations after insertion
    }
    
    // satr
    
    
    
    
    
    // salary slip 

    public function salary_detail(){
        $array = ['NIF1222001','NIF0420001','NIFALPHA'];
        
        if (in_array(user()->username, $array)) {
            
            
            
            return view('salary_detail',$this->data);
            
        } else {
           return 'You Do not Have Permission To Access This Page';
        }
    }
    
    public function salary_slip(){
        return view('salary_detail',$this->data);
    }
    
    public function make_salary_slip(Request $request){
        
        
         // Get the input values from the request
        $ctc = $request->input('ctc');
        $daysInMonth = Carbon::now()->month($request->month)->daysInMonth;
        $name = $request->input('name');
        $bank = $request->input('bank');
        $account = $request->input('accn');
        $id = $request->input('id');
        $month = $request->input('month');
        $year = $request->input('year');
        $day_worked = $request->input('day_worked');
        $absent = $daysInMonth - $day_worked;
        
        $monthName = Carbon::createFromFormat('m', $month)->format('F');
        // $accn = $request->input('accn');
        // $accn = $request->input('accn');
        // $accn = $request->input('accn');
        
        
        $this->user = DB::table('users')
                        ->where('users.username',$id)
                        ->join('employee_details','employee_details.user_id','users.id')
                        ->join('designations','designations.id','employee_details.designation_id')
                        ->join('teams','teams.id','employee_details.department_id')
                        ->select('users.*','users.name as UserName','employee_details.*','designations.*','teams.*','designations.name as designations_name')
                        ->first();
        
        if(!$this->user){
            return 'Incorrect EmployeeID!';
        }
        // Calculate basic annual salary (BAS)
        $bas = $ctc * 0.4;

        // Calculate monthly salary
        $monthly = $bas / 12;
        
        $leave_deduct = (($ctc/12)/$daysInMonth) *$absent;

        // Calculate HRA
        $hra = $bas * 0.4;

        // Convenience allowance
        $convenience = 1600 * 12;

        // Voice Skill Allowance (VSA)
        $vsa = $ctc * 0.2;

        // Employee PF
        $employeePF = $bas * 0.125;

        // Employee ESIC
        $employeeESIC = ($ctc / 12) < 21000 ? $ctc * 0.0075 : 0;

        // Employer PF
        $employerPF = $bas * 0.125;

        // Employer ESIC
        $employerESIC = ($ctc / 12) < 21000 ? $ctc * 0.0325 : 0;

        // Gross Salary
        $grossSalary = $ctc - ($employeePF + $employeeESIC);

        // Special Allowance
        $specialAllowance = $grossSalary - ($bas + $hra + $convenience + $vsa);
        
        $total_deduction = ($employeePF/12) + ($employeeESIC/12) + $leave_deduct;
        $net_tak_home = $grossSalary/12 - $total_deduction;
        $net_tak_home_word = $this->convertToWords($net_tak_home);
        // return $net_tak_home;

        // Return the calculated values for both annual and monthly
        $this->calc = [
            'annual' => [
                'ctc' => number_format($ctc,2),
                'bas' => $bas,
                'hra' => $hra,
                'convenience' => $convenience,
                'vsa' => $vsa,
                'employeePF' => $employeePF,
                'employeeESIC' => $employeeESIC,
                'employerPF' => $employerPF,
                'employerESIC' => $employerESIC,
                'grossSalary' => $grossSalary,
                'netTakehome' => $net_tak_home,
                'specialAllowance' => $specialAllowance,
                
            ],
            'monthly' => [
                'ctc' => number_format($ctc/12 ,2),
                'bas' => number_format($monthly, 2),
                'hra' => number_format($hra / 12, 2),
                'convenience' => number_format($convenience / 12, 2),
                'vsa' => number_format($vsa / 12, 2),
                'employeePF' => number_format($employeePF / 12, 2),
                'employeeESIC' => number_format(($ctc / 12) < 21000 ? $employeeESIC/12 : 0, 2),
                'employerPF' => number_format($employerPF / 12, 2),
                'employerESIC' => number_format($employerESIC / 12, 2),
                'grossSalary' => number_format($grossSalary / 12, 2),
                'netTakehome' => number_format($net_tak_home, 2),
                'specialAllowance' => number_format($specialAllowance / 12, 2),
                'days_in_month' => $daysInMonth,
                'day_worked' => $day_worked,
                'month_name' => $monthName,
                'year' => $year,
                'bank'=>$bank,
                'account' => $account,
                'total_deduction' => number_format($total_deduction,2),
                'leave_deduct' => number_format($leave_deduct,2),
                'net_tak_home_word' => $net_tak_home_word,
                
            ],
            'actual' => [
                'bas' => number_format($monthly, 2),
                'hra' => number_format((($hra / 12) / $daysInMonth) * $day_worked, 2),
                'convenience' => number_format((($convenience / 12) / $daysInMonth) * $day_worked, 2),
                'vsa' => number_format((($vsa / 12) / $daysInMonth) * $day_worked, 2),
                'employeePF' => number_format($employeePF / 12, 2),
                'employeeESIC' => number_format($employeeESIC / 12, 2),
                'employerPF' => number_format($employerPF / 12, 2),
                'employerESIC' => number_format($employerESIC / 12, 2),
                'grossSalary' => number_format($grossSalary / 12, 2),
                'netTakehome' => number_format((($net_tak_home / 12) / $daysInMonth) * $day_worked, 2),
                'specialAllowance' => number_format((($specialAllowance / 12) / $daysInMonth) * $day_worked, 2),
                
            ],
        ];
        // return $this->calc;
        return view('salary_slip',$this->data);

    }
    
    
    
    
     public function convertToWords($number)
{
    $words = '';

    $ones = array(
        0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four',
        5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine'
    );

    $teens = array(
        10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen',
        14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen',
        18 => 'eighteen', 19 => 'nineteen'
    );

    $tens = array(
        20 => 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty',
        60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
    );

    $suffixes = array(
        100 => 'hundred', 1000 => 'thousand', 1000000 => 'million',
        1000000000 => 'billion', 1000000000000 => 'trillion'
    );

    if ($number < 10) {
        $words = ucfirst($ones[$number]);
    } elseif ($number < 20) {
        $words = ucfirst($teens[$number]);
    } elseif ($number < 100) {
        $tenDigit = (int) ($number / 10) * 10;
        $oneDigit = $number % 10;
        $words = ucfirst($tens[$tenDigit]);
        if ($oneDigit > 0) {
            $words .= '-' . ucfirst($ones[$oneDigit]);
        }
    } else {
        foreach (array_reverse($suffixes, true) as $num => $suffix) {
            if ($number >= $num) {
                $divider = (int) ($number / $num);
                $words .= $this->convertToWords($divider) . ' ' . ucfirst($suffix);
                $number %= $num;
                if ($number > 0) {
                    $words .= ' ';
                }
            }
        }
        if ($number > 0) {
            if (!empty($words)) {
                $words .= ' and ';
            }
            $words .= $this->convertToWords($number);
        }
    }

    return $words;
}










    // salary automated functions 
    
    public function create_slip(){
        
        // abort_403(!($addPermission == 'all' || $addPermission == 'added'));
        
        $this->employees = User::allEmployees(null, true, ('all'));
        $this->departments = Team::allDepartments();
        $this->pageTitle = __('Salary Slip');
        $this->year = now()->format('Y');
        $this->month = now()->format('m');
        // $this->location = CompanyAddress::all();

        if (request()->ajax()) {
            $html = view('attendances.ajax.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'attendances.ajax.create';

        return view('salary.create_slip', $this->data);
    }
    
    public function generate(Request $request)
    {
        
         $validator = Validator::make($request->all(), [
            'month' => 'required',
            'year' => 'required',
            'user_id' => 'required',
            'mark_attendance_by' => 'required',
            // 'custom_working_days' => 'required',
            // 'week_off' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Retrieve input data from the request
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');
        $employeeIds = $request->input('user_id');
        $markAttendanceBy = $request->input('mark_attendance_by');
        $customWorkingDays = $request->input('custom_working_days');
        $week_off = $request->input('week_off');
        $daysInMonth = Carbon::now()->month($request->month)->daysInMonth;
        $pf_deduction = $request->input('pf_deduction');
        
        // Fetch all attendance data for the selected month and year
        $attendanceData = DB::table('attendances')
            ->whereIn('user_id', $employeeIds)
            ->whereMonth('clock_in_time', $selectedMonth)
            ->whereYear('clock_in_time', $selectedYear)
            ->get();
            
        // Fetch all necessary user details for the selected employees
        $users = DB::table('employee_details')
            ->whereIn('user_id', $employeeIds)
            ->join('users', 'employee_details.user_id', 'users.id')
            ->join('designations', 'designations.id', 'employee_details.designation_id')
            ->join('teams', 'teams.id', 'employee_details.department_id')
            ->select('users.*', 'users.name as UserName', 'employee_details.*', 'designations.*', 'teams.*', 'designations.name as designations_name')
            ->get()
            ->keyBy('user_id');
    
        // Perform calculations for each employee
        $salarySlipData = [];
        foreach ($employeeIds as $employeeId) {
            
             if (!isset($users[$employeeId])) {
                // Handle the case where user details are not found
                // For example, log an error message or skip processing for this user
                continue;
            }


            // Filter attendance data for the current employee
            $employeeAttendanceData = $attendanceData->where('user_id', $employeeId);
    
            // return $employeeAttendanceData;
            
            $user = $users[$employeeId];
            
            // Calculate salary based on the selected method (attendance or custom working days)
            $day_wrkd = ($markAttendanceBy === 'machine') ? $this->calculateSalaryByAttendance($employeeAttendanceData,$week_off,$daysInMonth) : $customWorkingDays;
    
            
            // Prepare data for salary slip
            $salarySlipData[] = $this->makeSalarySlip($request, $user, $day_wrkd, $daysInMonth,$selectedMonth , $selectedYear,$pf_deduction);
        }
    
        // return $salarySlipData;
        
        DB::transaction(function () use ($salarySlipData) {
            foreach ($salarySlipData as $data) {
                DB::table('salary_slip')
                    ->updateOrInsert(
                        [
                            'user_id' => $data['user_id'],
                            'month' => $data['month'],
                            'year' => $data['year'],
                        ],
                        $data
                    );
            }
        });
    
        // Return success response or any other necessary response
        return response()->json(['message' => 'Salary slips generated successfully']);
    }
    
    // Function to calculate salary based on attendance
    private function calculateSalaryByAttendance($attendanceData,$week_off,$daysInMonth)
    {
        // Calculate salary based on attendance count
        $attendanceCount = $attendanceData->count();
        $attendanceCount += $week_off;
        if($attendanceCount > $daysInMonth){
            return $daysInMonth;
        }
        return $attendanceCount;
    }
    
    
    // Function to prepare salary slip data
    private function makeSalarySlip(Request $request, $user, $day_wrkd, $daysInMonth,$selectedMonth,$selectedYear,$pf_deduction)
    {
        // Get the input values from the request
        $ctc = ($user->salary) ? $user->salary  : 240000;
        $day_worked = $day_wrkd;
        
        $per_day = ($ctc/12)/$daysInMonth;
        $payable = $per_day *$day_worked; 
        
        $absent = $daysInMonth - $day_worked;
        $monthName = Carbon::createFromFormat('m', $selectedMonth)->format('F');
        
        // Calculate basic annual salary (BAS)
        $bas = $payable * 0.4;
        
        // Calculate HRA
        $hra = $bas * 0.4;
        
        // Convenience allowance
        $convenience = 1600;
        
        // Voice Skill Allowance (VSA)
        $vsa = $payable * 0.2;
        
        // Defaulting PF and ESIC deductions to 0
        $employeePF = 0;
        $employeeESIC = 0;
        $employerPF = 0;
        $employerESIC = 0;
        
        // Check if user has selected to skip PF deductions
        if($pf_deduction == 'no') {
            // If user selects no PF deduction, then set PF deductions to zero
            $employeePF = 0;
            $employeeESIC = 0;
            $employerPF = 0;
            $employerESIC = 0;
            $pf_ded = 1;
        } else {
            // Calculate PF and ESIC deductions based on conditions
            $employeePF = $bas * 0.125;
            $employeeESIC = ($ctc / 12) < 21000 ? $payable * 0.0075 : 0;
            $employerPF = $bas * 0.125;
            $employerESIC = ($ctc / 12) < 21000 ? $payable * 0.0325 : 0;
            $pf_ded = 0;
        }
        
        // Gross Salary
        $grossSalary = $payable - ($employerPF + $employerESIC);
        
        // Special Allowance
        $specialAllowance = $grossSalary - ($bas + $hra + $convenience + $vsa);
        
        if($absent >= 1){
            $leave_deduct = $per_day * $absent;
        } else {
            $leave_deduct = 0;
        }
        
        $total_deduction = ($employeePF) + ($employeeESIC) ;
        $net_tak_home = $grossSalary - $total_deduction;
        if($net_tak_home < 0){
            $net_tak_home = 0;
        }
        $net_tak_home_word = $this->convertToWords($net_tak_home);
        
        // Return the calculated values for both annual and monthly
        return [
            'user_id' => $user->user_id,
            'month' => $selectedMonth,
            'year' => $selectedYear,
            'ctc_year' => $ctc,
            'ctc_month' => $ctc / 12,
            'bas_month' => $bas ,
            'hra_month' => $hra ,
            'convenience' => $convenience,
            'vsa' => $vsa,
            'employeePF' => $employeePF ,
            'employeeESIC' => $employeeESIC,
            'employerPF' => $employerPF ,
            'employerESIC' => $employerESIC ,
            'grossSalary' => $grossSalary ,
            'netTakehome' => $net_tak_home,
            'specialAllowance' => $specialAllowance,
            'days_in_month' => $daysInMonth,
            'payable_days' => $day_worked,
            'month_name' => $monthName,
            'year' => $selectedYear,
            'bank' => 'SBI',
            'account' => '************3946',
            'total_deduction' => $total_deduction,
            'leave_deduct' => $leave_deduct,
            'net_tak_home_word' => $net_tak_home_word,
            'pf_deduct' => $pf_ded,
        ];

    }
    
    
    
    
    
    
    
    
    
    
    // show page 
    
    
    public function show_slip(){
        
        $this->year = now()->format('Y');
        $this->month = now()->format('m');
        
        $query = DB::table('salary_slip')
                        ->where('salary_slip.trash_status',0)
                        ->join('users','users.id','salary_slip.user_id')
                        ->join('employee_details','employee_details.user_id','salary_slip.user_id')
                        ->join('designations','designations.id','employee_details.designation_id')
                        ->join('teams','employee_details.department_id','teams.id')
                        ->select(
                            'users.*', 'employee_details.*', 'designations.*','salary_slip.*',
                            'users.name as employee_name',
                            'users.id as user_iddd',
                            'users.status as status',
                            'salary_slip.id as slip_id',
                            'designations.name as designation_name',
                            'teams.team_name as deapartment'
                            )
                            ->latest('salary_slip.id');
                            
            if(isset($_GET['branch'])){
                if($_GET['branch'] != 'all'){
                    $b_id = (int) $_GET['branch'];
                $query->where('employee_details.branch_id',$b_id);
                }
                
            }
            if(isset($_GET['pf_deduction'])){
                if($_GET['pf_deduction'] != 'all'){
                    $b_id = (int) $_GET['pf_deduction'];
                $query->where('salary_slip.pf_deduct',$b_id);
                }
                
            }
            
            if(isset($_GET['month'])){
                if($_GET['month'] != 'all'){
                    $b_id = (int) $_GET['month'];
                $query->where('salary_slip.month',$b_id);
                }
                
            }
            
            if(isset($_GET['year'])){
                if($_GET['year'] != 'all'){
                    $b_id = (int) $_GET['year'];
                $query->where('salary_slip.year',$b_id);
                }
                
            }
            
            if(isset($_GET['status'])){
                if($_GET['status'] == 'active'){
                    $query->where('users.status','active');
                }elseif($_GET['status'] == 'deactive'){
                    $query->where('users.status','deactive');
                }else{
                    
                }
                
            } else {
                $query->where('users.status','active');
            }
            // if(isset($_GET['status'])){
            //     if($_GET['status'] != 'all'){
            //         $s_id = $_GET['status'];
            //         $query->where('users.login', '=', "disable");                }
                
            // }
            if(isset($_GET['department'])){
                if($_GET['department'] != 'all'){
                    $d_id = (int) $_GET['department'];
                $query->whereRaw('ABS(department_id) = ?', [$d_id]);
                }
                
            }
            
            if(isset($_GET['designation'])){
                if($_GET['designation'] != 'all'){
                    $ds_id = (int)$_GET['designation'];
                $query->whereRaw('ABS(designation_id) = ?', [$ds_id]);
                }
                
            }
            
            if (isset($_GET['search'])) {
                $name = $_GET['search'];
                $query->where(function ($query) use ($name) {
                    $query->where('users.name', 'LIKE', '%' . $name . '%')
                        ->orWhere('users.username', 'LIKE', '%' . $name . '%');
                });
            }  
            
            
        $perPage = 30; // Number of employees to display per page
        $this->slips = $query->paginate($perPage);
            
        // return $this->slips;
        $this->departments = Team::all();
        $this->designations = Designation::allDesignations();
        $this->branches = Branch::allBranches();
        
        return view('salary.show',$this->data);
    }
    
    public function view_slip($id){
        $this->user = DB::table('salary_slip')
                        ->where('salary_slip.id',$id)
                        ->join('users','users.id','salary_slip.user_id')
                        ->join('employee_details','employee_details.user_id','salary_slip.user_id')
                        ->join('designations','designations.id','employee_details.designation_id')
                        ->join('teams','employee_details.department_id','teams.id')
                        ->select(
                            'users.*', 'employee_details.*', 'designations.*','salary_slip.*',
                            'users.name as employee_name',
                            'users.id as user_iddd',
                            
                            'users.status as status',
                            'designations.name as designation_name',
                            'teams.team_name as deapartment'
                            )
                            ->latest('salary_slip.id')
                        ->first();
        if(!$this->user){
            return redirect('/');
        }
        // return $this->user;
        return view('salary.print_page',$this->data);
    }
    
    
    public function upload_salary_sheet(){
        return view('salary.upload_salary_data',$this->data);
    }
    
    public function upload_salary_sheet_store(Request $request){
        
        // return 67;
         // Validate the file
        // $request->validate([
        //     'file' => 'required|mimes:csv,xlsx',
        // ]);
        
        // Get the file
        $file = $request->file('file');
        $hasHeading = $request->has('hasHeading'); 
        
        // Read the file data
        $data = Excel::toArray([], $file);
        
        // Process the data
        $dataset = [];
        $startIndex = $hasHeading ? 1 : 0;
        foreach (array_slice($data[0], $startIndex) as $row) {
            $employee_id = $row[0];
            $ctc_year = $row[1];
            $bank_name = $row[2];
            $account_number = $row[3];
            
            // Add data to array
            $dataset[$employee_id] = [
                'salary' => $ctc_year,
                'bank' => $bank_name,
                'account' => $account_number,
            ];
        }
        // return $dataset;
        // Perform bulk update
        foreach ($dataset as $employee_id => $update) {
            DB::table('employee_details')
                ->where('employee_id', $employee_id)
                ->update($update);
        }
        
        // Return success response
        // return response()->json(['message' => 'Data uploaded successfully']);

    
    }
    
    public function new_page(){
        return view('salary.new_page');
    }
    
    
    
    
    // app/Http/Controllers/SalarySlipController.php

    public function edit_slip($id)
    {
        $this->slip = DB::table('salary_slip')
                    ->where('salary_slip.id', $id)
                    ->join('users', 'users.id', 'salary_slip.user_id')
                    ->leftJoin('employee_details', 'employee_details.user_id', 'salary_slip.user_id')
                    ->leftJoin('designations', 'designations.id', 'employee_details.designation_id')
                    ->leftJoin('teams', 'employee_details.department_id', 'teams.id')
                    ->select(
                        'salary_slip.*', 
                        'users.name as employee_name',
                        'designations.name as designation_name',
                        'teams.team_name as department_name'
                    )
                    ->first();
    
        if (!$this->slip) {
            return redirect()->back()->with('error', 'Slip not found!');
        }
    
        return view('salary.edit_page', $this->data);
    }
    
    public function update_slip(Request $request, $id)
    {
        $updateData = $request->except('_token');
    
        // Convert Net Take Home to Words
        if ($request->has('netTakehome') && is_numeric($request->netTakehome)) {
            $updateData['net_tak_home_word'] = $this->convertToWords($request->netTakehome);
        }
    
        // Convert Final Pay to Words
        if ($request->has('final_pay') && is_numeric($request->final_pay)) {
            $updateData['final_pay_word'] = $this->convertToWords($request->final_pay);
        }
    
        $updateData['last_edited_by'] = auth()->user()->id;

        DB::table('salary_slip')
            ->where('id', $id)
            ->update($updateData);
    
        return redirect()->route('salarySlip.edit', $id)->with('success', 'Salary slip updated successfully!');
    }


 




    






}