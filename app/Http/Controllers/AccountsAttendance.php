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
use App\Models\AgentAttendance;
use App\Models\NonCsaAttendance;

class AccountsAttendance extends AccountBaseController
{
    
    
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Attendances';
        
    }
    
    
    public function agent_dash(Request $request,$userid)
    {
        
        // $user = auth()->user()->image_url;
        $userId = $userid;
        // return $user;
        
        // employee_details
        $this->emp = DB::table('users')->where('users.id',$userId)
                                ->join('employee_details','employee_details.user_id','users.id')
                                ->first(); 
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
        
        
        return view('dashboard.accounts.agent', $this->data);
    }
    public function non_csa_dash(Request $request,$userid)
    {
        
        
        $userId = $userid;
        $this->emp = DB::table('users')->where('users.id',$userId)
                                ->join('employee_details','employee_details.user_id','users.id')
                                ->first(); 
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

        return view('dashboard.accounts.non_csa', $this->data);
    }
    
    




}