<?php

namespace App\Exports;

use App\Models\Leave;
use App\Models\Holiday;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use App\Models\EmployeeDetails;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public static $sum;
    public $year;
    public $month;
    public $late;
    public $userId;
    public $viewAttendancePermission;
    public $department;
    public $designation;
    public $startdate;
    public $enddate;

    public function __construct($year, $month, $id, $late, $department, $designation, $startdate, $enddate,$name,$branch)
    {
        $this->viewAttendancePermission = user()->permission('view_attendance');
        $this->year = $year;
        $this->month = $month;
        $this->userId = $id;
        $this->late = $late;
        $this->department = $department;
        $this->designation = $designation;
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->name = $name;
        $this->branch = $branch;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => [self::class, 'afterSheet'],
        ];
    }

    public static function afterSheet(AfterSheet $event)
{
    $event->sheet->getDelegate()->getStyle('B:AZ')
        ->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
}

    


    public function headings(): array
{
    $headings = ['Employee Name'];

    $period = CarbonPeriod::create($this->startdate, $this->enddate);

    foreach ($period as $date) {
        $formatted = $date->format('d-M'); // e.g., 01-Jun
        $headings[] = "$formatted Status";
        $headings[] = "$formatted In";
        $headings[] = "$formatted Out";
    }

    return $headings;
}


    public function collection()
    {
        $startDate = $this->startdate;
        $endDate = $this->enddate;
        $id = $this->userId;

        $employees = EmployeeDetails::join('users', 'employee_details.user_id', '=', 'users.id');
                    $employees->where('users.status', 'active');
        if ($id != 'all') {
            if ($this->viewAttendancePermission == 'owned') {
                $employees->where('users.id', user()->id);
            }
            else {
                $employees->where('users.id', $id);
            }
        }
        else if ($this->viewAttendancePermission == 'owned') {
            $employees->where('users.id', user()->id);
        }

        if ($this->viewAttendancePermission == 'owned') {
            $employees->where('users.id', user()->id);
        }

        if ($this->department != 'all') {
            $employees->where('employee_details.department_id', $this->department);
        }
        
        if ($this->name != '' && $this->name != 'NO') {
            $employees->where('users.name', 'LIKE', '%' . $this->name . '%')
                        ->orWhere('users.username', 'LIKE', '%' . $this->name . '%');
        }
        if ($this->branch != 'all' && $this->branch != '') {
            $employees->where('employee_details.branch_id', $this->branch);
        }
        
        if (in_array(auth()->user()->id, [139, 11405,13884,14220])) {
            $employees->where('employee_details.branch_id', 7);
        }
            
        if (auth()->user()->id == 11566 or auth()->user()->id == 235) {
            $employees = $employees->whereIn('employee_details.department_id', [18, 1, 28, 42]);
        }
        // if ($this->active_status == 'active') {
            //  $employees->where('users.status', 'active');
        // }
        
        // if ($this->active_status == 'inactive') {
        //     $employees->where('users.status', 'deactive');
        // }

        if ($this->designation != 'all') {
            $employees->where('employee_details.designation_id', $this->designation);
        }

        $employees = $employees->select('users.name', 'users.id','employee_details.employee_id')->get();
        $employeedata = array();
        $emp_attendance = 1;
        $employee_index = 0;

        foreach ($employees as $employee) {
            $userId = $employee->id;
            $employeedata[$employee_index]['employee_name'] = $employee->name .' ( '. $employee->employee_id.' )';

            $attendances = Attendance::where('attendances.user_id', '=', $userId);

            if ($this->late != 'all') {
                $attendances->where('attendances.late', $this->late);
            }

            $attendances = $attendances->orderBy('attendances.clock_in_time', 'asc')
                ->where(DB::raw('DATE(attendances.clock_in_time)'), '>=', $startDate->format('Y-m-d'))
                ->where(DB::raw('DATE(attendances.clock_in_time)'), '<=', $endDate->format('Y-m-d'))
                ->select(DB::raw('DATE_FORMAT(attendances.clock_in_time, "%Y-%m-%d") as date'), 'attendances.clock_in_time', 'attendances.clock_out_time', 'attendances.late', 'attendances.half_day')->get();

            $leavesDates = Leave::where('user_id', $userId)
                ->where('leave_date', '>=', $startDate)
                ->where('leave_date', '<=', $endDate)
                ->where('status', 'approved')
                ->select('leave_date', 'reason', 'duration')->get();

            $period = CarbonPeriod::create($startDate, $endDate); // Get All Dates from start to end date
            $holidays = Holiday::getHolidayByDates($startDate, $endDate); // Getting Holiday Data

            $attendances = collect($attendances)->each(function ($item) {
                $item->status = '';
                $item->occassion = '';
            });

            // Add New Collection if date does not match with attendance collection...
            foreach ($period->toArray() as $date) {
                $att = new Attendance();
                $att->date = $date->format('Y-m-d');
                $att->clock_in_time = null;
                $att->clock_out_time = null;
                $att->late = null;
                $att->half_day = null;

                if ($date->lessThan(now()) && !$attendances->whereBetween('clock_in_time', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])->count()) {

                    $att->status = 'Absent';
                    // If date is not in attendance..
                    foreach ($leavesDates as $leave) { // check leaves
                        if ($date->equalTo($leave->leave_date)) {
                            $att->status = 'Leave';
                        }
                    }

                    foreach ($holidays as $holiday) { // Check holidays
                        if (\Carbon\Carbon::createFromFormat('Y-m-d', $holiday->holiday_date)->startOfDay()->equalTo($date)) {
                            $att->status = 'Holiday';
                            $att->occassion = $holiday->occassion;
                        }
                    }

                    $attendances->push($att);

                }
                else if ($date->lessThan(now())) {
                    // else date present in attendance then check for holiday and leave
                    foreach ($leavesDates as $leave) { // check employee leaves

                        if ($date->equalTo($leave->leave_date)) {
                            $att->status = 'Leave';
                            $attendances->push($att);
                        }
                    }

                    foreach ($holidays as $holiday) { // Check holidays

                        if ($date->format('Y-m-d') == $holiday->holiday_date && !$attendances->whereBetween('clock_in_time', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])->count()) {
                            $att->status = 'Holiday';
                            $att->occassion = $holiday->occassion;
                            $attendances->push($att);
                        }
                        else if ($date->format('Y-m-d') == $holiday->holiday_date && $attendances->whereBetween('clock_in_time', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])->count()) {
                            // here modify the collection property not creating new
                            $this->checkHolidays($attendances, $date);
                        }

                    }

                }
            }

            $employee_temp = array();


            foreach ($attendances->sortBy('date') as $attendance) {
                $date = Carbon::createFromFormat('Y-m-d', $attendance->date)->timezone(company()->timezone)->format(company()->date_format);

                $to = $attendance->clock_out_time ? \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $attendance->clock_out_time) : null;
                $from = $attendance->clock_in_time ? \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $attendance->clock_in_time) : null;

                $clock_in = $attendance->clock_in_time ? Carbon::createFromFormat('Y-m-d H:i:s', $attendance->clock_in_time)->timezone(company()->timezone)->format(company()->time_format) : 0;
                $clock_out = $attendance->clock_out_time ? Carbon::createFromFormat('Y-m-d H:i:s', $attendance->clock_out_time)->timezone(company()->timezone)->format(company()->time_format) : 0;

                $diff_in_hours = ($to && $from) ? $to->diffInHours($from) : 0;

                if ($attendance->status != null) {


                    if ($attendance->status == 'Absent') {
                        $status = __('app.absent');
                    }
                    else if ($attendance->status == 'Leave') {
                        $status = __('app.onLeave');
                    }
                    else if ($attendance->status == 'Holiday') {
                        $status = __('app.holiday', ['name' => $attendance->occassion]);
                    }
                }
                else if ($attendance->late == 'yes' && $attendance->half_day == 'yes') {
                    $status = __('app.lateHalfday');
                }
                else if ($attendance->late == 'yes') {
                    $status = __('app.presentlate');
                }
                else if ($attendance->half_day == 'yes') {
                    $status = __('app.halfday');
                }
                else {
                    $status = __('app.present');
                }

                if ($employee_temp && $employee_temp[1] == $date) {
                    $employeedata[$employee_index]['dates'][$emp_attendance - 1]['clock_in_time'] .= $clock_in ? (' | ' . $clock_in) : '';
                    $employeedata[$employee_index]['dates'][$emp_attendance - 1]['clock_in_time'] .= $clock_out ? (' | ' . $clock_out) : '';
                    $employeedata[$employee_index]['dates'][$emp_attendance - 1]['total_hours'] += $diff_in_hours;
                } else {
                    $employeedata[$employee_index]['dates'][$emp_attendance] = [
                        'total_hours' => $diff_in_hours,
                        'date' => $attendance->date,
                        'clock_in_time' => $clock_in,
                        'clock_out_time' => $clock_out,
                        'status' => $status,
                    ];

                    $emp_attendance++;
                }

                $employee_temp = [$emp_attendance, $date];
            }

            $employee_index++;
            $emp_attendance = 1;
        }

        $employeedata = collect($employeedata);
        self::$sum = $employeedata;

        return $employeedata;
    }

    public function map($employeedata): array
    {
        $data = [];
        $data[] = $employeedata['employee_name'];
    
        $dates = $employeedata['dates'] ?? [];
    
        // Sort by date (just in case)
        usort($dates, fn($a, $b) => strtotime($a['date']) <=> strtotime($b['date']));
    
        foreach ($dates as $day) {
            $data[] = $day['status'] ?? '-';
            $data[] = $day['clock_in_time'] ?? '-';
            $data[] = $day['clock_out_time'] ?? '-';
        }
    
        return $data;
    }


    public function checkHolidays($attendances, $date)
    {
        foreach ($attendances as $attendance) {
            if ($date->format('Y-m-d') == \Carbon\Carbon::parse($attendance->clock_in_time)->format('Y-m-d')) {
                $attendance->status = '';
            }
        }
    }

}
