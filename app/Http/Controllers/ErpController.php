<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\User;
use App\Helper\Files;
use App\Helper\Reply;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Currency;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\InvoiceItems;
use Illuminate\Http\Request;
use App\Models\AcceptEstimate;
use App\Events\NewEstimateEvent;
use App\Models\EstimateTemplate;
use App\Models\EstimateItemImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Requests\StoreEstimate;
use App\Models\EstimateTemplateItem;
use Illuminate\Support\Facades\File;
use App\DataTables\EstimatesDataTable;
use App\Http\Requests\EstimateAcceptRequest;
use App\Models\UnitType;
use Illuminate\Validation\Validator;


class ErpController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'ERP';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('employees', $this->user->modules));
            return $next($request);
        });
    }
    
    
    public function erp_index(){
        
        abort_403(user()->permission('edit_employees') != 'all');
        $this->erp = DB::table('employee_erp')->where('employee_erp.trash_status',0)
                                        ->leftJoin('employee_details','employee_erp.employee_id','employee_details.employee_id')
                                        ->leftJoin('users','users.id','employee_details.user_id')
                                        ->select('employee_erp.*','employee_details.user_id as emp_id','employee_details.joining_date as emp_joining_date','users.status')
                                        ->latest('employee_erp.id')
                                        ->orderBy('employee_erp.reference_id','DESC')
                                        ->get();
        // return 'Page is under maintenance!!!!!!!!!!!';
        $this->employees = User::allEmployees(null, true, 'all');
        return view('erp.hr_index',$this->data);
    }
    
    public function erp_update(Request $request, $id){
        
        abort_403(user()->permission('edit_employees') != 'all');
        DB::table('employee_erp')
        ->where('id', $id)
        ->update([
            'batch_no' => $request->input('batch_no'),
            'employee_id' => $request->input('employee_id'),
            'name' => $request->input('name'),
            'contact' => $request->input('contact'),
            'process' => $request->input('process'),
            'int_status' => $request->input('int_status'),
            // 'reference_name' => $request->input('reference_name'),
            // 'reference_emp_id' => $request->input('reference_emp_id'),
            // 'reference_id' => $request->input('reference_id'),
            'campaign_name' => $request->input('campaign_name'),
            'joining_date' => $request->input('joining_date'),
            'tenurity' => $request->input('tenurity'),
            'in_months' => $request->input('in_months'),
            'exit_date' => $request->input('exit_date'),
            'bonus_status' => $request->input('bonus_status'),
            'bonus_amount' => $request->input('bonus_amount'),
            'email' => $request->input('email'),
            'alt_contact' => $request->input('alt_contact'),
            'exp_type' => $request->input('employmentStatus'),
            'previous_comp' => $request->input('previousCompany'),
            'updated_by' => user()->id
        ]);

        return redirect()->back()->with('success', 'Employee details updated successfully');
    }
    
    
    public function erp_delete($id){
        
        abort_403(user()->permission('edit_employees') != 'all');
        DB::table('employee_erp')
        ->where('id', $id)
        ->update([
            'trash_status' => 1,
            'updated_by' => user()->id
        ]);

        return redirect()->back()->with('success', 'Employee deleted successfully');
    }
    
    
    public function store(Request $request)
    {
        abort_403(user()->permission('edit_employees') != 'all');
    
        $data = [
            'name' => $request->input('reference_name'),
            'employee_id' => $request->input('reference_id'),
            'email' => $request->input('email'),
            'contact' => $request->input('contact'),
            'alt_contact' => $request->input('alt_contact'),
            'batch_no' => $request->input('batch_no'),
            'process' => $request->input('process'),
            'campaign_name' => $request->input('campaign_name'),
            'joining_date' => $request->input('joining_date'),
            'tenurity' => $request->input('tenurity'),
            'in_months' => $request->input('in_months'),
            'exit_date' => $request->input('exit_date'),
            'int_status' => $request->input('int_status'),
            'bonus_status' => $request->input('bonus_status'),
            'bonus_amount' => $request->input('bonus_amount'),
            'alt_contact' => $request->input('alt_contact'),
            'exp_type' => $request->input('employmentStatus'),
            'previous_comp' => $request->input('previousCompany'),
            'added_by' => user()->id,
        ];
    
        if($request->input('user_id') != 'Other'){
            $referee = DB::table('employee_details')
                ->where('employee_details.user_id', $request->input('user_id'))
                ->join('users','users.id','employee_details.user_id')
                ->select('employee_details.employee_id','users.name')
                ->first();
    
            if ($referee) {
                $data['reference_id'] = $request->input('user_id');
                $data['reference_name'] = $referee->name;
                $data['reference_emp_id'] = $referee->employee_id;
            }
        }else{
            $data['reference_name'] = 'Other';
        }
    
        DB::table('employee_erp')->insert($data);
    
        return redirect()->back()->with('success', 'Reference added successfully');
    }
    
    
    
    
    
    
    
    
    // my erp 
    public function my_erp_index(){
        
        // abort_403(user()->permission('edit_employees') != 'all');
        
        $this->pageTitle = 'My ERP';
        $this->erp = DB::table('employee_erp')->where('employee_erp.trash_status',0)
                                        ->where('employee_erp.reference_emp_id',user()->username)
                                        ->leftJoin('employee_details','employee_erp.employee_id','employee_details.employee_id')
                                        ->leftJoin('users','users.id','employee_details.user_id')
                                        ->select('employee_erp.*','employee_details.user_id as emp_id','users.status','employee_details.joining_date as emp_joining_date')
                                        ->latest('employee_erp.id')
                                        ->get();
        // return 'Page is under maintenance!!!!!!!!!!!';
        // return $data = view('shift_view')
        return view('erp.emp_index',$this->data);
    }
    
    public function my_store(Request $request)
    {
        // abort_403(user()->permission('edit_employees') != 'all');
    
        $data = [
            'name' => $request->input('reference_name'),
            'employee_id' => $request->input('reference_id'),
            // 'user_id' => $request->input('reference_id'),
            'contact' => $request->input('contact'),
            'batch_no' => $request->input('batch_no'),
            'process' => $request->input('process'),
            'campaign_name' => $request->input('campaign_name'),
            'email' => $request->input('email'),
            'alt_contact' => $request->input('alt_contact'),
            'exp_type' => $request->input('employmentStatus'),
            'previous_comp' => $request->input('previousCompany'),
            // 'bonus_status' => $request->input('bonus_status'),
            // 'bonus_amount' => $request->input('bonus_amount'),
            'added_by' => user()->id,
        ];
    
             
    
            $data['reference_id'] = user()->id;
            $data['reference_name'] = user()->name;
            $data['reference_emp_id'] = user()->username;
        
    
        DB::table('employee_erp')->insert($data);
    
        return redirect()->back()->with('success', 'Reference added successfully');
    }
    
    
    
    public function uploadCsv(Request $request)
    {
        // Validate the uploaded CSV file
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);
    
        // Check if the file is uploaded
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $handle = fopen($file, 'r');
            $header = fgetcsv($handle, 1000, ",");
            
            // Pre-fetch reference data from the database
            $references = DB::table('employee_details')
                ->join('users', 'users.id', '=', 'employee_details.user_id')
                ->select('employee_details.user_id', 'employee_details.employee_id', 'users.name')
                ->get()
                ->keyBy('employee_id'); // Make sure to key by user_id
            // return $references;
            // Initialize error log array
            $errorLog = [];
    
            // Start a transaction for batch processing
            DB::beginTransaction();
    
            try {
                // Prepare an array to hold the data for batch insertion
                $dataToInsert = [];
    
                // Read each row from the CSV
                $rowNumber = 1; // To track row numbers in the CSV
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    $rowNumber++;
    
                    try {
                        // Assuming Reference ID is in column index 8
                        $referenceId = $data[8];
    
                        // Retrieve the reference details from the pre-fetched data
                        $referee = $references->get($referenceId);
                        
                        if ($referee) {
                            $referenceName = $referee->name;
                            $referenceEmpId = $referee->employee_id;
                            $ref_user_id = $referee->user_id;
                            // return ;
                        } else {
                            $referenceName = null;
                            $referenceEmpId = null;
                            $referenceId = null;
                            $ref_user_id = null;
                        }
                        
                        
                        // checking referee selection status
                        $referee_id = $data[2];
                        $referee_data = $references->get($referee_id);
                        
                        if ($referee_data) {
                            $referee_status = 2;
                        } else {
                            $referee_status = 0;
                        }
    
                        // Prepare the data for insertion
                        $newRow = [
                            'batch_no' => $data[1],
                            'employee_id' => $data[2],
                            'name' => $data[3],
                            'contact' => $data[4],
                            'email' => $data[5],
                            'process' => $data[6],
                            'reference_name' => $referenceName,
                            'reference_emp_id' => $referenceEmpId,
                            'reference_id' => $ref_user_id,
                            'campaign_name' => $data[9],
                            'joining_date' => $data[10],
                            'tenurity' => $data[11],
                            'in_months' => $data[12],
                            'exit_date' => $data[13],
                            'int_status' => $referee_status,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
    
                        // Check if employee_id is not null
                        if (!is_null($newRow['employee_id'])) {
                            // Check if employee_id already exists in the database
                            $exists = DB::table('employee_erp')
                                ->where('employee_id', $newRow['employee_id'])
                                ->exists();
    
                            if ($exists) {
                                // Log the error for this specific row
                                $errorLog[] = "Row {$rowNumber}: Employee ID {$newRow['employee_id']} already exists.";
                                continue; // Skip this row
                            }
                        }
    
                        // Add to the data to be inserted
                        $dataToInsert[] = $newRow;
                    } catch (Exception $e) {
                        // Log the error for this specific row
                        $errorLog[] = "Row {$rowNumber}: " . $e->getMessage();
                    }
                }
    
                // Batch insert the data into the database
                if (!empty($dataToInsert)) {
                    DB::table('employee_erp')->insert($dataToInsert);
                }
    
                // Commit the transaction
                DB::commit();
    
                fclose($handle);
    
                // Check if there were any errors
                if (!empty($errorLog)) {
                    return back()->with('success', 'CSV uploaded with some errors.')->with('errorLog', $errorLog);
                }
    
                return back()->with('success', 'CSV uploaded successfully.');
            } catch (\Exception $e) {
                // Rollback the transaction in case of an error
                DB::rollBack();
    
                fclose($handle);
    
                return back()->with('error', 'There was an error uploading the CSV: ' . $e->getMessage());
            }
        }
    
        return back()->with('error', 'Please upload a valid CSV file.');
    }

    
    
    
   
    /**
 * Parse a date string into a Carbon date, or log an error if parsing fails.
 *
 * @param string $dateString
 * @param int $rowNumber
 * @param array &$errorLog
 * @param string $fieldName
 * @return \Carbon\Carbon|null
 */
private function parseDate($dateString, $rowNumber, &$errorLog, $fieldName)
{
    // Define valid date formats
    $validFormats = ['d/m/Y', 'Y-m-d', 'd-m-Y'];

    try {
        // Check if the date string is empty or contains invalid placeholders
        if (empty(trim($dateString)) || in_array($dateString, ['-', 'N/A', 'Pending', 'Active', 'Inactive'])) {
            return null; // Return null for invalid or empty date strings
        }

        // Trim whitespace and remove any unwanted characters
        $dateString = trim($dateString);

        // Attempt to parse the date string with multiple formats
        foreach ($validFormats as $format) {
            $parsedDate = Carbon::createFromFormat($format, $dateString, 'UTC');
            if ($parsedDate !== false) {
                return $parsedDate;
            }
        }

        // If no format matched, throw an exception
        throw new \Exception("Unexpected data found. Date format did not match any known pattern.");

    } catch (\Exception $e) {
        $errorLog[] = "Row {$rowNumber}: Could not parse '{$fieldName}' - " . $e->getMessage();
        return null;
    }
}



    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}