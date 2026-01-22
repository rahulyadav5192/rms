<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Team;
use App\Models\Branch;
use App\Models\Designation;
use App\Models\NonCsaAttendance;
use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class NonCsaAttendanceDashboardExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    public $year;
    public $month;
    public $department;
    public $branch;
    public $designation;
    public $name;
    public $activeStatus;
    public $employeeType;
    public $startDate;
    public $endDate;
    public $allDates;
    public $attendanceData;

    public function __construct($year, $month, $department, $branch, $designation, $name, $activeStatus, $employeeType)
    {
        $this->year = $year;
        $this->month = $month;
        $this->department = $department;
        $this->branch = $branch;
        $this->designation = $designation;
        $this->name = $name;
        $this->activeStatus = $activeStatus;
        $this->employeeType = $employeeType;
        
        $this->startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $this->endDate = Carbon::create($year, $month, 1)->endOfMonth();
        $daysInMonth = $this->endDate->day;
        
        // Generate all dates
        $this->allDates = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day)->format('Y-m-d');
            $this->allDates[] = $date;
        }
        
        // Build employee query (same as controller)
        $employeesQuery = User::join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->leftJoin('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->where('roles.name', '<>', 'client')
            ->select('users.id', 'users.name', 'users.email', 'users.status', 'users.image', 
                     'employee_details.employee_id', 'employee_details.department_id', 
                     'employee_details.branch_id', 'employee_details.designation_id')
            ->groupBy('users.id');
        
        // Apply CSA/Non-CSA filter
        if ($employeeType === 'csa') {
            $employeesQuery->where('employee_details.designation_id', 12);
        } elseif ($employeeType === 'non_csa') {
            $employeesQuery->where(function($query) {
                $query->where('employee_details.designation_id', '<>', 12)
                      ->orWhereNull('employee_details.designation_id');
            });
        }
        
        // Apply other filters
        if ($department != 'all') {
            $employeesQuery->where('employee_details.department_id', $department);
        }
        
        if ($branch != 'all' && $branch != '') {
            $employeesQuery->where('employee_details.branch_id', $branch);
        }
        
        if ($designation != 'all') {
            $employeesQuery->where('employee_details.designation_id', $designation);
        }
        
        if ($name != '') {
            $nameParts = array_map('trim', explode(',', $name));
            $nameParts = array_filter($nameParts);
            
            if (!empty($nameParts)) {
                $employeesQuery->where(function($query) use ($nameParts) {
                    foreach ($nameParts as $part) {
                        $part = trim($part);
                        if (empty($part)) continue;
                        
                        if (is_numeric($part)) {
                            $query->orWhere('users.id', $part)
                                  ->orWhere('employee_details.employee_id', $part);
                        } else {
                            $query->orWhere('users.name', 'LIKE', '%' . $part . '%')
                                  ->orWhere('users.username', 'LIKE', '%' . $part . '%')
                                  ->orWhere('employee_details.employee_id', 'LIKE', '%' . $part . '%');
                        }
                    }
                });
            }
        }
        
        if ($activeStatus === '0') {
            $employeesQuery->where('users.status', 'active');
        } elseif ($activeStatus === '1') {
            $employeesQuery->where('users.status', 'deactive');
        }
        
        // Apply user-specific restrictions
        if (in_array(auth()->user()->id, [139, 11405, 13884, 14220])) {
            $employeesQuery->where('employee_details.branch_id', 7);
        }
        if (auth()->user()->id == 11566 || auth()->user()->id == 235) {
            $employeesQuery->whereIn('employee_details.department_id', [18, 1, 28, 42]);
        }
        if (auth()->user()->id == 16531) {
            $employeesQuery->whereIn('employee_details.department_id', [20]);
        }
        
        $employees = $employeesQuery->get();
        $employeeIds = $employees->pluck('id')->toArray();
        
        if (empty($employeeIds)) {
            $this->attendanceData = [];
            return;
        }
        
        // Fetch sheet-based data
        $sheetData = NonCsaAttendance::whereIn('user_id', $employeeIds)
            ->whereBetween('date', [$this->startDate->toDateString(), $this->endDate->toDateString()])
            ->select('user_id', 'date', 'in_time as clock_in', 'out_time as clock_out', 'attendance_status')
            ->get()
            ->groupBy('user_id')
            ->map(function ($records) {
                return $records->keyBy('date');
            });
        
        // Fetch punch machine data
        $punchData = Attendance::whereIn('user_id', $employeeIds)
            ->whereBetween('date', [$this->startDate->toDateString(), $this->endDate->toDateString()])
            ->select('user_id', 'date', 'clock_in_time as punch_in', 'clock_out_time as punch_out')
            ->get()
            ->groupBy('user_id')
            ->map(function ($records) {
                return $records->keyBy('date');
            });
        
        // Build attendance data
        $attendanceData = [];
        $requiredHours = 9;
        
        foreach ($employees as $employee) {
            $empId = $employee->id;
            $attendanceData[$empId] = [
                'employee' => $employee,
                'dates' => [],
            ];
            
            foreach ($this->allDates as $date) {
                $sheetRecord = $sheetData[$empId][$date] ?? null;
                $clockIn = $sheetRecord ? ($sheetRecord->clock_in ?? null) : null;
                $clockOut = $sheetRecord ? ($sheetRecord->clock_out ?? null) : null;
                
                $punchRecord = $punchData[$empId][$date] ?? null;
                $punchIn = $punchRecord ? ($punchRecord->punch_in ?? null) : null;
                $punchOut = $punchRecord ? ($punchRecord->punch_out ?? null) : null;
                
                // Calculate sheet hours
                $totalHours = 0;
                if ($clockIn && $clockOut) {
                    try {
                        $inTime = Carbon::parse($clockIn);
                        $outTime = Carbon::parse($clockOut);
                        if ($outTime->greaterThan($inTime)) {
                            $totalHours = $outTime->floatDiffInHours($inTime);
                        }
                    } catch (\Exception $e) {
                        // Invalid time format
                    }
                }
                
                // Calculate punch hours
                $punchHours = 0;
                if ($punchIn && $punchOut) {
                    try {
                        $punchInTime = Carbon::parse($punchIn);
                        $punchOutTime = Carbon::parse($punchOut);
                        if ($punchOutTime->greaterThan($punchInTime)) {
                            $punchHours = $punchOutTime->floatDiffInHours($punchInTime);
                        }
                    } catch (\Exception $e) {
                        // Invalid time format
                    }
                }
                
                $attendanceData[$empId]['dates'][$date] = [
                    'clock_in' => $clockIn ? Carbon::parse($clockIn)->format('H:i') : '',
                    'clock_out' => $clockOut ? Carbon::parse($clockOut)->format('H:i') : '',
                    'total_hours' => $totalHours > 0 ? number_format($totalHours, 2) : '',
                    'punch_in' => $punchIn ? Carbon::parse($punchIn)->format('H:i') : '',
                    'punch_out' => $punchOut ? Carbon::parse($punchOut)->format('H:i') : '',
                    'punch_hours' => $punchHours > 0 ? number_format($punchHours, 2) : '',
                ];
            }
        }
        
        $this->attendanceData = $attendanceData;
    }

    public function headings(): array
    {
        $headings = ['Employee Name', 'Employee ID'];
        
        foreach ($this->allDates as $date) {
            $dateFormatted = Carbon::parse($date)->format('d-M');
            $headings[] = $dateFormatted . ' Clock In';
            $headings[] = $dateFormatted . ' Clock Out';
            $headings[] = $dateFormatted . ' Sheet Hours';
            $headings[] = $dateFormatted . ' Punch In';
            $headings[] = $dateFormatted . ' Punch Out';
            $headings[] = $dateFormatted . ' Punch Hours';
        }
        
        return $headings;
    }

    public function collection()
    {
        $data = [];
        
        foreach ($this->attendanceData as $empId => $empData) {
            $row = [
                'employee_name' => $empData['employee']->name,
                'employee_id' => $empData['employee']->employee_id ?? '',
            ];
            
            foreach ($this->allDates as $date) {
                $dayData = $empData['dates'][$date] ?? null;
                if ($dayData) {
                    $row[] = $dayData['clock_in'];
                    $row[] = $dayData['clock_out'];
                    $row[] = $dayData['total_hours'];
                    $row[] = $dayData['punch_in'];
                    $row[] = $dayData['punch_out'];
                    $row[] = $dayData['punch_hours'];
                } else {
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                    $row[] = '';
                }
            }
            
            $data[] = $row;
        }
        
        return collect($data);
    }

    public function map($row): array
    {
        return $row;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => [self::class, 'afterSheet'],
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->getDelegate()->getStyle('A:ZZ')
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Make header row bold
        $event->sheet->getDelegate()->getStyle('1:1')
            ->getFont()
            ->setBold(true);
    }
}

