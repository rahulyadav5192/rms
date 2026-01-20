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
use App\Models\NonCsaAttendance;
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
use App\Models\AgentAttendance;
use App\Exports\FilteredEmployeesExport;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Event;
use Illuminate\Queue\Events\JobProcessed;
use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class AgentAttendanceController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Agent Attendance';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array('leaves', $this->user->modules));

            return $next($request);
        });
        
    }
    
    
    
    public function index()
    {
        $this->travels = DB::table('travel_allowances')
            ->where('user_id', user()->id)
            // ->select('id', 'start_date', 'end_date', 'destination', 'amount', 'transport_mode', 'status')
            ->orderBy('start_date', 'desc')
            ->where('trash_status',0)
            ->get();

        return view('travel.index', $this->data);
    }
    
    public function upload(){
        
        return view('attendances.agent.upload',$this->data);
    }
    public function uploadAttendance(Request $request)
    {
        if (!$request->hasFile('attendance_file')) {
            return response()->json(['error' => 'No file uploaded.'], 400);
        }

        $file = $request->file('attendance_file');
        if (!$file->isValid()) {
            return response()->json(['error' => 'Invalid file upload.'], 400);
        }

        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            return response()->json(['error' => 'Unable to open the CSV file.'], 500);
        }

        // Read and skip the header row
        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return response()->json(['error' => 'CSV file is empty or invalid.'], 400);
        }

        // Process in chunks of 500 records
        $chunkSize = 500;
        $records = [];
        $rowCount = 0;
        $totalRows = 0;
        $insertedCount = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            $totalRows++;
            // if (count($row) !== 25) {
            //     continue; // Skip malformed rows
            // }
            echo "Row $totalRows processed: " . json_encode($row) . "<br>"; // Debug output
            $records[] = $row;
            $rowCount++;

            if ($rowCount % $chunkSize === 0 || feof($handle)) {
                $result = $this->processChunk($records, $totalRows - $rowCount + 1);
                $insertedCount += $result['inserted'];
                $errors = array_merge($errors, $result['errors']);
                $records = [];
            }
        }

        fclose($handle);
        return response()->json([
            'message' => 'Attendance data uploaded and updated successfully.',
            'total' => $totalRows,
            'inserted' => $insertedCount,
            'errors' => $errors,
        ], 200);
    }

    private function processChunk(array $records, $startLine)
    {
        $attendanceData = [];
        $inserted = 0;
        $errors = [];

        foreach ($records as $index => $row) {
            $lineNumber = $startLine + $index;
            $record = [
                'week' => $row[0] ?? null,
                'ucid' => $row[1] ?? null,
                'sr_no' => $row[2] ?? null,
                'date' => $row[3] ?? null,
                'center' => $row[4] ?? null,
                'emp_id' => $row[5] ?? null,
                'name_of_employee' => $row[6] ?? null,
                'email_id' => $row[7] ?? null,
                'tl_name' => $row[8] ?? null,
                'sn_team_lead' => $row[9] ?? null,
                'am_name' => $row[10] ?? null,
                'lob' => $row[11] ?? null,
                'status' => $row[12] ?? null,
                'batch_no' => $row[13] ?? null,
                'designation' => $row[14] ?? null,
                'emp_type' => $row[15] ?? null,
                'doj' => $row[16] ?? null,
                'tenure_days' => $row[17] ?? null,
                'shift' => $row[18] ?? null,
                'attendance' => $row[19] ?? null,
                'target_login_hrs' => $row[20] ?? null,
                'cc_login_hrs' => $row[21] ?? null,
                'sf_login_hrs' => $row[22] ?? null,
                'total_login' => $row[23] ?? null,
                'present_day' => $row[24] ?? null,
            ];

            if (!$record['emp_id'] || !$record['date']) {
                $errors[] = ['line' => $lineNumber, 'employeeId' => $record['emp_id'] ?? 'N/A', 'message' => 'Missing emp_id or date'];
                continue;
            }

            try {
                $employee = DB::table('employee_details')->where('employee_id', $record['emp_id'])->first();
                if (!$employee) {
                    $errors[] = ['line' => $lineNumber, 'employeeId' => $record['emp_id'], 'message' => 'Employee not found'];
                    continue;
                }

                $existingRecord = AgentAttendance::where('date', \DateTime::createFromFormat('l, F d, Y', $record['date'])->format('Y-m-d'))
                    ->where('user_id', $employee->user_id)
                    ->first();

                $attendanceRecord = [
                    'week' => $record['week'],
                    'ucid' => $record['ucid'],
                    'sr_no' => (int)$record['sr_no'],
                    'date' => \DateTime::createFromFormat('l, F d, Y', $record['date'])->format('Y-m-d'),
                    'center' => $record['center'],
                    'user_id' => $employee->user_id,
                    'name_of_employee' => $record['name_of_employee'],
                    'email_id' => $record['email_id'],
                    'tl_name' => $record['tl_name'],
                    'sn_team_lead' => $record['sn_team_lead'],
                    'am_name' => $record['am_name'],
                    'lob' => $record['lob'],
                    'status' => $record['status'],
                    'batch_no' => $record['batch_no'],
                    'designation' => $record['designation'],
                    'emp_type' => $record['emp_type'],
                    'shift' => $record['shift'],
                    'attendance' => $record['attendance'],
                    'target_login_hrs' => $record['target_login_hrs'],
                    'cc_login_hrs' => $record['cc_login_hrs'],
                    'sf_login_hrs' => $record['sf_login_hrs'],
                    'total_login' => $record['total_login'],
                    'present_day' => $record['present_day'],
                    'updated_at' => now(),
                ];

                if ($existingRecord) {
                    $existingRecord->update($attendanceRecord);
                } else {
                    $attendanceData[] = $attendanceRecord;
                }
            } catch (\Exception $e) {
                $errors[] = ['line' => $lineNumber, 'employeeId' => $record['emp_id'], 'message' => $e->getMessage()];
                continue;
            }
        }

        if (!empty($attendanceData)) {
            $inserted += AgentAttendance::insert($attendanceData) ? count($attendanceData) : 0;
        }

        return ['inserted' => $inserted, 'errors' => $errors];
    }
    
    
    

    public function uploadNonCsaAttendance(Request $request)
    {
        if (!$request->hasFile('attendance_file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }
    
        $file = $request->file('attendance_file');
        $monthYear = $request->input('month_year', 'Apr-2025');
    
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return response()->json(['error' => 'Unable to open file'], 400);
        }
    
        // First row (Week and Dates)
        $firstRow = fgetcsv($handle);
        if (!$firstRow) {
            return response()->json(['error' => 'Missing date row'], 400);
        }
    
        // Second row (column headers with week ranges and attendance headers)
        $secondRow = fgetcsv($handle);
        if (!$secondRow) {
            return response()->json(['error' => 'Missing header row'], 400);
        }
    
        // Mapping date column indexes to their respective date and week number
        $dateMap = []; // [index => ['date' => Y-m-d, 'week' => Week-XX]]
        $currentWeek = null;
        foreach ($firstRow as $index => $value) {
            $cell = trim($value);
            if (preg_match('/^Week-\d+$/i', $cell)) {
                $currentWeek = $cell;
            } elseif (preg_match('/^[A-Za-z]+, [A-Za-z]+ \d{1,2}, \d{4}$/', $cell)) {
                try {
                    $parsedDate = Carbon::createFromFormat('l, F j, Y', $cell)->format('Y-m-d');
                    $dateMap[$index] = ['date' => $parsedDate, 'week' => $currentWeek];
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    
        if (empty($dateMap)) {
            return response()->json(['error' => 'No valid date columns found'], 400);
        }
    
        // Index positions for employee fields
        $empIdIndex = 3;
        $fixedFields = [
            'process' => 0,
            'sub_process' => 1,
            'department' => 2,
            'emp_id' => 3,
            'email_id' => 4,
            'name' => 5,
            'supervisor_name' => 6,
            'designation' => 7,
        ];
    
        $inserted = 0;
        $updated = 0;
        $errors = [];
        $total = 0;
    
        // Read in chunks
        $chunkSize = 100;
        $chunk = [];
    
        while (($row = fgetcsv($handle)) !== false) {
            $chunk[] = $row;
            if (count($chunk) >= $chunkSize) {
                $this->processChunk2($chunk, $fixedFields, $dateMap, $monthYear, $inserted, $updated, $errors, $total);
                $chunk = [];
            }
        }
    
        // Process remaining rows
        if (!empty($chunk)) {
            $this->processChunk2($chunk, $fixedFields, $dateMap, $monthYear, $inserted, $updated, $errors, $total);
        }
    
        fclose($handle);
    
        return response()->json([
            'inserted' => $inserted,
            'updated' => $updated,
            'errors' => $errors,
            'total' => $total,
        ]);
    }
    
    private function processChunk2(&$rows, $fixedFields, $dateMap, $monthYear, &$inserted, &$updated, &$errors, &$total)
    {
        foreach ($rows as $row) {
            $total++;
    
            $empId = trim($row[$fixedFields['emp_id']] ?? '');
            if (!$empId) continue;
    
            $employee = EmployeeDetails::where('employee_id', $empId)->first();
            if (!$employee) {
                $errors[] = ['line' => $total, 'employeeId' => $empId, 'message' => 'Employee not found'];
                continue;
            }
    
            $meta = [];
            foreach ($fixedFields as $field => $index) {
                $meta[$field] = trim($row[$index] ?? 'Unknown');
            }
    
            foreach ($dateMap as $dateIndex => $info) {
                $attendance = strtoupper(str_replace(' ', '', trim($row[$dateIndex] ?? 'A')));
                $inTime = $this->validateTime($row[$dateIndex + 1] ?? null);
                $outTime = $this->validateTime($row[$dateIndex + 2] ?? null);
    
                $existing = NonCsaAttendance::where('user_id', $employee->user_id)
                    ->where('date', $info['date'])->first();
    
                $data = array_merge($meta, [
                    'user_id' => $employee->user_id,
                    'month_year' => $monthYear,
                    'week_number' => $info['week'],
                    'date' => $info['date'],
                    'attendance_status' => $attendance,
                    'in_time' => $inTime,
                    'out_time' => $outTime,
                ]);
    
                if ($existing) {
                    $existing->update($data);
                    $updated++;
                } else {
                    NonCsaAttendance::create($data);
                    $inserted++;
                }
            }
        }
    }
    
    private function validateTime($time)
    {
        if (!$time || strtolower($time) == 'null') return null;
    
        $time = trim($time);
        // Accepts format like "10:00 AM" or "14:00"
        try {
            return Carbon::parse($time)->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
    
}

    


      