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
        // try {
            if ($request->hasFile('attendance_file')) {
                $file = $request->file('attendance_file');
                $handle = fopen($file->getRealPath(), 'r');

                if ($handle === false) {
                    throw new \Exception("Unable to open the CSV file.");
                }

                // Read the header row
                $header = fgetcsv($handle);
                if ($header === false) {
                    fclose($handle);
                    throw new \Exception("CSV file is empty or invalid.");
                }

                Log::info('CSV Header: ' . json_encode($header));

                // Process in chunks of 500 records
                $chunkSize = 500;
                $records = [];
                $rowCount = 0;

                while (($row = fgetcsv($handle)) !== false) {
                    // Ensure row has the same number of columns as header
                    if (count($row) !== count($header)) {
                        Log::warning('Row with mismatched columns detected: ' . json_encode($row));
                        continue; // Skip malformed rows
                    }

                    $records[] = array_combine($header, $row);
                    $rowCount++;

                    if ($rowCount % $chunkSize === 0) {
                        $this->processChunk($records);
                        $records = [];
                    }
                }
                
                // return $records;

                // Process any remaining records
                // if (!empty($records)) {
                    return $this->processChunk($records);
                // }

                fclose($handle);
                return response()->json(['message' => 'Attendance data uploaded and updated successfully.'], 200);
            }

            return response()->json(['message' => 'No file uploaded.'], 400);
        // } catch (ModelNotFoundException $e) {
        //     Log::error('Employee not found: ' . $e->getMessage());
        //     return response()->json(['message' => 'Error: ' . $e->getMessage()], 404);
        // } catch (\Exception $e) {
        //     Log::error('Error uploading attendance: ' . $e->getMessage());
        //     return response()->json(['message' => 'Error processing file: ' . $e->getMessage()], 500);
        // }
    }

    private function processChunk(array $records)
    {
        $attendanceData = [];
        foreach ($records as $record) {
            Log::info('Processing record: ' . json_encode($record));
            
            
            // Check if required keys exist
            $requiredKeys = ['Week', 'UCID', 'Sr No', 'Date', 'Center', 'EMP ID', 'NAME OF EMPLOYEES', 'Email ID', 'TL Name', 'Sn Team Lead', 'AM Name', 'LOB', 'Status', 'Batch No', 'DESIGNATION', 'EMP Type', 'DOJ', 'Tenure Days', 'Shift', 'Attendance', 'Traget Login HRs', 'CC Login Hrs', 'SF Login HRs', 'Total Login', 'Present day'];
            $missingKeys = array_diff($requiredKeys, array_keys($record));
            // if (!empty($missingKeys)) {
            //     Log::warning('Missing keys in record: ' . json_encode($missingKeys));
            //     continue; // Skip record with missing keys
            // }

            // Fetch user_id from employee_details based on emp_id
            $employee = DB::table('employee_details')->where('employee_id', $record['EMP ID'])
                                                    ->first();
            // return $employee;
            if (!$employee) {
                throw new ModelNotFoundException("Employee with EMP ID {$record['EMP ID']} not found in employee_details.");
            }

            // Check for existing record by date and user_id
            $existingRecord = AgentAttendance::where('date', \DateTime::createFromFormat('l, F d, Y', $record['Date'])->format('Y-m-d'))
                ->where('user_id', $employee->user_id)
                ->first();
                
            return $record;

            $attendanceRecord = [
                'week' => $record['Week'],
                'ucid' => $record['UCID'],
                'sr_no' => (int)$record['Sr No'],
                'date' => \DateTime::createFromFormat('l, F d, Y', $record['Date'])->format('Y-m-d'),
                'center' => $record['Center'],
                'user_id' => $employee->user_id,
                'name_of_employee' => $record['NAME OF EMPLOYEES'],
                'email_id' => $record['Email ID'],
                'tl_name' => $record['TL Name'],
                'sn_team_lead' => $record['Sn Team Lead'],
                'am_name' => $record['AM Name'],
                'lob' => $record['LOB'],
                'status' => $record['Status'],
                'batch_no' => $record['Batch No'],
                'designation' => $record['DESIGNATION'],
                'emp_type' => $record['EMP Type'],
                'shift' => $record['Shift'],
                'attendance' => $record['Attendance'],
                'target_login_hrs' => $record['Traget Login HRs'],
                'cc_login_hrs' => $record['CC Login Hrs'],
                'sf_login_hrs' => $record['SF Login HRs'],
                'total_login' => $record['Total Login'],
                'present_day' => (int)$record['Present day'],
                'updated_at' => now(),
            ];

            Log::info('Prepared attendance record: ' . json_encode($attendanceRecord));

            if ($existingRecord) {
                Log::info('Updating existing record for user_id: ' . $employee->user_id . ' on date: ' . $attendanceRecord['date']);
                $existingRecord->update($attendanceRecord);
            } else {
                Log::info('Adding new record for user_id: ' . $employee->user_id . ' on date: ' . $attendanceRecord['date']);
                $attendanceRecord['created_at'] = now();
                $attendanceData[] = $attendanceRecord;
            }
        }

        // Insert new records in chunk
        return $attendanceData;
        
        if (!empty($attendanceData)) {
            Log::info('Inserting ' . count($attendanceData) . ' new records');
            $result = AgentAttendance::insert($attendanceData);
            Log::info('Insert result: ' . json_encode($result));
        } else {
            Log::info('No new records to insert in this chunk');
        }
    }

    


    
    
    
    
}
     