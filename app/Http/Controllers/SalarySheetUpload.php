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
use App\Imports\SalaryImport;



class SalarySheetUpload extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Salary';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array('employees', $this->user->modules));
            return $next($request);
        });
        
    }
    
    
    
    public function upload_salary_sheet(){
        
        $this->year = now()->format('Y');
        $this->month = now()->format('m');
        
        return view('salary.upload_salary_sheet',$this->data);
    }
    
    
    
    public function uploadMonthlySheet(Request $request)
{
    // Validate the incoming request
    // echo "validating....";
    $request->validate([
        'import_file' => 'required|mimes:csv',
        'month' => 'required|numeric',
        'year' => 'required|numeric',
    ]); 
    
    // Get the file from the request
    $file = $request->file('import_file');
    $month = $request->input('month');
    $year = $request->input('year');

    // return $month;
    $monthName = Carbon::createFromFormat('m', $month)->format('F');
    
    // Read the CSV file
    $csvData = array_map('str_getcsv', file($file));

    // Fetch user details
    $employeeDetails = EmployeeDetails::all()->keyBy('employee_id');

    // Array to store salary data
    $allSalaryData = [];

    // Process CSV data
    $this->processCSVData($csvData, $employeeDetails, $month, $year, $allSalaryData, $monthName);

    // return $allSalaryData;
    
    // Upsert salary data into the database
        if (!empty($allSalaryData)) {
            DB::transaction(function () use ($allSalaryData) {
                    foreach ($allSalaryData as $data) {
                        // Perform the upsert operation for each record
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
    
        }
        
        if ($request->hasFile('import_file')) {
            $fileName = time().'_'.$year.'_'.$month.'.'.$file->getClientOriginalExtension();
            $filePath = public_path('/sheet_files/');
            $file->move($filePath, $fileName);
        }
        
    return redirect()->back()->with('mess','Data Uploaded Successfully.');
}

private function processCSVData($csvData, $employeeDetails, $month, $year, &$allSalaryData, $monthName)
{
    // Skip headers
    $headersSkipped = false;

    foreach ($csvData as $row) {
        // Skip headers
        if (!$headersSkipped) {
            $headersSkipped = true;
            continue;
        }

        // Ensure that the row has enough elements
        // if (count($row) < 31) {
        //     // Log or handle the incomplete row error
        //     \Log::error('Incomplete row: ' . json_encode($row));
        //     continue;
        // }

        $employeeId = $row[2]; // Assuming employee ID is in the first column

        $employeeDetail = $employeeDetails[$employeeId] ?? null;

        if (!$employeeDetail) {
            continue;
        }
        
        // Handle PF condition
        $pf = $row[14] == 0 ? 1 : 0;
        $final_pay = round(floatval($row[32] ?? 0));
        
        // calculation 
        
        $gross = floatval($row[13] ?? 0);
        

        // Construct the salary data array
        $salaryData = [
            'user_id' => $employeeDetail->user_id,
            'ctc_year' => (floatval($row[10] ?? 0) * 12), // Monthly CTC
            'ctc_month' => floatval($row[10] ?? 0), // Monthly CTC
            'days_in_month' => floatval($row[11] ?? 0), // Days in Month
            'payable_days' => floatval($row[12] ?? 0), // Payable Days
            'ctc_as_per_payable_days' => floatval($row[13] ?? 0), // CTC as per Payable Days
            'bas_month' => floatval($row[14] ?? 0), // Basic
            'hra_month' => floatval($row[15] ?? 0), // HRA
            'convenience' => floatval($row[16] ?? 0), // Conveyance
            'vsa' => floatval($row[17] ?? 0), // Voice Skill Allowance
            'specialAllowance' => floatval($row[18] ?? 0), // Special Allowance
            'grossSalary' => floatval($row[19] ?? 0), // Gross Salary
            'arrear' => floatval($row[20] ?? 0), // Arrear
            'deduction_keys' => floatval($row[21] ?? 0), // Deduction Keys
            'employeePF' => floatval($row[22] ?? 0), // Employee PF
            'employeeESIC' => floatval($row[23] ?? 0), // Employee ESIC
            'netTakehome' => floatval($row[24] ?? 0), // Net Pay
            'employerPF' => floatval($row[25] ?? 0), // Employer PF
            'employerESIC' => floatval($row[26] ?? 0), // Employer ESIC
            'overtime_amount' => floatval($row[28] ?? 0), // OT Aamount
            'ta' => floatval($row[29] ?? 0), // TA
            'cecl' => floatval($row[30] ?? 0), // CECL
            'ot_incentive_client_driven' => 0, // Assuming OT Incentive Client Driven is not available in the CSV
            'other_deduction' => floatval($row[27] ?? 0), // Assuming Deduction is not available in the CSV
            'total_deduction' => (floatval($row[21] ?? 0)) + (floatval($row[22] ?? 0)) + (floatval($row[23] ?? 0)), // Deduction Keys + Employee PF + Employee ESIC
            'final_pay' => $final_pay,
            'final_pay_word' => $this->convertToWords($final_pay) ?? '',
            'net_tak_home_word' => $this->convertToWords($row[24] ?? 0) ?? '',
            'month_name' => $monthName,
            'pf_deduct' => $pf,
            'month' => $month,
            'year' => $year,
            'bank' => $row[7] ?? '',
            'account' => $row[5] ?? '',
            'ifsc' => $row[6] ?? '',
            'pf_deduct' => floatval($row[22] ?? 0) > 1 ? 0 : 1,
        ];

        // Add salary data to the array
        $allSalaryData[] = $salaryData;
    }
    
    
}


    
    
    
    
    
    
    // upload file with chunks 
    
    public function testing(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls|max:204800',
            'month' => 'required|numeric',
            'year' => 'required|numeric',
        ]); 
    
        if ($request->hasFile('import_file')) {
            $file = $request->file('import_file');
            $fileName = time().'.'.$file->getClientOriginalExtension();
            $filePath = public_path('/sheet_files/');
            $file->move($filePath, $fileName);
    
            DB::table('salary_sheet_file')
                            ->updateOrInsert(
                                [
                                    
                                    'month' => $request->month,
                                    'year' => $request->year,
                                ],
                                [
                                    'file_name' => $fileName,
                                    ]
                                
                            );
            $update = DB::table('salary_sheet_file')
                    ->where('month', $request->month)
                    ->where('year', $request->year)
                    ->first();
            return response()->json(['message' => 'File uploaded successfully', 'file' => $fileName,'file_id' => $update->id], 200);
        } else {
            return response()->json(['error' => 'No file uploaded'], 400);
        }
    }
    
    
    
    
   public function convertToWords($number)
{
    $number = (int) $number; // Ensure $number is an integer

    if ($number === 0) {
        return 'Zero';
    }

    $negative = $number < 0;
    $number = abs($number);

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
                $words .= 'and ';
            }
            $words .= $this->convertToWords($number);
        }
    }

    if ($negative) {
        $words = 'Negative ' . $words;
    }

    return $words;
}


    


    
    
    
    
}