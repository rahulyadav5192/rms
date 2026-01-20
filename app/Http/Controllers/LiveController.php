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
use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Support\Facades\Storage;


class LiveController extends Controller
{
    
    
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.leaves';
        
    }

    
    
    public function policy_change()
    {
       
        // dd('gafdsuc');
        $this->policy = DB::table('policies')->where('trash_status',0)->get();
            return view('/police_create',$this->data);
        
    } 
    public function all_machine()
    {
       
        // dd('gafdsuc');
        $data = DB::table('bio_machine')->orderBy('status','ASC')->where('trash_status',0)->get();
        return $data;
        
    } 
    public function deactive_machine($ip)
    {
       
        // dd('gafdsuc');
        // $data = DB::table('bio_machine')->where('id',$ip)->update([
        //     'status' => 1,
        //     ]);
        // return $data;
        
    } 
    
    public function active_machine($ip)
    {
       
        // dd('gafdsuc');
        // $data = DB::table('bio_machine')->where('id',$ip)->update([
        //     'status' => 0,
        //     ]);
        // return $data;
        
    }
    
    public function rating($rate)
    {
       
        // dd('gafdsuc');
        $data = DB::table('users')->where('id',user()->id)->update([
            'niftel_rate' => $rate,
            ]);
        return $rate;
        
    }
    
    public function attendence_shift() {
        // current date
        $arr  = rand(1,4); 
        
        if($arr !=3 ){
            $todayDate = Carbon::now()->format('Y-m-d');
        }else{
            $todayDate = Carbon::now()->subDay(1)->format('Y-m-d');
        }
        $employee = DB::table('employee_shift_schedules')
            ->whereDate('employee_shift_schedules.date', $todayDate)
            ->join('employee_details', 'employee_details.user_id', 'employee_shift_schedules.user_id')
            ->join('bio_machine', 'bio_machine.id', 'employee_details.bio_machine_id')
            ->join('employee_shifts', 'employee_shifts.id', 'employee_shift_schedules.employee_shift_id')
            ->whereNotNull('employee_details.bio_machine_id')
            ->where('bio_machine.trash_status', 0)
            ->whereNotNull('employee_details.bio_uid')
            ->select('employee_details.*','employee_shifts.*','employee_shift_schedules.*','bio_machine.*','bio_machine.id as bioID')
            // ->where('employee_details.user_id',30)
            ->inRandomOrder()
            ->get();
    
        // return $employee;
        $date = $todayDate;
        
        // return $employee;
        foreach ($employee as $emp) {
            
            echo "Working On ". $emp->employee_id;
            
            
            // Assuming $date is the date you want and $emp is the employee object
            $start_time = $date . ' ' . $emp->office_start_time;
            $end_time = $date . ' ' . $emp->office_end_time;
            
            // Create Carbon DateTime objects
            $start_datetime = Carbon::parse($start_time);
            $end_datetime = Carbon::parse($start_time);
            
            // Adjust the time range if needed (in this example, subtract 4 hours from start and add 15 hours to end)
            $start_datetime->subHours(4);
            $end_datetime->addHours(14);
            
            // Fetch data from the 'att_temp' table for the specified time range
            $tempData = DB::table('att_temp')
                ->where('id', $emp->bio_uid)
                ->where('ip', $emp->bioID)
                ->whereBetween('timestamp', [
                    $start_datetime->toDateTimeString(),
                    $end_datetime->toDateTimeString(),
                ])
                ->get();
            // return $tempData;
            if(count($tempData) == 0){
                continue;
            }
            // Sort the $tempData by timestamp
            $tempData = $tempData->sortBy('timestamp');
            
            // Initialize clock_in and clock_out
            $clock_in = null;
            $clock_out = null;
            
            foreach ($tempData as $temp) {
                if ($clock_in === null) {
                    // First record represents login time
                    $clock_in = $temp->timestamp;
                }
                
                // Last record represents logout time
                $clock_out = $temp->timestamp;
            }
            
            // Check if there is only one record (no logout time)
            if (count($tempData) == 1) {
                $clock_out = null;
            }
            
            $attendance = DB::table('attendances')
                ->where('user_id', $emp->user_id)
                ->where('date', $todayDate)
                ->first();
            
            if ($attendance !== null) {
                // Update existing attendance record
                DB::table('attendances')
                    ->where('id', $attendance->id)
                    ->update([
                        'clock_in_time' => $clock_in,
                        'clock_out_time' => $clock_out,
                    ]);
            } else {
                // Insert new attendance record
                // ... (your existing logic for late, half_day, etc.)
                DB::table('attendances')->insert([
                    'user_id' => $emp->user_id,
                    'company_id' => 1,
                    'location_id' => 1,
                    'clock_in_time' => $clock_in,
                    'clock_out_time' => $clock_out,
                    'date' => $todayDate,
                    'shift_start_time' => $start_time,
                    'shift_end_time' => $end_time,
                    'employee_shift_id' => $emp->employee_shift_id,
                    'late' => 'no',
                    'work_from_type' => 'office',
                    'half_day' => 'no',
                ]);
            }

        }
    }

    
    public function export_csv(){
        
         $addPermission = user()->permission('add_employees');
        abort_403(!in_array($addPermission, ['all']));
        
        //      $users = DB::table('users')
        // ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
        // ->leftJoin('attendances', function ($join) {
        //     $join->on('users.id', '=', 'attendances.user_id')
        //         ->whereBetween('attendances.date', [now()->subDays(12), now()]);
        // })
        // ->where('employee_details.bio_machine_id', 2)
        // ->groupBy('users.id')
        // ->havingRaw('COUNT(attendances.id) < 5 OR COUNT(attendances.id) IS NULL')
        // ->get();
        
        
        

// $startDate = '2024-06-01';
// $endDate = '2024-06-30';

// $users = DB::table('users')
//     ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
//     ->join('branches', 'employee_details.branch_id', '=', 'branches.id')
//     ->join('teams', 'employee_details.department_id', '=', 'teams.id')
//     ->join('designations', 'employee_details.designation_id', '=', 'designations.id')
//     ->leftJoin('attendances', function ($join) use ($startDate, $endDate) {
//         $join->on('users.id', '=', 'attendances.user_id')
//             ->whereBetween('attendances.date', [$startDate, $endDate]);
//     })
//     ->select(
//         'employee_details.employee_id',
//         'users.name as emp_name',
//         'branches.name as branch_name',
//         'teams.team_name as department_name',
//         'designations.name as designation_name',
//         DB::raw('COUNT(attendances.id) as days_attended')
//     )
//     ->whereNotNull('employee_details.bio_machine_id')
//     ->whereNotNull('employee_details.bio_uid')
//     ->where('users.status','active')
//     ->where('employee_details.branch_id',1)
//     ->groupBy('users.id')
//     ->havingRaw('COUNT(attendances.id) < 15') 
//     ->get();

// $filename = "attendance_report_june_2024_final.csv";
// $handle = fopen($filename, 'w+');
// fputcsv($handle, ['Emp Name', 'Emp Id', 'Branch Name', 'Department', 'Designation', 'No of days attended']);

// foreach ($users as $user) {
//     fputcsv($handle, [
//         $user->emp_name,
//         $user->employee_id,
//         $user->branch_name,
//         $user->department_name,
//         $user->designation_name,
//         $user->days_attended
//     ]);
// }

// fclose($handle);

// // Provide a link to download the file
// echo "<a href='$filename'>Download CSV</a>";
// die();
                //  return;
            // return count($users);
        

        
        
        // Create a temporary table to store the results
        // DB::statement("
        //     CREATE TEMPORARY TABLE temp_export AS
        //     SELECT
        //         @row_number := @row_number + 1 AS 'Sr no',
        //         d.team_name AS 'Department Name',
        //         ed.employee_id AS 'Employee Id',
        //         u.name AS 'User Name'
        //     FROM
        //         (SELECT @row_number := 0) AS row_number_init
        //     CROSS JOIN
        //         users u
        //     JOIN
        //         employee_details ed ON u.id = ed.user_id
        //     JOIN
        //         teams d ON ed.department_id = d.id
        //     LEFT JOIN
        //         attendances a ON u.id = a.user_id AND a.date BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE()
        //     WHERE 
        //         ed.bio_machine_id = 2
        //         AND u.status = 'active'
        //         AND ed.branch_id = 1
        //     GROUP BY u.id
        //     HAVING COUNT(a.id) = 0 
        //     ORDER BY d.id, u.id;


        // ");
        
        // to fetch data of that employees whoes name is not registered 
        
        // DB::statement("
        //     CREATE TEMPORARY TABLE temp_export AS
        //     SELECT
        //         @row_number := @row_number + 1 AS 'Sr no',
        //         d.team_name AS 'Department Name',
        //         ed.employee_id AS 'Employee Id',
        //         u.name AS 'User Name'
        //     FROM
        //         (SELECT @row_number := 0) AS row_number_init
        //     CROSS JOIN
        //         users u
        //     JOIN
        //         employee_details ed ON u.id = ed.user_id
        //     JOIN
        //         teams d ON ed.department_id = d.id
        //     LEFT JOIN
        //         attendances a ON u.id = a.user_id AND a.date BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE()
        //     WHERE 
        //         ed.bio_machine_id = NULL
        //         AND u.status = 'active'
        //         AND ed.branch_id = 1
        //     GROUP BY u.id
        //     HAVING COUNT(a.id) = 0 
        //     ORDER BY d.id, u.id;


        // ");
        
        // CREATE TEMPORARY TABLE temp_export AS
        //     SELECT  b.name,d.name AS designation, u.username, u.name AS user_name, ed.date_of_birth, ed.joining_date
        //     FROM users u
        //     JOIN employee_details ed ON u.id = ed.user_id
        //     JOIN designations d ON ed.designation_id = d.id
        //     JOIN branches b ON ed.branch_id = b.id
        //     WHERE u.status = 'active' AND ed.designation_id NOT IN (2, 12, 15);
        
        
        DB::statement("
            CREATE TEMPORARY TABLE temp_export AS
            SELECT 
                u.username,
                t.team_name AS department,
                d.name AS designation,
                u.name AS user_name,
                CASE
                    WHEN SUM(CASE WHEN edocs.name LIKE '%Post Graduation Marksheet%' THEN 1 ELSE 0 END) > 0 THEN 'Post Graduation'
                    WHEN SUM(CASE WHEN edocs.name LIKE '%Graduation Marksheet%' THEN 1 ELSE 0 END) > 0 THEN 'Graduation'
                    WHEN SUM(CASE WHEN edocs.name LIKE '%12th Marksheet%' THEN 1 ELSE 0 END) > 0 THEN '12th'
                    WHEN SUM(CASE WHEN edocs.name LIKE '%10th Marksheet%' THEN 1 ELSE 0 END) > 0 THEN '10th'
                    ELSE NULL
                END AS education,
                ed.date_of_birth,
                ed.joining_date,
                ed.local_add,
                ed.local_city,
                ed.local_state,
                ed.local_pin,
                ed.per_add,
                ed.per_city,
                ed.per_state,
                ed.per_code,
                ed.aadhar_no,
                ed.pan_no,
                b.name AS branch_name,
                u.email,
                u.mobile,
                ed.alt_contact_no AS alternate_Mobile,
                u.gender,
                CASE WHEN u.form_filled = 1 THEN 'No' ELSE 'Yes' END AS form_filled,
                CASE
                    WHEN COUNT(edocs.id) < 1 THEN 'Not Uploaded'
                    WHEN SUM(CASE WHEN edocs.name LIKE '%Adhar Card%' THEN 1 ELSE 0 END) = 0
                         OR SUM(CASE WHEN edocs.name LIKE '%Pan Card%' THEN 1 ELSE 0 END) = 0
                         OR SUM(CASE WHEN edocs.name LIKE '%10th Marksheet%' THEN 1 ELSE 0 END) = 0
                         OR SUM(CASE WHEN edocs.name LIKE '%12th Marksheet%' THEN 1 ELSE 0 END) = 0
                         OR SUM(CASE WHEN edocs.name LIKE '%Passport Size Photo%' THEN 1 ELSE 0 END) = 0
                         OR SUM(CASE WHEN edocs.name LIKE '%Acknowledgment Letter%' THEN 1 ELSE 0 END) = 0
                    THEN 'Partial'
                    ELSE 'Full'
                END AS document_status,
                CASE 
                    WHEN eb.user_id IS NOT NULL THEN 'Yes'
                    ELSE 'No'
                END AS account_details,
                GROUP_CONCAT(edocs.name SEPARATOR ', ') AS documents,
                
                -- New columns from employee_bank_details
                eb.bank_name,
                eb.account_number,
                eb.ifsc_code,
                eb.branch_name AS bank_branch_name,
                eb.account_type
        
            FROM users u
            JOIN employee_details ed ON u.id = ed.user_id
            JOIN branches b ON ed.branch_id = b.id 
            JOIN teams t ON t.id = ed.department_id
            JOIN designations d ON ed.designation_id = d.id
            LEFT JOIN employee_docs edocs ON u.id = edocs.user_id
            LEFT JOIN employee_bank_details eb ON u.id = eb.user_id
            WHERE u.status = 'active'
            GROUP BY 
                u.username, u.name, ed.date_of_birth, ed.joining_date, b.id,
                eb.bank_name, eb.account_number, eb.ifsc_code, eb.branch_name, eb.account_type;
        ");

        
        /// Define the path where you want to save the CSV file
        $filePath = '/home/ncpl/rms.niftel.com/public/export.csv';

        // Open file pointer
        $file = fopen($filePath, 'w');
        
        // Check if file was successfully opened
        if (!$file) {
            // Handle error here
            echo "Failed to open file.";
        } else {
            $data = DB::table('temp_export')->get()->toArray();
        
    
            $headersMapping = [
                'username' => 'Username',
                'department' => 'Department',
                'designation' => 'Designation',
                'user_name' => 'Full Name',
                'education' => 'Education',
                'date_of_birth' => 'Date of Birth',
                'joining_date' => 'Joining Date',
                'local_add' => 'Local Address',
                'local_city' => 'Local City',
                'local_state' => 'Local State',
                'local_pin' => 'Local Pincode',
                'per_add' => 'Permanent Address',
                'per_city' => 'Permanent City',
                'per_state' => 'Permanent State',
                'per_code' => 'Permanent Pincode',
                'aadhar_no' => 'Aadhar Number',
                'pan_no' => 'PAN Number',
                'branch_name' => 'Branch Name',
                'email' => 'Email',
                'mobile' => 'Mobile Number',
                'alternate_Mobile' => 'Alternate Mobile',
                'gender' => 'Gender',
                'form_filled' => 'Form Filled',
                'document_status' => 'Document Status',
                'account_details' => 'Account Details',
                'documents' => 'Uploaded Documents',
                'bank_name' => 'Bank Name',
                'account_number' => 'Account Number',
                'ifsc_code' => 'IFSC Code',
                'bank_branch_name' => 'Bank Branch Name',
                'account_type' => 'Account Type'
            ];
            
            // Create temporary file
            $filename = '/tmp/emp_data.csv';
            $handle = fopen($filename, 'w+');
            
            // Use headers mapping for CSV header row
            fputcsv($handle, array_values($headersMapping));
            
            // Write data rows
            foreach ($data as $row) {
                $rowArray = (array) $row;
                $formattedRow = [];
            
                foreach (array_keys($headersMapping) as $key) {
                    $formattedRow[] = $rowArray[$key] ?? '';
                }
            
                fputcsv($handle, $formattedRow);
            }
            
            fclose($handle);
            
            // Set response headers for download
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="employee_export.csv"',
            ];
            
            // Return response
            $response = Response::make(file_get_contents($filename), 200, $headers);
            
            // Remove temporary file
            unlink($filename);
            
            return $response;
        }
    }
    
    
    
    // dpwnload inactive people 
    
    public function export_inactive(){
        DB::statement("
            CREATE TEMPORARY TABLE temp_export AS
            SELECT u.username,
                   t.team_name AS department,
                   d.name AS designation,
                   u.name AS user_name,
                   ed.date_of_birth,
                   ed.bio_machine_id,
                   ed.joining_date,
                   b.name AS branch_name,
                   u.email,
                   u.mobile,
                   ed.alt_contact_no AS alternate_Mobile,
                   u.gender,
                   CASE WHEN u.form_filled = 1 THEN 'No' ELSE 'Yes' END AS form_filled,
                   CASE
                       WHEN COUNT(edocs.id) < 1 THEN 'Not Uploaded'
                       WHEN SUM(CASE WHEN edocs.name LIKE '%Adhar Card%' THEN 1 ELSE 0 END) = 0
                            OR SUM(CASE WHEN edocs.name LIKE '%Pan Card%' THEN 1 ELSE 0 END) = 0
                            OR SUM(CASE WHEN edocs.name LIKE '%10th Marksheet%' THEN 1 ELSE 0 END) = 0
                            OR SUM(CASE WHEN edocs.name LIKE '%12th Marksheet%' THEN 1 ELSE 0 END) = 0
                            OR SUM(CASE WHEN edocs.name LIKE '%Passport Size Photo%' THEN 1 ELSE 0 END) = 0
                            THEN 'Partial'
                       ELSE 'Full'
                   END AS document_status,
                   GROUP_CONCAT(edocs.name SEPARATOR ', ') AS documents
            FROM users u
            JOIN employee_details ed ON u.id = ed.user_id
            JOIN branches b ON ed.branch_id = b.id
            JOIN teams t ON t.id = ed.department_id
            JOIN designations d ON ed.designation_id = d.id
            LEFT JOIN employee_docs edocs ON u.id = edocs.user_id
            WHERE u.status = 'deactive'
            
                
            GROUP BY u.username, u.name, ed.date_of_birth, ed.joining_date, b.id;

        ");
        
        

        /// Define the path where you want to save the CSV file
        $filePath = '/home/ncpl/rms.niftel.com/public/export.csv';

        // Open file pointer
        $file = fopen($filePath, 'w');
        
        // Check if file was successfully opened
        if (!$file) {
            // Handle error here
            echo "Failed to open file.";
            die();
        } else {
            $data = DB::table('temp_export')->get()->toArray();
            
    
        // Fetch the data from the temporary table
        $data = DB::table('temp_export')->get()->toArray();
        // return count($data);
        // Export the data to a CSV file
        $filename = '/tmp/emp_date.csv';
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array_keys((array) $data[0]));
    
        foreach ($data as $row) {
            fputcsv($handle, (array) $row);
        }
    
        fclose($handle);
    
        // Set the headers for the response
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="emp_date.csv"',
        );
    
        // Create a response object with the raw file content
        $response = Response::make(file_get_contents($filename), 200, $headers);
    
        // Clean up the temporary file
        unlink($filename);
    
        return $response;
    }
    }
    
    
    public function all_temp()
    { 
        ini_set('memory_limit', '512M'); // Increase memory limit
        set_time_limit(300);
    
        echo 'Formatting data to insert...';
    
        // Fetch a random machine record
        $all = DB::table('bio_machine')
            ->where('status', 0)
            ->where('trash_status', 0)
            ->inRandomOrder()
            ->first();
    
        if (!$all) {
            echo '<br>No machine found';
            return;
        }
    
        // Fetch data from API
        $response = Http::post('https://rest.niftel.com/api/all_data/'.$all->ip);
        $statusCode = $response->status();
        $body = $response->body();
        
        // return $all;
    
        // Decode JSON response
        $data = json_decode($body, true);
    
        if (empty($data)) {
            echo '<br>No data found from API';
            return;
        }
    
        echo '<br>Data fetched, total records: ' . count($data);
    
        // Define chunk size to avoid SQL limit errors
        $chunkSize = 1000;
    
        // Break data into smaller chunks
        $dataChunks = array_chunk($data, $chunkSize);
    
        $totalInserted = 0;
    
        DB::beginTransaction();  // Start transaction
    
        try {
            foreach ($dataChunks as $chunkIndex => $chunk) {
                echo "<br>Processing chunk " . ($chunkIndex + 1) . " of " . count($dataChunks);
    
                // Fetch existing records for this chunk
                $existingRecords = DB::table('att_temp')
                    ->whereIn('uid', array_column($chunk, 'uid'))
                    ->whereIn('id', array_column($chunk, 'id'))
                    ->where('ip', $all->id)
                    ->get();
    
                // Filter out records that already exist
                $dataToInsert = array_filter($chunk, function ($record) use ($existingRecords) {
                    return !$existingRecords->contains(function ($existingRecord) use ($record) {
                        return $existingRecord->uid == $record['uid'] && $existingRecord->id == $record['id'];
                    });
                });
    
                // Prepare data for bulk insert
                $dataToInsertFormatted = [];
                foreach ($dataToInsert as $record) {
                    $dataToInsertFormatted[] = [
                        'ip' => $all->id,
                        'uid' => $record['uid'],
                        'id' => $record['id'],
                        'timestamp' => $record['timestamp'],
                    ];
                }
    
                // Insert the chunk if there's new data
                if (!empty($dataToInsertFormatted)) {
                    DB::table('att_temp')->upsert(
                        $dataToInsertFormatted,
                        ['ip', 'uid', 'id'], // Unique constraints
                        ['timestamp'] // Columns to update if record exists
                    );
    
                    $totalInserted += count($dataToInsertFormatted);
                }
            }
    
            DB::commit();  // Commit the transaction
            echo "<br>Finished — Total records inserted: $totalInserted";
        } catch (\Exception $e) {
            DB::rollBack();  // Rollback on error
            \Log::error('Error in all_temp function: ' . $e->getMessage());
            echo '<br>Error occurred: ' . $e->getMessage();
        }
    }
    
    



    
    
    
    // flexi from db 
    public function flexi_db(){
        
        ini_set('max_execution_time', 15000);
        $arr  = rand(1,5);  
        if($arr !=3 ){
            $todayDate = Carbon::now()->format('Y-m-d');
        }else{
            $todayDate = Carbon::now()->subDay(1)->format('Y-m-d');
        }
        
        $employee = DB::table('employee_details')
                    ->join('bio_machine','bio_machine.id','employee_details.bio_machine_id')
                    ->whereNotNull('employee_details.bio_machine_id')
                    // ->where('bio_machine.status',0)
                    // ->where('employee_details.user_id',1968)
                    ->whereNotNull('employee_details.bio_uid')
                    ->leftJoin('employee_shift_schedules', function($join) use ($todayDate) {
                        $join->on('employee_shift_schedules.user_id', '=', 'employee_details.user_id')
                             ->whereDate('employee_shift_schedules.date', $todayDate);
                    })
                    ->whereNull('employee_shift_schedules.user_id')
                    ->inRandomOrder()
                    ->select('employee_details.*','bio_machine.*','bio_machine.id as bioID')
                    ->get();
        // return $employee;
        $date = $todayDate;
        
        foreach($employee as $emp){
            
            
            
            echo "Working On ". $emp->employee_id;
            // dd();
            $data = [
                'emp_id' => $emp->bio_uid,
                'date' => $date,
                'ip' => $emp->ip
            ];
        
            // $response = Http::post('https://sky.udtahathi.bioIDxyz/api/get_atte', $data);
            // $statusCode = $response->status();
            // $body = $response->body();
            // return $body;
            $body = DB::table('att_temp')->where('ip',$emp->bioID)->where('id',$emp->bio_uid)->whereDate('timestamp',$date)->orderBy('uid','ASC')->get();
            // return $body;
            
                // data found and working on clock in clock out 
                
                $data = $body->toArray();
                $datacount = count($data);
                // return $datacount; 
                if($datacount == 0){
                    // return ss;
                    continue;
                } 
                // return 'ss';
                $lastElement = end($data);
                $firstElement = reset($data);
                // return $lastElement;
                $clock_in = $firstElement->timestamp;
                $clock_out = $lastElement->timestamp;
                $attenence = DB::table('attendances')->where('user_id',$emp->user_id)->where('date', $todayDate)->first();
                
                if($attenence != NULL){
                    if($datacount >= 2){
                    
                        $update = DB::table('attendances')->where('id',$attenence->id)->update([
                            // 'user_id' => $emp->user_id,
                            // 'clock_in_time' => $clock_in,
                            'clock_out_time' => $clock_out,
                            // 'shift_start_time' => $start_time,
                            // 'shift_end_time' => $end_time
                        ]);
                     }
                    
                }else{
                    $clockInTime = Carbon::parse($clock_in)->format('H:i:s'); // Extract time from the timestamp
                    $startTime = '';
                    $late = 'no'; // Default value, assuming the employee is not late
                    
                    if (isset($emp->late_mark_duration) && $emp->late_mark_duration > 0) {
                        $clockInParsed = Carbon::parse($clockInTime);
                        $startTimeParsed = Carbon::parse($startTime);
                        $lateMinutes = $startTimeParsed->diffInMinutes($clockInParsed, false); // Calculate the difference in minutes
                        if ($lateMinutes > $emp->late_mark_duration) {
                            $late = 'yes';
                        }
                    }

                    
                    
                  $clockInTime = substr($clock_in, 11, 8); // Extracts '09:20:15' from '2023-11-01 09:20:15'

                  $halfday = 'no'; // Default value, assuming the user is not logging in after half-day mark time
 
                  // if (!is_null($emp->halfday_mark_time)) {
                  //     $halfDayMarkTime = substr($emp->halfday_mark_time, 0, 8); // Extracts time portion from halfday_mark_time

                  //     if ($clockInTime >= $halfDayMarkTime) {
                  //         // User has checked in after or at the half-day mark time 
                  //         $halfday = 'yes';
                  //     }
                  // }
                  
                  if($datacount <= 1){
                      $clock_out = NULL;
                  }

                    $update = DB::table('attendances')->insert([
                        'user_id' => $emp->user_id,
                        'company_id' =>1,
                        'location_id' =>1,
                        'clock_in_time' => $clock_in,
                        'clock_out_time' => $clock_out,
                        'date' => $todayDate,
                        'late' => $late,
                        'work_from_type'=>'office',
                        'half_day'=>$halfday,
                    ]);
                }
                
            
            // return $body;
            
        }
        
    }
    
    
    
    public function flexi_db2() {
    ini_set('max_execution_time', 60); // Aim to finish within 60 seconds

    // Determine the date based on random value
    $arr = rand(1, 5);
    $todayDate = $arr != 3 ? Carbon::now()->format('Y-m-d') : Carbon::now()->subDay(1)->format('Y-m-d');
    echo "Processing date: $todayDate\n";

    // Step 1: Fetch all employees in one query
    $employees = DB::table('employee_details')
        ->join('bio_machine', 'bio_machine.id', 'employee_details.bio_machine_id')
        ->whereNotNull('employee_details.bio_machine_id')
        ->whereNotNull('employee_details.bio_uid')
        ->leftJoin('employee_shift_schedules', function ($join) use ($todayDate) {
            $join->on('employee_shift_schedules.user_id', '=', 'employee_details.user_id')
                 ->whereDate('employee_shift_schedules.date', $todayDate);
        })
        ->whereNull('employee_shift_schedules.user_id')
        ->inRandomOrder()
        ->select('employee_details.*', 'bio_machine.*', 'bio_machine.id as bioID')
        ->get()
        ->keyBy('user_id'); // Index by user_id for faster lookups

    if ($employees->isEmpty()) {
        echo "No employees to process.\n";
        return;
    }

    // Step 2: Fetch att_temp data for all employees, ordered by uid ASC
    $attendanceDataRaw = DB::table('att_temp')
        ->whereIn('ip', $employees->pluck('bioID')->unique()->toArray())
        ->whereIn('id', $employees->pluck('bio_uid')->toArray())
        ->whereDate('timestamp', $todayDate)
        ->orderBy('uid', 'ASC')
        ->get();

    if ($attendanceDataRaw->isEmpty()) {
        echo "No attendance data found.\n";
        return;
    }

    // Step 3: Group att_temp data by bio_uid and calculate clock_in, clock_out, punch_count
    $attendanceData = [];
    foreach ($attendanceDataRaw as $record) {
        $bio_uid = $record->id; // bio_uid is mapped to 'id' in att_temp
        if (!isset($attendanceData[$bio_uid])) {
            $attendanceData[$bio_uid] = [
                'bioID' => $record->ip,
                'records' => [],
            ];
        }
        $attendanceData[$bio_uid]['records'][] = $record;
    }

    // Calculate clock_in, clock_out, and punch_count for each bio_uid
    foreach ($attendanceData as $bio_uid => &$data) {
        $records = $data['records'];
        $data['punch_count'] = count($records);
        $data['clock_in'] = $records[0]->timestamp; // First record (sorted by uid ASC)
        $data['clock_out'] = $records[count($records) - 1]->timestamp; // Last record
    }
    unset($data); // Clean up reference

    // Step 4: Fetch existing attendance records
    $existingAttendances = DB::table('attendances')
        ->whereIn('user_id', $employees->pluck('user_id')->toArray())
        ->where('date', $todayDate)
        ->get()
        ->keyBy('user_id'); // Index by user_id for faster lookups

    // Step 5: Prepare bulk insert and update data
    $inserts = [];
    $updates = [];

    foreach ($employees as $emp) {
        echo "Working On " . $emp->employee_id . "\n";

        $bio_uid = $emp->bio_uid;
        if (!isset($attendanceData[$bio_uid])) {
            continue; // Skip if no attendance data for this employee
        }

        $attData = $attendanceData[$bio_uid];
        $clock_in = $attData['clock_in'];
        $clock_out = $attData['punch_count'] >= 2 ? $attData['clock_out'] : null;
        $punch_count = $attData['punch_count'];

        // Check if attendance record exists
        $attendance = $existingAttendances[$emp->user_id] ?? null;

        if ($attendance) {
            // Update existing record if there are 2 or more punches
            if ($punch_count >= 2) {
                $updates[] = [
                    'id' => $attendance->id,
                    'clock_out_time' => $clock_out,
                ];
            }
        } else {
            // Prepare new attendance record
            $clockInTime = Carbon::parse($clock_in)->format('H:i:s');
            $startTime = '';
            $late = 'no';

            // Calculate late status
            if (isset($emp->late_mark_duration) && $emp->late_mark_duration > 0) {
                $clockInParsed = Carbon::parse($clockInTime);
                $startTimeParsed = Carbon::parse($startTime);
                $lateMinutes = $startTimeParsed->diffInMinutes($clockInParsed, false);
                if ($lateMinutes > $emp->late_mark_duration) {
                    $late = 'yes';
                }
            }

            $clockInTime = substr($clock_in, 11, 8);
            $halfday = 'no';

            if ($punch_count <= 1) {
                $clock_out = null;
            }

            $inserts[] = [
                'user_id' => $emp->user_id,
                'company_id' => 1,
                'location_id' => 1,
                'clock_in_time' => $clock_in,
                'clock_out_time' => $clock_out,
                'date' => $todayDate,
                'late' => $late,
                'work_from_type' => 'office',
                'half_day' => $halfday,
            ];
        }
    }

    // Step 6: Perform bulk insert and update
    if (!empty($inserts)) {
        DB::table('attendances')->insert($inserts);
        echo "Inserted " . count($inserts) . " new attendance records.\n";
    }

    if (!empty($updates)) {
        foreach ($updates as $update) {
            DB::table('attendances')
                ->where('id', $update['id'])
                ->update([
                    'clock_out_time' => $update['clock_out_time'],
                ]);
        }
        echo "Updated " . count($updates) . " existing attendance records.\n";
    }
}
    
    
    
    
    
    
    
    
    
    public function emp_id(){
        $machine = DB::table('bio_machine')->where('trash_status',0)->inRandomOrder()->first();
        $data = [
                'ip' => $machine->ip
            ];
        $response = Http::post('https://rest.niftel.com/api/get_all', $data);
        $statusCode = $response->status();
        $body = $response->body();
        // return $body;
        $data = json_decode($body, true);
        if(is_null($data)){
            return;
        }
        foreach($data as $d){
            $emp_id = $d['name'];
            $emp_detail = DB::table('employee_details')->where('employee_id',$emp_id)->update([
                'bio_machine_id' => $machine->id,
                'bio_uid' => $d['userid']
                ]);
        }
    }
    
    
    public function flexi(){
        
          ini_set('max_execution_time', 15000);
        //  dispatch(new attendence_prev())->timeout(15 * 60);
        // current date 
        // $todayDate = now()->toDateString();
        $arr  = rand(1,4); 
        return $arr;
        if($arr !=3 ){
            $todayDate = Carbon::now()->format('Y-m-d');
        }else{
            $todayDate = Carbon::now()->subDay(1)->format('Y-m-d');
        }
        return $todayDate;
        $employee = DB::table('employee_details')
                    ->join('bio_machine','bio_machine.id','employee_details.bio_machine_id')
                    ->whereNotNull('employee_details.bio_machine_id')
                    ->where('bio_machine.status',0)
                    // ->where('employee_details.user_id',186)
                    ->whereNotNull('employee_details.bio_uid')
                    ->leftJoin('employee_shift_schedules', function($join) use ($todayDate) {
                        $join->on('employee_shift_schedules.user_id', '=', 'employee_details.user_id')
                             ->whereDate('employee_shift_schedules.date', $todayDate);
                    })
                    ->whereNull('employee_shift_schedules.user_id')
                    ->inRandomOrder()
                    ->select('employee_details.*','bio_machine.*')
                    ->get();
        // return count($employee);
        $date = $todayDate;
        
        foreach($employee as $emp){
            
            
            
            
            $data = [
                'emp_id' => $emp->bio_uid,
                'date' => $date,
                'ip' => $emp->ip
            ];
        
            $response = Http::post('https://rest.niftel.com/api/get_atte', $data);
            $statusCode = $response->status();
            $body = $response->body();
            // return $body;
            
            
            if ($statusCode == 200) {
                
                
                // data found and working on clock in clock out 
                
                $data = json_decode($body, true);
                $datacount = count($data);
                    
                if($datacount==0){
                    continue;
                }
                $firstElement = reset($data);
                $lastElement = end($data);
                
                $clock_in = $firstElement['timestamp'];
                $clock_out = $lastElement['timestamp'];
                $attenence = DB::table('attendances')->where('user_id',$emp->user_id)->where('date', $todayDate)->first();
                
                if($attenence != NULL){
                     if($datacount > 1){
                    
                    $update = DB::table('attendances')->where('id',$attenence->id)->update([
                        // 'user_id' => $emp->user_id,
                        // 'clock_in_time' => $clock_in,
                        'clock_out_time' => $clock_out,
                        // 'shift_start_time' => $start_time,
                        // 'shift_end_time' => $end_time
                        ]);
                     }
                    
                }else{
                   $clockInTime = Carbon::parse($clock_in)->format('H:i:s'); // Extract time from the timestamp
                    $startTime = '';
                    
                    $late = 'no'; // Default value, assuming the employee is not late
                    
                    if (isset($emp->late_mark_duration) && $emp->late_mark_duration > 0) {
                        $clockInParsed = Carbon::parse($clockInTime);
                        $startTimeParsed = Carbon::parse($startTime);
                    
                        $lateMinutes = $startTimeParsed->diffInMinutes($clockInParsed, false); // Calculate the difference in minutes
                        
                        if ($lateMinutes > $emp->late_mark_duration) {
                            $late = 'yes';
                        }
                    }

// return $late;
                    
                    
                  $clockInTime = substr($clock_in, 11, 8); // Extracts '09:20:15' from '2023-11-01 09:20:15'

                  $halfday = 'no'; // Default value, assuming the user is not logging in after half-day mark time
 
                  // if (!is_null($emp->halfday_mark_time)) {
                  //     $halfDayMarkTime = substr($emp->halfday_mark_time, 0, 8); // Extracts time portion from halfday_mark_time

                  //     if ($clockInTime >= $halfDayMarkTime) {
                  //         // User has checked in after or at the half-day mark time 
                  //         $halfday = 'yes';
                  //     }
                  // }
                  
                  if($datacount <= 1){
                      $clock_out = NULL;
                  }

                    $update = DB::table('attendances')->insert([
                        'user_id' => $emp->user_id,
                        'company_id' =>1,
                        'location_id' =>1,
                        'clock_in_time' => $clock_in,
                        // 'clock_out_time' => $clock_out,
                        'date' => $todayDate, 
                        'late' => $late,
                        'work_from_type'=>'office',
                        'half_day'=>$halfday,
                    ]);
                }
                
            } else {
                // Handle other status codes accordingly
            }
            // return $body;
            
        }
        
    }
    
    
    public function attendence(){
        
        
        // current date 
        $todayDate = now()->toDateString();
        // $todayDate = Carbon::now()->subDay()->format('Y-m-d');
        $employee = DB::table('employee_shift_schedules')->whereDate('employee_shift_schedules.date', $todayDate)
                    ->join('employee_details','employee_details.user_id','employee_shift_schedules.user_id')
                    ->join('bio_machine','bio_machine.id','employee_details.bio_machine_id')
                    ->join('employee_shifts','employee_shifts.id','employee_shift_schedules.employee_shift_id')
                    ->whereNotNull('employee_details.bio_machine_id')
                    // ->where('employee_details.user_id',235)
                    ->where('bio_machine.status',0)
                    ->whereNotNull('employee_details.bio_uid')
                    ->inRandomOrder()
                    ->get();
        // return $employee;
        $date = $todayDate;
        
        foreach($employee as $emp){
            
            
            
            // if($emp->bio_machine_id == 1){
            //     continue;
                
            // }
            // return $emp;
            $start_time = $emp->office_start_time;
            $end_time = $emp->office_end_time;
            $data = [
                'emp_id' => $emp->bio_uid,
                'date' => $date,
                'time' => $start_time,
                'ip' => $emp->ip
            ];
        
            $response = Http::post('https://rest.niftel.com/api/get_checkin', $data);
            $statusCode = $response->status();
            $body = $response->body();
            // return $body;
            
            
            if ($statusCode == 200) {
                
                // data found and working on clock in clock out 
                
                $data = json_decode($body, true);
                $datacount = count($data);
                    
                if($datacount==0){
                    continue;
                }
                $firstElement = reset($data);
                $lastElement = end($data);
                   
                $clock_in = $firstElement['timestamp'];
                $clock_out = $lastElement['timestamp'];
                $attenence = DB::table('attendances')->where('user_id',$emp->user_id)->where('date', $todayDate)->first();
                
                if($attenence != NULL){
                    
                     if($datacount > 1){
                    
                    $update = DB::table('attendances')->where('id',$attenence->id)->update([
                        'clock_out_time' => $clock_out,
                        ]);
                     }
                    
                }else{
                    //  return 'ca2';
                   $clockInTime = Carbon::parse($clock_in)->format('H:i:s'); // Extract time from the timestamp
                    $startTime = $start_time;
                    
                    $late = 'no'; // Default value, assuming the employee is not late
                    
                    if (isset($emp->late_mark_duration) && $emp->late_mark_duration > 0) {
                        $clockInParsed = Carbon::parse($clockInTime);
                        $startTimeParsed = Carbon::parse($startTime);
                    
                        $lateMinutes = $startTimeParsed->diffInMinutes($clockInParsed, false); // Calculate the difference in minutes
                        
                        if ($lateMinutes > $emp->late_mark_duration) {
                            $late = 'yes';
                        }
                    }

// return $late;
                    
                    
                  $clockInTime = substr($clock_in, 11, 8); // Extracts '09:20:15' from '2023-11-01 09:20:15'

                  $halfday = 'no'; // Default value, assuming the user is not logging in after half-day mark time
 
                  // if (!is_null($emp->halfday_mark_time)) {
                  //     $halfDayMarkTime = substr($emp->halfday_mark_time, 0, 8); // Extracts time portion from halfday_mark_time

                  //     if ($clockInTime >= $halfDayMarkTime) {
                  //         // User has checked in after or at the half-day mark time 
                  //         $halfday = 'yes';
                  //     }
                  // }
                    if($datacount <= 1){
                      $clock_out = NULL;
                    }
                    $update = DB::table('attendances')->insert([
                        'user_id' => $emp->user_id,
                        'company_id' =>1,
                        'location_id' =>1,
                        'clock_in_time' => $clock_in,
                        'clock_out_time' => $clock_out,
                        'date' => $todayDate,
                        'shift_start_time' => $start_time,
                        'shift_end_time' => $end_time,
                        'employee_shift_id' => $emp->employee_shift_id,
                        'late' => $late,
                        'work_from_type'=>'office',
                        'half_day'=>$halfday,
                    ]);
                }
                
                
            } else {
                // Handle other status codes accordingly
            }
            // return $body;
            
        }
    }
    
    
    
    
    // leave manager 
    
    public function head_leave_manage($team_id){
        
        $this->leave = DB::table('leaves')
                        ->join('employee_details', 'leaves.user_id', '=', 'employee_details.user_id')
                        ->join('users', 'users.id', '=', 'employee_details.user_id')
                        ->where('employee_details.department_id', $team_id)
                        ->select('leaves.*','employee_details.*', 'users.name as employee_name') // Include any other fields you want to display
                        ->get();
        return view('leave_manager',$this->data);
    }
    
    
    
    public function deactive2()
    {

        return count($data);

        $affectedRows = DB::table('users')
                        ->whereIn('username', $data)
                        ->update([
                            'status' => 'deactive',
                            'login' => 'disable',
                            // Add more columns and their corresponding new values as needed
                        ]);

    }
    
    
    public function deactive()
    {
        // Read the employee sheet file
        $filePath = public_path('inactive.csv'); // Path to your employee sheet file
        $file = fopen($filePath, 'r');

        // Check if file opened successfully
        if ($file) {
            $employeeIds = [];

            // Read each line of the file
            while (($data = fgetcsv($file)) !== false) {
                // Assuming the employee ID is in the first column (index 0)
                $employeeId = $data[0];
                
                // Add the employee ID to the array
                $employeeIds[] = $employeeId;
            }

            fclose($file);

            // $affectedRows = DB::table('users')
            //             ->whereIn('username', $employeeIds)
            //             ->get();
                        
            // return count($affectedRows);
            $update = DB::table('users')
                        ->whereIn('username', $employeeIds)
                        ->update([
                            'status' => 'deactive',
                            // 'login' => 'disable',
                        ]);
            return $update;
        } else {
            // Handle file opening error
            echo "Failed to open the employee sheet file.";
        }
        
        return 1;
        
        // deactive that employees that are not in the active.csv
        $filePath = public_path('active.csv'); // Path to your employee sheet file
        
        // Check if the file exists
        if (!file_exists($filePath)) {
            return "File not found: $filePath";
        }
        
        // Open the file
        $file = fopen($filePath, 'r');
        if (!$file) {
            return "Failed to open file: $filePath";
        }
        
        // Read employee IDs from the CSV
        $employeeIds = [];
        while (($data = fgetcsv($file)) !== false) {
            if (!empty($data[0])) { // Ensure the first column is not empty
                $employeeIds[] = trim($data[0]); // Trim any unnecessary whitespace
            }
        }
        fclose($file);
        
        // Check if we have valid employee IDs
        if (empty($employeeIds)) {
            return "No valid employee IDs found in the CSV file.";
        }
        
        // Departments to check
        $departments = [28, 18, 29, 31, 1];
        
        // Fetch employees from employee_details in the specified departments
        $employees = DB::table('employee_details')
            ->whereIn('department_id', $departments)
            ->get(['employee_id', 'user_id']);
        
        // Initialize counters
        $deactivatedCount = 0;
        
        // Update user statuses
        foreach ($employees as $employee) {
            if (in_array($employee->employee_id, $employeeIds)) {
                // Activate only if not already active
                DB::table('users')
                    ->where('id', $employee->user_id)
                    ->where('status', '!=', 'active') // Avoid unnecessary updates
                    ->update(['status' => 'active']);
            } else {
                // Deactivate only if not already deactive
                $updated = DB::table('users')
                    ->where('id', $employee->user_id)
                    ->where('status', '!=', 'deactive') // Avoid unnecessary updates
                    ->update(['status' => 'deactive']);
                
                if ($updated) {
                    $deactivatedCount++;
                }
            }
        }
        
        // Return the count of deactivated users
        return "Total users deactivated: $deactivatedCount";


    }
    
    public function updateWorkFromHome()
{
    // Read the WFH CSV file
    $filePath = public_path('wfh.csv'); // Path to your WFH CSV file
    
    // Check if the file exists
    if (!file_exists($filePath)) {
        return "File not found: $filePath";
    }
    
    // Open the file
    $file = fopen($filePath, 'r');
    if (!$file) {
        return "Failed to open file: $filePath";
    }
    
    // Read employee IDs from the CSV
    $employeeIds = [];
    while (($data = fgetcsv($file)) !== false) {
        if (!empty($data[0])) { // Ensure the first column is not empty
            $employeeIds[] = trim($data[0]); // Trim any unnecessary whitespace
        }
    }
    fclose($file);
    
    // Check if we have valid employee IDs
    if (empty($employeeIds)) {
        return "No valid employee IDs found in the CSV file.";
    }
    
    // Update employment_type to part_time for matching employee IDs
    $updatedRows = DB::table('employee_details')
        ->whereIn('employee_id', $employeeIds)
        ->where('employment_type', '!=', 'part_time') // Avoid unnecessary updates
        ->update([
            'employment_type' => 'part_time'
        ]);
    
    // Return the count of updated records
    return "Total employees updated to part_time: $updatedRows";
}
    
    
    public function upload_salary_sheet_store(Request $request){
        
        // Get the file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
    
        // Get the uploaded file
        $file = $request->file('file');
        $hasHeading = $request->has('hasHeading'); 
    
        // Read the Excel file data
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getPathname());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
    
        // Process the data
        $dataset = [];
        $startRow = $hasHeading ? 2 : 1; // Skip the heading if present
        $highestRow = $worksheet->getHighestRow();
        for ($rowIndex = $startRow; $rowIndex <= $highestRow; ++$rowIndex) {
            $employee_id = $worksheet->getCell('A' . $rowIndex)->getValue();
            $ctc_year = $worksheet->getCell('B' . $rowIndex)->getValue();
            $bank_name = $worksheet->getCell('C' . $rowIndex)->getValue();
            $account_number = $worksheet->getCell('D' . $rowIndex)->getValue();
    
            // Add data to array
            $dataset[$employee_id] = [
                'salary' => $ctc_year,
                'bank' => $bank_name,
                'account' => $account_number,
            ];
        }
     
        // Perform bulk update
        foreach ($dataset as $employee_id => $update) { 
            DB::table('employee_details')
                ->where('employee_id', $employee_id)
                ->update($update);
        }
    
        // Return success response
        return redirect()->back()->with('mess','Data Uploaded Successfully');
        
        // Return success response
        // return response()->json(['message' => 'Data uploaded successfully']);

    
    }
    
    
    
    
    
    
    // whole month
    
    public function attendance_shift_month() {
        // Get the current month and year
        $currentMonth = Carbon::now()->subDay(1)->format('m');
        $currentYear = Carbon::now()->format('Y');
    
        // Get the number of days in the current month
        $daysInMonth = Carbon::now()->daysInMonth;
    
        for ($day = 1; $day <= $daysInMonth; $day++) {
            // Create the date string for the current day
            $todayDate = Carbon::createFromDate($currentYear, $currentMonth, $day)->format('Y-m-d');
            
            // Echo the date to prevent timeout
            echo "Processing date: " . $todayDate . "\n";
    
            $employee = DB::table('employee_shift_schedules')
                ->whereDate('.date', $todayDate)
                ->join('employee_details', 'employee_details.user_id', 'employee_shift_schedules.user_id')
                ->join('bio_machine', 'bio_machine.id', 'employee_details.bio_machine_id')
                ->join('employee_shifts', 'employee_shifts.id', 'employee_shift_schedules.employee_shift_id')
                ->whereNotNull('employee_details.bio_machine_id')
                ->where('bio_machine.trash_status', 0)
                ->whereNotNull('employee_details.bio_uid')
                ->select('employee_details.*', 'employee_shifts.*', 'employee_shift_schedules.*', 'bio_machine.*', 'bio_machine.id as bioID')
                ->inRandomOrder()
                ->get();
    
            foreach ($employee as $emp) {
                // if($emp->employee_id != 'NIF0224148'){
                //     continue;
                // }
                echo "Working On ". $emp->employee_id;
                $start_time = $todayDate . ' ' . $emp->office_start_time;
                $end_time = $todayDate . ' ' . $emp->office_end_time;
    
                // Create Carbon DateTime objects
                $start_datetime = Carbon::parse($start_time);
                $end_datetime = Carbon::parse($start_time);
    
                // Adjust the time range if needed (in this example, subtract 4 hours from start and add 15 hours to end)
                $start_datetime->subHours(4);
                $end_datetime->addHours(14);
    
                // Fetch data from the 'att_temp' table for the specified time range
                $tempData = DB::table('att_temp')
                    ->where('id', $emp->bio_uid)
                    ->where('ip', $emp->bioID)
                    ->whereBetween('timestamp', [
                        $start_datetime->toDateTimeString(),
                        $end_datetime->toDateTimeString(),
                    ])
                    ->get();
    
                if (count($tempData) == 0) {
                    continue;
                }
    
                // Sort the $tempData by timestamp
                $tempData = $tempData->sortBy('timestamp');
    
                // Initialize clock_in and clock_out
                $clock_in = null;
                $clock_out = null;
    
                foreach ($tempData as $temp) {
                    if ($clock_in === null) {
                        // First record represents login time
                        $clock_in = $temp->timestamp;
                    }
                    // Last record represents logout time
                    $clock_out = $temp->timestamp;
                }
    
                // Check if there is only one record (no logout time)
                if (count($tempData) == 1) {
                    $clock_out = null;
                }
    
                $attendance = DB::table('attendances')
                    ->where('user_id', $emp->user_id)
                    ->where('date', $todayDate)
                    ->first();
    
                if ($attendance !== null) {
                    // Update existing attendance record
                    DB::table('attendances')
                        ->where('id', $attendance->id)
                        ->update([
                            'clock_in_time' => $clock_in,
                            'clock_out_time' => $clock_out,
                        ]);
                } else {
                    // Insert new attendance record
                    DB::table('attendances')->insert([
                        'user_id' => $emp->user_id,
                        'company_id' => 1,
                        'location_id' => 1,
                        'clock_in_time' => $clock_in,
                        'clock_out_time' => $clock_out,
                        'date' => $todayDate,
                        'shift_start_time' => $start_time,
                        'shift_end_time' => $end_time,
                        'employee_shift_id' => $emp->employee_shift_id,
                        'late' => 'no',
                        'work_from_type' => 'office',
                        'half_day' => 'no',
                    ]);
                }
            }
        }
    }
    
    
    // flexi whole month 
    public function flexi_db_month()
{
    ini_set('max_execution_time', 15000);
    ini_set('memory_limit', '1G');
    // return 'started';
    $now = Carbon::now();
    $currentMonth = $now->subDay()->format('m');
    $currentYear = $now->format('Y');
    $daysInMonth = Carbon::createFromDate($currentYear, $currentMonth, 1)->daysInMonth;

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $todayDate = Carbon::createFromDate($currentYear, $currentMonth, $day)->format('Y-m-d');
        echo "Processing: $todayDate \n";

        // Step 1: Only get user_ids missing shift for the day
        $employeeIds = DB::table('employee_details')
            ->leftJoin('employee_shift_schedules', function ($join) use ($todayDate) {
                $join->on('employee_shift_schedules.user_id', '=', 'employee_details.user_id')
                     ->whereDate('employee_shift_schedules.date', $todayDate);
            })
            ->whereNotNull('employee_details.bio_machine_id')
            ->whereNotNull('employee_details.bio_uid')
            ->whereNull('employee_shift_schedules.user_id')
            ->pluck('employee_details.user_id');

        if ($employeeIds->isEmpty()) {
            echo "No employee found\n";
            continue;
        }

        // Step 2: Fetch employee + machine data in one go
        $employees = DB::table('employee_details')
            ->join('bio_machine', 'bio_machine.id', '=', 'employee_details.bio_machine_id')
            ->whereIn('employee_details.user_id', $employeeIds)
            ->select('employee_details.*', 'bio_machine.*', 'bio_machine.id as bioID')
            ->get()
            ->keyBy('user_id');

        // Step 3: Fetch attendance data in one query
        $existingAttendances = DB::table('attendances')
            ->whereIn('user_id', $employeeIds)
            ->where('date', $todayDate)
            ->get()
            ->keyBy('user_id');

        // Step 4: For each user, get biometric logs only once
        foreach ($employees as $emp) {
            echo "Working On " . $emp->employee_id . "\n";

            $punches = DB::table('att_temp')
                ->where('ip', $emp->bioID)
                ->where('id', $emp->bio_uid)
                ->whereDate('timestamp', $todayDate)
                ->orderBy('timestamp')
                ->pluck('timestamp');

            $datacount = $punches->count();

            if ($datacount == 0) continue;

            $clock_in = $punches->first();
            $clock_out = $punches->last();

            // Attendance exists: update out time if needed
            if ($existingAttendances->has($emp->user_id)) {
                if ($datacount >= 2) {
                    DB::table('attendances')
                        ->where('id', $existingAttendances[$emp->user_id]->id)
                        ->update(['clock_out_time' => $clock_out]);
                }
                continue;
            }

            // New attendance: insert
            $late = 'no';
            $clockInTime = substr($clock_in, 11, 8);
            $halfday = ($datacount <= 1) ? 'yes' : 'no';

            // Late logic if required
            if (!empty($emp->late_mark_duration) && $emp->late_mark_duration > 0 && !empty($emp->start_time)) {
                try {
                    $clockInParsed = Carbon::parse($clockInTime);
                    $startTimeParsed = Carbon::parse($emp->start_time);
                    $lateMinutes = $startTimeParsed->diffInMinutes($clockInParsed, false);
                    if ($lateMinutes > $emp->late_mark_duration) {
                        $late = 'yes';
                    }
                } catch (\Exception $e) {}
            }

            DB::table('attendances')->insert([
                'user_id' => $emp->user_id,
                'company_id' => 1,
                'location_id' => 1,
                'clock_in_time' => $clock_in,
                'clock_out_time' => $datacount > 1 ? $clock_out : null,
                'date' => $todayDate,
                'late' => $late,
                'work_from_type' => 'office',
                'half_day' => $halfday,
            ]);
        }
    }
}


public function flexi_db_month_user($filterEmpID)
{
    ini_set('max_execution_time', 15000);
    ini_set('memory_limit', '1G');
    // return 'data';

    $now = Carbon::now();
    $currentMonth = $now->subDay()->format('m');
    $currentYear = $now->format('Y');
    $daysInMonth = Carbon::createFromDate($currentYear, $currentMonth, 1)->daysInMonth;

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $todayDate = Carbon::createFromDate($currentYear, $currentMonth, $day)->format('Y-m-d');
        echo "Processing: $todayDate \n";

        // Step 1: Get user_ids missing shift for the day, and filter by employee_id if provided
        $employeeIdsQuery = DB::table('employee_details')
            ->leftJoin('employee_shift_schedules', function ($join) use ($todayDate) {
                $join->on('employee_shift_schedules.user_id', '=', 'employee_details.user_id')
                     ->whereDate('employee_shift_schedules.date', $todayDate);
            })
            ->whereNotNull('employee_details.bio_machine_id')
            ->whereNotNull('employee_details.bio_uid')
            ->whereNull('employee_shift_schedules.user_id');

        if (!empty($filterEmpID)) {
            $employeeIdsQuery->where('employee_details.employee_id', $filterEmpID);
        }

        $employeeIds = $employeeIdsQuery->pluck('employee_details.user_id');

        if ($employeeIds->isEmpty()) {
            echo "No employee found\n";
            continue;
        }

        // Step 2: Fetch employee + machine data
        $employees = DB::table('employee_details')
            ->join('bio_machine', 'bio_machine.id', '=', 'employee_details.bio_machine_id')
            ->whereIn('employee_details.user_id', $employeeIds)
            ->select('employee_details.*', 'bio_machine.*', 'bio_machine.id as bioID')
            ->get()
            ->keyBy('user_id');

        // Step 3: Fetch attendance data in one query
        $existingAttendances = DB::table('attendances')
            ->whereIn('user_id', $employeeIds)
            ->where('date', $todayDate)
            ->get()
            ->keyBy('user_id');

        // Step 4: For each user, get biometric logs
        foreach ($employees as $emp) {
            echo "Working On " . $emp->employee_id . "\n";

            $punches = DB::table('att_temp')
                ->where('ip', $emp->bioID)
                ->where('id', $emp->bio_uid)
                ->whereDate('timestamp', $todayDate)
                ->orderBy('timestamp')
                ->pluck('timestamp');

            $datacount = $punches->count();

            if ($datacount == 0) continue;

            $clock_in = $punches->first();
            $clock_out = $punches->last();

            // Attendance exists: update out time if needed
            if ($existingAttendances->has($emp->user_id)) {
                if ($datacount >= 2) {
                    DB::table('attendances')
                        ->where('id', $existingAttendances[$emp->user_id]->id)
                        ->update(['clock_out_time' => $clock_out]);
                }
                continue;
            }

            // New attendance: insert
            $late = 'no';
            $clockInTime = substr($clock_in, 11, 8);
            $halfday = ($datacount <= 1) ? 'yes' : 'no';

            if (!empty($emp->late_mark_duration) && $emp->late_mark_duration > 0 && !empty($emp->start_time)) {
                try {
                    $clockInParsed = Carbon::parse($clockInTime);
                    $startTimeParsed = Carbon::parse($emp->start_time);
                    $lateMinutes = $startTimeParsed->diffInMinutes($clockInParsed, false);
                    if ($lateMinutes > $emp->late_mark_duration) {
                        $late = 'yes';
                    }
                } catch (\Exception $e) {}
            }

            DB::table('attendances')->insert([
                'user_id' => $emp->user_id,
                'company_id' => 1,
                'location_id' => 1,
                'clock_in_time' => $clock_in,
                'clock_out_time' => $datacount > 1 ? $clock_out : null,
                'date' => $todayDate,
                'late' => $late,
                'work_from_type' => 'office',
                'half_day' => $halfday,
            ]);
        }
    }
}




    public function flexi_db_month2() {
        ini_set('max_execution_time', 60); // Set to 60 seconds; aim to finish within this
    
        // Get current month and year
        $currentMonth = Carbon::now()->subDay(1)->format('m');
        $currentYear = Carbon::now()->format('Y');
        $daysInMonth = Carbon::now()->daysInMonth;
    
        // Loop through each day starting from the 9th
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $todayDate = Carbon::createFromDate($currentYear, $currentMonth, $day)->format('Y-m-d');
            echo "Processing date: $todayDate\n";
    
            // Step 1: Fetch all employees in one query (excluding those with a schedule for the day)
            $employees = DB::table('employee_details')
                ->join('bio_machine', 'bio_machine.id', 'employee_details.bio_machine_id')
                ->whereNotNull('employee_details.bio_machine_id')
                ->whereNotNull('employee_details.bio_uid')
                ->leftJoin('employee_shift_schedules', function ($join) use ($todayDate) {
                    $join->on('employee_shift_schedules.user_id', '=', 'employee_details.user_id')
                         ->whereDate('employee_shift_schedules.date', $todayDate);
                })
                ->whereNull('employee_shift_schedules.user_id')
                ->select('employee_details.*', 'bio_machine.ip', 'bio_machine.id as bioID')
                ->get()
                ->keyBy('user_id'); // Index by user_id for faster lookups
    
            if ($employees->isEmpty()) {
                continue; // Skip if no employees to process
            }
    
            // Step 2: Aggregate att_temp data for the day (get first and last timestamps per employee)
            $attendanceData = DB::table('att_temp')
                ->select(
                    'id as bio_uid',
                    'ip as bioID',
                    DB::raw('MIN(timestamp) as clock_in'),
                    DB::raw('MAX(timestamp) as clock_out'),
                    DB::raw('COUNT(*) as punch_count')
                )
                ->whereIn('ip', $employees->pluck('bioID')->unique()->toArray())
                ->whereIn('id', $employees->pluck('bio_uid')->toArray())
                ->whereDate('timestamp', $todayDate)
                ->groupBy('id', 'ip')
                ->get()
                ->keyBy('bio_uid'); // Index by bio_uid for faster lookups
    
            if ($attendanceData->isEmpty()) {
                continue; // Skip if no attendance data
            }
    
            // Step 3: Fetch existing attendance records for the day
            $existingAttendances = DB::table('attendances')
                ->whereIn('user_id', $employees->pluck('user_id')->toArray())
                ->where('date', $todayDate)
                ->get()
                ->keyBy('user_id'); // Index by user_id for faster lookups
    
            // Step 4: Prepare bulk insert and update data
            $inserts = [];
            $updates = [];
    
            foreach ($employees as $emp) {
                $bio_uid = $emp->bio_uid;
                if (!isset($attendanceData[$bio_uid])) {
                    continue; // Skip if no attendance data for this employee
                }
    
                $attData = $attendanceData[$bio_uid];
                $clock_in = $attData->clock_in;
                $clock_out = $attData->punch_count >= 2 ? $attData->clock_out : null;
                $punch_count = $attData->punch_count;
    
                // Check if attendance record exists
                $attendance = $existingAttendances[$emp->user_id] ?? null;
    
                if ($attendance) {
                    // Update existing record if there are 2 or more punches
                    if ($punch_count >= 2) {
                        $updates[] = [
                            'id' => $attendance->id,
                            'clock_out_time' => $clock_out,
                        ];
                    }
                } else {
                    // Prepare new attendance record
                    $clockInTime = Carbon::parse($clock_in)->format('H:i:s');
                    $late = 'no';
    
                    // Calculate late status
                    if (isset($emp->late_mark_duration) && $emp->late_mark_duration > 0) {
                        $clockInParsed = Carbon::parse($clockInTime);
                        $startTimeParsed = Carbon::parse(''); // Adjust based on your start time logic
                        $lateMinutes = $startTimeParsed->diffInMinutes($clockInParsed, false);
                        if ($lateMinutes > $emp->late_mark_duration) {
                            $late = 'yes';
                        }
                    }
    
                    $halfday = $punch_count <= 1 ? 'yes' : 'no';
    
                    $inserts[] = [
                        'user_id' => $emp->user_id,
                        'company_id' => 1,
                        'location_id' => 1,
                        'clock_in_time' => $clock_in,
                        'clock_out_time' => $clock_out,
                        'date' => $todayDate,
                        'late' => $late,
                        'work_from_type' => 'office',
                        'half_day' => $halfday,
                    ];
                }
            }
    
            // Step 5: Perform bulk insert and update
            if (!empty($inserts)) {
                DB::table('attendances')->insert($inserts);
            }
    
            if (!empty($updates)) {
                foreach ($updates as $update) {
                    DB::table('attendances')
                        ->where('id', $update['id'])
                        ->update([
                            'clock_out_time' => $update['clock_out_time'],
                        ]);
                }
            }
        }
    }
    // machine user export 
    
    public function machine_emp()
    {
        
        $machine = DB::table('bio_machine')->where('trash_status',0)->where('id',2)->inRandomOrder()->first();
        $data = [
                'ip' => $machine->ip
            ];
        $response = Http::post('https://rest.niftel.com/api/get_all', $data);
        $statusCode = $response->status();
        $body = $response->body();
        // return $body;
        $data = json_decode($body, true);
        if(is_null($data)){
            return;
        }
        foreach($data as $d){
            $array[] = $d['name'];
        }
        // Define the class inline
        $export = new class([$array]) implements FromArray {
            protected $array;

            public function __construct(array $array)
            {
                $this->array = $array;
            }

            public function array(): array
            {
                return [$this->array];
            }
        };

        // Directly return the Excel download response
        return Excel::download($export, 'array.xlsx');
    }
    
public function inactive_us()
{
    $filePath = public_path('support staff data active data - support staff data active data.csv');

    // Step 1: Load employee_ids from CSV
    $allowedEmployeeIds = [];
    if (($handle = fopen($filePath, 'r')) !== false) {
        while (($data = fgetcsv($handle)) !== false) {
            $employeeId = trim($data[0]);
            if (!empty($employeeId)) {
                $allowedEmployeeIds[] = $employeeId;
            }
        }
        fclose($handle);
    }

    // Step 2: Find users to deactivate (branch_id = 1, department_id IN [...], not in CSV)
    $usersToDeactivate = DB::table('users as u')
        ->join('employee_details as ed', 'ed.user_id', '=', 'u.id')
        ->select('u.id', 'ed.employee_id', 'u.name')
        ->where('ed.branch_id', 1)
        ->whereIn('ed.department_id', [22, 8, 16, 6, 11, 3])
        ->whereNotIn('ed.employee_id', $allowedEmployeeIds)
        ->get();

    // Step 3: Save list to CSV
    $csvPath = public_path('inactivated-support-staff.csv');
    $handle = fopen($csvPath, 'w');
    fputcsv($handle, ['user_id', 'employee_id', 'name']);

    foreach ($usersToDeactivate as $user) {
        fputcsv($handle, [$user->id, $user->employee_id, $user->name]);
    }

    fclose($handle);

    // Step 4: Deactivate users
    DB::table('users')
        ->whereIn('id', $usersToDeactivate->pluck('id'))
        ->update(['status' => 'deactive']);

    return response()->json([
        'status' => 'done',
        'deactivated_count' => count($usersToDeactivate),
        'download_url' => asset('inactivated-support-staff.csv'),
    ]);
}

public function updateDesignationFromCsv()
    {
        // CSV file name in /public directory
        $filePath = public_path('employee_details (10).csv');

        if (!file_exists($filePath)) {
            return "CSV file not found at: {$filePath}";
        }

        // Open the file
        if (($handle = fopen($filePath, 'r')) !== false) {

            // Skip header row if present
            fgetcsv($handle);

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // Assuming CSV columns: user_id, designation_id
                $userId = isset($data[0]) ? trim($data[0]) : null;
                $designationId = isset($data[1]) ? trim($data[1]) : null;

                if ($userId && $designationId) {
                    DB::table('employee_details')
                        ->where('user_id', $userId)
                        ->update(['designation_id' => $designationId]);
                }
            }
            fclose($handle);

            return "Designation IDs updated successfully from CSV.";
        }

        return "Unable to open the CSV file.";
    }



    

}