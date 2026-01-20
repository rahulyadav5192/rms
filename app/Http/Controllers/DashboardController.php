<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\AttendanceSetting;
use App\Models\Attendance;
use App\Models\DashboardWidget;
use App\Models\EmployeeDetails;
use App\Models\Event;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\ProjectTimeLog;
use App\Models\ProjectTimeLogBreak;
use App\Models\NonCsaAttendance;
use App\Models\Task;
use App\Models\AgentAttendance;
use App\Models\TaskboardColumn;
use App\Models\Ticket;
use App\Traits\ClientDashboard;
use App\Traits\ClientPanelDashboard;
use App\Traits\CurrencyExchange;
use App\Traits\EmployeeDashboard;
use App\Traits\FinanceDashboard;
use App\Traits\HRDashboard;
use App\Traits\OverviewDashboard;
use App\Traits\ProjectDashboard;
use App\Traits\TicketDashboard;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nwidart\Modules\Facades\Module;

class DashboardController extends AccountBaseController
{

    use AppBoot, CurrencyExchange, OverviewDashboard, EmployeeDashboard, ProjectDashboard, ClientDashboard, HRDashboard, TicketDashboard, FinanceDashboard, ClientPanelDashboard;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.dashboard';
        $this->middleware(function ($request, $next) {
            $this->viewOverviewDashboard = user()->permission('view_overview_dashboard');
            $this->viewProjectDashboard = user()->permission('view_project_dashboard');
            $this->viewClientDashboard = user()->permission('view_client_dashboard');
            $this->viewHRDashboard = user()->permission('view_hr_dashboard');
            $this->viewTicketDashboard = user()->permission('view_ticket_dashboard');
            $this->viewFinanceDashboard = user()->permission('view_finance_dashboard');

            return $next($request);
        });

    }

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|mixed|void
     */
    public function index()
    {
        $this->isCheckScript();
    
        $user = auth()->user();
        $userId = $user->id;
    
        // Bypass for admin or specific user
        if (!in_array('admin', user_roles()) && $userId != 692) {
    
    
            // 4️⃣ Check if joining form is filled
            if ($user->form_filled == '1') {
                session()->put('show_confetti', true);
                return redirect('joining_form')->with('Please Fill Joining Form To Go Further!');
            }
            
            // 1️⃣ Get all active policies
            $totalPolicies = DB::table('policies')
                ->where('trash_status', 0)
                ->pluck('id')
                ->toArray();
    
            // 2️⃣ Get all policies accepted by the user
            $acceptedPolicies = DB::table('pilicy_accept')
                ->where('user_id', $userId)
                ->pluck('policy_id')
                ->toArray();
    
            // 3️⃣ If user has not accepted all policies → redirect
            $remainingPolicies = array_diff($totalPolicies, $acceptedPolicies);
            if (count($remainingPolicies) > 0) {
                return redirect('police_accept?mess=Please Accept All Policies First');
            }
    
            
        }
    
        // 5️⃣ Fetch uploaded documents
        $documents = DB::table('employee_docs')
            ->where('user_id', $userId)
            ->pluck('name')
            ->toArray();
    
        // 6️⃣ Determine required docs based on offer letter
        $offer = DB::table('employee_details')
            ->where('user_id', $userId)
            ->select('offer_salary_month')
            ->first();
    
        $requiredDocs = [
            'Adhar Card',
            'Pan Card',
            '12th Marksheet',
            '10th Marksheet',
            'Passport Size Photo',
            'Acknowledgment Letter'
        ];
    
        if (!empty($offer) && $offer->offer_salary_month != '') {
            $requiredDocs[] = 'Offer Letter/Appointment Letter';
        }
    
        // 7️⃣ Check for missing documents
        $missingDocs = array_diff($requiredDocs, $documents);
    
        if (!empty($missingDocs) && $userId != 1) {
            return redirect('/account/settings/profile-settings?tab=documents')
                ->with('missing_docs', implode(', ', $missingDocs));
        }
    
        // 8️⃣ Redirect based on designation
        if ($user->designation_id == 12) {
            return redirect('account/dashboard/agent');
        } else {
            return redirect('account/dashboard/non-csa');
        }
    
        // 9️⃣ Role-based dashboards (fallback)
        if (in_array('employee', user_roles())) {
            return $this->employeeDashboard();
        }
    
        if (in_array('client', user_roles())) {
            return $this->clientPanelDashboard();
        }
    }

    
    
    
    public function show_pdf(Request $request)
    {
        $filePath = public_path('NIFTEL Handbook 2024-2025 Updated April 2025 (1).pdf');

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="protected.pdf"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function widget(Request $request, $dashboardType)
    {
        $data = $request->all();
        unset($data['_token']);
        DashboardWidget::where('status', 1)->where('dashboard_type', $dashboardType)->update(['status' => 0]);

        foreach ($data as $key => $widget) {
            DashboardWidget::where('widget_name', $key)->where('dashboard_type', $dashboardType)->update(['status' => 1]);
        }

        return Reply::success(__('messages.updateSuccess'));
    }

    public function checklist()
    {
        if (in_array('admin', user_roles())) {
            $this->isCheckScript();

            return view('dashboard.checklist', $this->data);
        }
    }

    /**
     * @return array|\Illuminate\Http\Response
     */
    public function memberDashboard()
    {
        abort_403(!in_array('employee', user_roles()));

        return $this->employeeDashboard();
    }

    public function advancedDashboard()
    {

        if (in_array('admin', user_roles()) || $this->sidebarUserPermissions['view_overview_dashboard'] == 4
            || $this->sidebarUserPermissions['view_project_dashboard'] == 4
            || $this->sidebarUserPermissions['view_client_dashboard'] == 4
            || $this->sidebarUserPermissions['view_hr_dashboard'] == 4
            || $this->sidebarUserPermissions['view_ticket_dashboard'] == 4
            || $this->sidebarUserPermissions['view_finance_dashboard'] == 4) {

            $tab = request('tab');

            switch ($tab) {
            case 'project':
                $this->projectDashboard();
                break;
            case 'client':
                $this->clientDashboard();
                break;
            case 'hr':
                $this->hrDashboard();
                break;
            case 'ticket':
                $this->ticketDashboard();
                break;
            case 'finance':
                $this->financeDashboard();
                break;
            default:
                if (in_array('admin', user_roles()) || $this->sidebarUserPermissions['view_overview_dashboard'] == 4) {
                    $this->activeTab = $tab ?: 'overview';
                    $this->overviewDashboard();

                }
                elseif ($this->sidebarUserPermissions['view_project_dashboard'] == 4) {
                    $this->activeTab = $tab ?: 'project';
                    $this->projectDashboard();

                }
                elseif ($this->sidebarUserPermissions['view_client_dashboard'] == 4) {
                    $this->activeTab = $tab ?: 'client';
                    $this->clientDashboard();

                }
                elseif ($this->sidebarUserPermissions['view_hr_dashboard'] == 4) {
                    $this->activeTab = $tab ?: 'hr';
                    $this->hrDashboard();

                }
                elseif ($this->sidebarUserPermissions['view_finance_dashboard'] == 4) {
                    $this->activeTab = $tab ?: 'finance';
                    $this->ticketDashboard();

                }
                else if ($this->sidebarUserPermissions['view_ticket_dashboard'] == 4) {
                    $this->activeTab = $tab ?: 'finance';
                    $this->financeDashboard();
                }

                break;
            }

            if (request()->ajax()) {
                $html = view($this->view, $this->data)->render();

                return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
            }

            if (!isset($this->activeTab)) {
                $this->activeTab = $tab ?: 'overview';
            }

            return view('dashboard.admin', $this->data);
        }
    }

    public function accountUnverified()
    {
        return view('dashboard.unverified', $this->data);
    }

    public function weekTimelog()
    {
        $now = now(company()->timezone);
        $attndcSetting = AttendanceSetting::first();
        $this->timelogDate = $timelogDate = Carbon::parse(request()->date);
        $this->weekStartDate = $now->copy()->startOfWeek($attndcSetting->week_start_from);
        $this->weekEndDate = $this->weekStartDate->copy()->addDays(7);
        $this->weekPeriod = CarbonPeriod::create($this->weekStartDate, $this->weekStartDate->copy()->addDays(6)); // Get All Dates from start to end date

        $this->dateWiseTimelogs = ProjectTimeLog::dateWiseTimelogs($timelogDate->toDateString(), user()->id);
        $this->dateWiseTimelogBreak = ProjectTimeLogBreak::dateWiseTimelogBreak($timelogDate->toDateString(), user()->id);

        $this->weekWiseTimelogs = ProjectTimeLog::weekWiseTimelogs($this->weekStartDate->copy()->toDateString(), $this->weekEndDate->copy()->toDateString(), user()->id);
        $this->weekWiseTimelogBreak = ProjectTimeLogBreak::weekWiseTimelogBreak($this->weekStartDate->toDateString(), $this->weekEndDate->toDateString(), user()->id);

        $html = view('dashboard.employee.week_timelog', $this->data)->render();

        return Reply::dataOnly(['html' => $html]);
    }

    public function privateCalendar()
    {
        if (request()->filter) {
            $employee_details = EmployeeDetails::where('user_id', user()->id)->first();
            $employee_details->calendar_view = (request()->filter != false) ? request()->filter : null;
            $employee_details->save();
            session()->forget('user');
        }

        $startDate = Carbon::parse(request('start'));
        $endDate = Carbon::parse(request('end'));

        // get calendar view current logined user
        $calendar_filter_array = explode(',', user()->employeeDetails->calendar_view);

        $eventData = array();

        if (!is_null(user()->permission('view_events')) && user()->permission('view_events') != 'none') {

            if (in_array('events', $calendar_filter_array)) {
                // Events
                $model = Event::with('attendee', 'attendee.user');

                $model->where(function ($query) {
                    $query->whereHas('attendee', function ($query) {
                        $query->where('user_id', user()->id);
                    });
                    $query->orWhere('added_by', user()->id);
                });

                $model->whereBetween('start_date_time', [$startDate->toDateString(), $endDate->toDateString()]);

                $events = $model->get();


                foreach ($events as $event) {
                    $eventData[] = [
                        'id' => $event->id,
                        'title' => ucfirst($event->event_name),
                        'start' => $event->start_date_time,
                        'end' => $event->end_date_time,
                        'event_type' => 'event',
                        'extendedProps' => ['bg_color' => $event->label_color, 'color' => '#fff', 'icon' => 'fa-calendar']
                    ];
                }
            }

        }

        if (!is_null(user()->permission('view_holiday')) && user()->permission('view_holiday') != 'none') {
            if (in_array('holiday', $calendar_filter_array)) {
                // holiday
                $holidays = Holiday::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->get();

                foreach ($holidays as $holiday) {
                    $eventData[] = [
                        'id' => $holiday->id,
                        'title' => ucfirst($holiday->occassion),
                        'start' => $holiday->date,
                        'end' => $holiday->date,
                        'event_type' => 'holiday',
                        'extendedProps' => ['bg_color' => '#1d82f5', 'color' => '#fff', 'icon' => 'fa-star']
                    ];
                }
            }

        }

        if (!is_null(user()->permission('view_tasks')) && user()->permission('view_tasks') != 'none') {

            if (in_array('task', $calendar_filter_array)) {
                // tasks
                $completedTaskColumn = TaskboardColumn::completeColumn();
                $tasks = Task::with('boardColumn')
                    ->where('board_column_id', '<>', $completedTaskColumn->id)
                    ->whereHas('users', function ($query) {
                        $query->where('user_id', user()->id);
                    })
                    ->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween(DB::raw('DATE(tasks.`due_date`)'), [$startDate->toDateString(), $endDate->toDateString()]);

                        $q->orWhereBetween(DB::raw('DATE(tasks.`start_date`)'), [$startDate->toDateString(), $endDate->toDateString()]);
                    })->get();

                foreach ($tasks as $task) {
                    $eventData[] = [
                        'id' => $task->id,
                        'title' => ucfirst($task->heading),
                        'start' => $task->start_date,
                        'end' => $task->due_date ?: $task->start_date,
                        'event_type' => 'task',
                        'extendedProps' => ['bg_color' => $task->boardColumn->label_color, 'color' => '#fff', 'icon' => 'fa-list']
                    ];
                }
            }
        }

        if (!is_null(user()->permission('view_tickets')) && user()->permission('view_tickets') != 'none') {

            if (in_array('tickets', $calendar_filter_array)) {
                // tickets
                $tickets = Ticket::where('user_id', user()->id)
                    ->whereBetween(DB::raw('DATE(tickets.`updated_at`)'), [$startDate->toDateTimeString(), $endDate->endOfDay()->toDateTimeString()])->get();

                foreach ($tickets as $key => $ticket) {
                    $eventData[] = [
                        'id' => $ticket->ticket_number,
                        'title' => ucfirst($ticket->subject),
                        'start' => $ticket->updated_at,
                        'end' => $ticket->updated_at,
                        'event_type' => 'ticket',
                        'extendedProps' => ['bg_color' => '#1d82f5', 'color' => '#fff', 'icon' => 'fa-ticket-alt']
                    ];
                }
            }

        }

        if (!is_null(user()->permission('view_leave')) && user()->permission('view_leave') != 'none') {

            if (in_array('leaves', $calendar_filter_array)) {
                // approved leaves of all emoloyees with employee name
                $leaves = Leave::join('leave_types', 'leave_types.id', 'leaves.leave_type_id')
                    ->where('leaves.status', 'approved')
                    ->select('leaves.id', 'leaves.leave_date', 'leaves.status', 'leave_types.type_name', 'leave_types.color', 'leaves.leave_date', 'leaves.duration', 'leaves.status', 'leaves.user_id')
                    ->with('user')
                    ->whereBetween(DB::raw('DATE(leaves.`leave_date`)'), [$startDate->toDateString(), $endDate->toDateString()])
                    ->get();

                foreach ($leaves as $leave) {
                    $duration = ($leave->duration == 'half day') ? '( ' . __('app.halfday') . ' )' : '';

                    $eventData[] = [
                        'id' => $leave->id,
                        'title' => $duration . ' ' . ucfirst($leave->user->name),
                        'start' => $leave->leave_date->toDateString(),
                        'end' => $leave->leave_date->toDateString(),
                        'event_type' => 'leave',
                        /** @phpstan-ignore-next-line */
                        'extendedProps' => ['name' => 'Leave : ' . ucfirst($leave->user->name), 'bg_color' => $leave->color, 'color' => '#fff', 'icon' => 'fa-plane-departure']
                    ];
                }
            }
        }

        return $eventData;
    }
    
    public function agent_dash(Request $request)
    {
        
        // $user = auth()->user()->image_url;
        // return $user;
        $userId = user()->id;
    
        // Get month and year from URL parameters, default to current month if not provided
        $this->year = $request->input('year', Carbon::now()->year);
        $this->month = $request->input('month', Carbon::now()->month);
    
        $this->startDate = Carbon::create($this->year, $this->month, 1)->startOfMonth()->toDateString();
        $this->endDate = Carbon::create($this->year, $this->month, 1)->endOfMonth()->toDateString();
    
        // Manual Attendance (MIS Uploaded)
        $this->filteredData = AgentAttendance::where('user_id', $userId)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) {
                $targetHrsSec = $attendance->target_login_hrs ? (strtotime($attendance->target_login_hrs) - strtotime('00:00:00')) : 0;
                $totalLoginSec = $attendance->total_login ? (strtotime($attendance->total_login) - strtotime('00:00:00')) : 0;
                return array_merge($attendance->toArray(), [
                    'target_login_hrs_sec' => $targetHrsSec,
                    'total_login_sec' => $totalLoginSec,
                ]);
            })
            ->toArray();
    
        // Biometric Attendance
        $this->biometricData = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) {
                $clockIn = Carbon::parse($attendance->clock_in_time);
                $clockOut = Carbon::parse($attendance->clock_out_time);
                $loginHour = $attendance->clock_in_time && $attendance->clock_out_time ? $clockOut->diffInHours($clockIn) : 0;
                return [
                    'date' => $attendance->date,
                    'clock_in_time' => $attendance->clock_in_time,
                    'clock_out_time' => $attendance->clock_out_time,
                    'login_hour' => $loginHour,
                ];
            })
            ->toArray();
    
        $biometricMap = collect($this->biometricData)->keyBy('date');
    
        // Compare and flag short login days
        $this->shortLoginCount = 0;
        $this->filteredData = collect($this->filteredData)->map(function ($day) use ($biometricMap) {
            $biometric = $biometricMap[$day['date']] ?? null;
            $biometricLoginHour = $biometric['login_hour'] ?? 0;
            $targetLoginHour = $day['target_login_hrs_sec'] / 3600;
    
            $isShortLogin = $targetLoginHour - $biometricLoginHour > 0.01;
    
            if ($isShortLogin) {
                $this->shortLoginCount++;
            }
    
            return array_merge($day, [
                'is_short_login' => $isShortLogin,
                'biometric_login_hour' => $biometricLoginHour,
                'target_login_hour' => $targetLoginHour,
            ]);
        })->toArray();
    
        // Totals
        $this->totalPresentDays = array_sum(array_column($this->filteredData, 'present_day'));
        $this->totalTargetLoginHrsSec = array_sum(array_column($this->filteredData, 'target_login_hrs_sec'));
        $this->totalLoginSec = array_sum(array_column($this->filteredData, 'total_login_sec'));
    
        // View data
        $this->currentMonthYear = Carbon::create($this->year, $this->month)->format('F Y');
        $this->years = range(Carbon::now()->year - 5, Carbon::now()->year + 5);
        $this->months = range(1, 12);
        $this->maxShortLoginsAllowed = 3;
        
        
        return view('dashboard.agent', $this->data);
    }
    
    public function tl_dash(Request $request)
{
    $tlId = user()->username;

    $this->year = $request->input('year', \Carbon\Carbon::now()->year);
    $this->month = $request->input('month', \Carbon\Carbon::now()->month);

    $this->startDate = \Carbon\Carbon::create($this->year, $this->month, 1)->startOfMonth()->toDateString();
    $this->endDate = \Carbon\Carbon::create($this->year, $this->month, 1)->endOfMonth()->toDateString();

    $this->fullEmployeeData = AgentAttendance::with(['user' => function ($query) {
        $query->with('employeeDetail'); // Load employeeDetail from User
    }])
        ->where('tl_name', $tlId)
        ->whereBetween('date', [$this->startDate, $this->endDate])
        ->get()
        ->map(function ($attendance) {
            // Convert time strings to seconds, handling null or invalid values
            $targetHrsSec = $attendance->target_login_hrs ? (strtotime($attendance->target_login_hrs) - strtotime('00:00:00')) : 0;
            $loginHrsSec = $attendance->total_login ? (strtotime($attendance->total_login) - strtotime('00:00:00')) : 0;
            
            // Get employee name from user->employeeDetail
            $employeeName = $attendance->user && $attendance->user->employeeDetail ? 
                $attendance->user->employeeDetail->name : 
                ($attendance->user ? $attendance->user->name : 'Unknown');
            
            return array_merge($attendance->toArray(), [
                'employee_name' => $employeeName,
                'target_login_hrs_sec' => $targetHrsSec,
                'total_login_sec' => $loginHrsSec,
            ]);
        })
        ->toArray();

    // return $this->fullEmployeeData;
    // Use a local variable to avoid overloaded property issue
    $employeeData = [];
    foreach ($this->fullEmployeeData as $data) {
        $userId = $data['user_id'];
        if (!isset($employeeData[$userId])) {
            $employeeData[$userId] = [
                'user_id' => $userId,
                'employee_name' => $data['name_of_employee'], // Add employee_name
                'total_present_day' => 0, // Initialize total_present_day
                'total_target_login_hrs_sec' => 0,
                'total_login_sec' => 0,
            ];
        }
        $employeeData[$userId]['total_present_day'] += $data['present_day'] ?? 0; // Sum present_day
        $employeeData[$userId]['total_target_login_hrs_sec'] += $data['target_login_hrs_sec'];
        $employeeData[$userId]['total_login_sec'] += $data['total_login_sec'];
    }
    
    $this->employeeData = array_values($employeeData);
    $this->fullEmployeeData = $this->fullEmployeeData;
    $this->currentMonthYear = \Carbon\Carbon::createFromFormat('Y-m-d', $this->startDate)->format('F Y');
    $this->years = range(\Carbon\Carbon::now()->year - 5, \Carbon\Carbon::now()->year + 5);
    $this->months = range(1, 12);


    return view('dashboard.tl', $this->data);
}
    public function employee_detail($userId)
    {
        $this->year = request()->input('year', \Carbon\Carbon::now()->year);
        $this->month = request()->input('month', \Carbon\Carbon::now()->month);
        
        $tl = user()->username;
        $this->startDate = \Carbon\Carbon::create($this->year, $this->month, 1)->startOfMonth()->toDateString();
        $this->endDate = \Carbon\Carbon::create($this->year, $this->month, 1)->endOfMonth()->toDateString();
    
        $this->filteredData = AgentAttendance::with(['user' => function ($query) {
            $query->with('employeeDetail');
        }])
            ->where('user_id', $userId)
            ->where('tl_name', $tl)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) {
                // Convert time strings to seconds, handling null or invalid values
                $targetHrsSec = $attendance->target_login_hrs ? (strtotime($attendance->target_login_hrs) - strtotime('00:00:00')) : 0;
                $totalLoginSec = $attendance->total_login ? (strtotime($attendance->total_login) - strtotime('00:00:00')) : 0;
                return array_merge($attendance->toArray(), [
                    'employee_name' => $attendance->user->employeeDetail->name ?? $attendance->user->name ?? 'Unknown',
                    'target_login_hrs_sec' => $targetHrsSec,
                    'total_login_sec' => $totalLoginSec,
                ]);
            })
            ->toArray();
    
        $this->biometricData = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) {
                $clockIn = \Carbon\Carbon::parse($attendance->clock_in_time);
                $clockOut = \Carbon\Carbon::parse($attendance->clock_out_time);
                $loginHour = $attendance->clock_in_time && $attendance->clock_out_time ? $clockOut->diffInHours($clockIn) : 0;
                return [
                    'date' => $attendance->date,
                    'clock_in_time' => $attendance->clock_in_time,
                    'clock_out_time' => $attendance->clock_out_time,
                    'login_hour' => $loginHour,
                ];
            })
            ->toArray();
    
        $this->currentMonthYear = \Carbon\Carbon::create($this->year, $this->month)->format('F Y');
    
        $this->totalPresentDays = array_sum(array_column($this->filteredData, 'present_day'));
        $this->totalTargetLoginHrsSec = array_sum(array_column($this->filteredData, 'target_login_hrs_sec'));
        $this->totalLoginSec = array_sum(array_column($this->filteredData, 'total_login_sec'));
    
        $this->filteredData = $this->filteredData;
        $this->biometricData = $this->biometricData;
        $this->currentMonthYear = $this->currentMonthYear;
        $this->startDate = $this->startDate;
        $this->endDate = $this->endDate;
        $this->years = range(\Carbon\Carbon::now()->year - 5, \Carbon\Carbon::now()->year + 5);
        $this->months = range(1, 12);
        $this->totalPresentDays = $this->totalPresentDays;
        $this->totalTargetLoginHrsSec = $this->totalTargetLoginHrsSec;
        $this->totalLoginSec = $this->totalLoginSec;
        $this->user_id = $userId;
    
    
        return view('dashboard.employee_detail', $this->data);
    }
    
    public function non_csa_dash(Request $request)
    {
        $userId = user()->id;

        // Local variables
        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Generate all dates in the selected month
        $allDates = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $allDates[$currentDate->toDateString()] = null;
            $currentDate->addDay();
        }

        $nonCsaRecords = NonCsaAttendance::where('user_id', $userId)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date')
            ->map(function ($attendance) {
                $loginHours = 0;
        
                if (!empty($attendance->in_time) && !empty($attendance->out_time)) {
                    try {
                        $inTime = Carbon::parse($attendance->in_time);
                        $outTime = Carbon::parse($attendance->out_time);
        
                        // In case clock out is before clock in (e.g., wrong data)
                        if ($outTime->greaterThan($inTime)) {
                            $loginHours = $outTime->floatDiffInHours($inTime);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Time parse error for Non-CSA: " . $e->getMessage());
                    }
                }
        
                return array_merge($attendance->toArray(), [
                    'login_hours' => $loginHours,
                ]);
            });


        // return $nonCsaRecords;
        // Build filteredData with missing dates
        $filteredData = [];
        foreach ($allDates as $date => $value) {
            if (isset($nonCsaRecords[$date])) {
                $filteredData[] = $nonCsaRecords[$date];
            } else {
                $filteredData[] = [
                    'process' => null,
                    'sub_process' => null,
                    'department' => null,
                    'emp_id' => null,
                    'email_id' => null,
                    'name' => null,
                    'supervisor_name' => null,
                    'designation' => null,
                    'month_year' => Carbon::parse($date)->format('F Y'),
                    'date' => $date,
                    'attendance_status' => null,
                    'in_time' => null,
                    'out_time' => null,
                    'login_hours' => null,
                ];
            }
        }
        
        // return $filteredData;

        // Biometric Data
        $biometricRecords = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date')
            ->map(function ($attendance) {
                $clockIn = $attendance->clock_in_time ? Carbon::parse($attendance->clock_in_time) : null;
                $clockOut = $attendance->clock_out_time ? Carbon::parse($attendance->clock_out_time) : null;
                $loginHour = ($clockIn && $clockOut) ? $clockOut->floatDiffInHours($clockIn) : 0;
                return [
                    'date' => $attendance->date,
                    'clock_in_time' => $attendance->clock_in_time,
                    'clock_out_time' => $attendance->clock_out_time,
                    'login_hour' => $loginHour,
                    'short_hours' => $loginHour < 9 && $loginHour > 0 ? true : false, // ðŸ‘ˆ new flag
                ];
            });


        // Build biometricData with missing dates
        $biometricData = [];
        foreach ($allDates as $date => $value) {
            if (isset($biometricRecords[$date])) {
                $biometricData[] = $biometricRecords[$date];
            } else {
                $biometricData[] = [
                    'date' => $date,
                    'clock_in_time' => null,
                    'clock_out_time' => null,
                    'login_hour' => null,
                ];
            }
        }
        
        // Count short biometric login days (< 9 hours)
        $shortLoginDays = collect($biometricData)->filter(function ($record) {
            return isset($record['short_hours']) && $record['short_hours'] === true;
        })->count();
        
        $this->shortLoginDays = $shortLoginDays;
        $this->maxAllowedShortDays = 3; // You can change this if needed


        $currentMonthYear = Carbon::create($year, $month)->format('F Y');
        $totalPresentDays = count(array_filter($filteredData, fn($record) => $record['attendance_status'] === 'P'));
        $totalLoginHours = array_sum(array_column($filteredData, 'login_hours') ?: [0]);

        // Assign to $this->data
        $this->filteredData = $filteredData;
        $this->biometricData = $biometricData;
        $this->currentMonthYear = $currentMonthYear;
        $this->startDate = $startDate->toDateString();
        $this->endDate = $endDate->toDateString();
        $this->years = range(Carbon::now()->year - 5, Carbon::now()->year + 5);
        $this->months = range(1, 12);
        $this->totalPresentDays = $totalPresentDays;
        $this->totalLoginHours = $totalLoginHours;

        return view('dashboard.non_csa', $this->data);
    }

}
