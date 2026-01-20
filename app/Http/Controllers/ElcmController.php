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

class ElcmController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.employees';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('employees', $this->user->modules));
            return $next($request);
        });
    }

    public function elcm($user_id)
    {
        $this->pageTitle = 'ELCM'; 
        $this->elsm = DB::table('employee_elcm')->where('employee_elcm.user_id',$user_id)->first();
        if(!$this->elsm){
            DB::table('employee_elcm')->insert(['user_id' => $user_id]);
            $this->elsm = DB::table('employee_elcm')->where('employee_elcm.user_id',$user_id)->first();
        }
        return view('employees.edit_elcm',$this->data);
        
    }
    
    public function elcm_red(){
        return redirect('/account/employees');
    }
    
    public function elcm_edit(Request $request,$user_id){
         // Validate the incoming request data
        // $request->validate([
            // 'bgvStatus' => 'required|string|max:255',
            // 'inductionDate' => 'required|date',
            // 'loiYes' => 'required|string|max:255',
            // 'loiDate' => 'required|date',
            // 'documentStatus' => 'required|string|max:255',
            // 'biometricYesNo' => 'required|string|max:255',
            // 'handoverToTraining' => 'required|string|max:255',
            // 'batchNumberHR' => 'required|string|max:255',
            // 'trainingStartDate' => 'required|date',
            // 'trainingEndDate' => 'required|date',
            // 'cltCertificationDate' => 'required|date',
            // 'cltCertificationStatus' => 'required|string|max:255',
            // 'offerLetterYesNo' => 'required|string|max:255',
            // 'offerLetterDate' => 'required|date',
            // 'bankAccountStatus' => 'required|string|max:255',
            // 'ojtStartDate' => 'required|date',
            // 'ojtEndDate' => 'required|date',
            // 'ojtCertifiedDate' => 'required|date',
            // 'ojtCertificationStatus' => 'required|string|max:255',
            // 'handoverToProduction' => 'required|string|max:255',
            // 'currentStatus' => 'required|string|max:255',
            // 'reasonForLeaving' => 'required|string|max:255',
            // 'noticePeriod' => 'required|integer',
            // 'noticePeriodServedDays' => 'required|integer',
            // 'noticeServed' => 'required|string|max:255',
            // 'remarks' => 'required|string|max:255',
            // 'fnfEligibility' => 'required|string|max:255',
            // 'fnfStatus' => 'required|string|max:255',
            // 'fnfDate' => 'required|date',
            // 'experienceLetterStatus' => 'required|string|max:255',
            // 'att_email' => 'required|email|max:255',
        // ]);

        // Update the data in the database using query builder
        DB::table('employee_elcm')
            ->where('user_id', $user_id)
            ->update([
                'bgv' => $request->bgvStatus,
                'induaction_date' => $request->inductionDate,
                'loi' => $request->loiYes,
                'loi_date' => $request->loiDate,
                'document_status' => $request->documentStatus,
                'biometric' => $request->biometricYesNo,
                'handover_training_date' => $request->handoverToTraining,
                'batch_no' => $request->batchNumberHR,
                'training_start' => $request->trainingStartDate,
                'training_end' => $request->trainingEndDate,
                'clt_date' => $request->cltCertificationDate,
                'clt_status' => $request->cltCertificationStatus,
                'offer_letter' => $request->offerLetterYesNo,
                'offer_later_date' => $request->offerLetterDate,
                'bank_acc_status' => $request->bankAccountStatus,
                'ojt_start' => $request->ojtStartDate,
                'ojt_end' => $request->ojtEndDate,
                'ojt_cert_date' => $request->ojtCertifiedDate,
                'ojt_cert_status' => $request->ojtCertificationStatus,
                'handover_ops_date' => $request->handoverToProduction,
                'current_status' => $request->currentStatus,
                'reason_for_leav' => $request->reasonForLeaving,
                'notice_period_days' => $request->noticePeriod,
                'notice_period_served' => $request->noticePeriodServedDays,
                'notice_served' => $request->noticeServed,
                'remark' => $request->remarks,
                'fnf_elg' => $request->fnfEligibility,
                'fnf_status' => $request->fnfStatus,
                'fnf_date' => $request->fnfDate,
                'exp_letter' => $request->experienceLetterStatus,
                'att_email' => $request->att_email,
            ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Data updated successfully.');
    }
    
    public function uploadCsv(Request $request)
    {
        // Validate the file upload
        $request->validate([
            'csv_file' => 'required|file|mimes:csv',
        ]);

        // Get the file
        $file = $request->file('csv_file');

        // Open the file and read its contents
        $fileData = file_get_contents($file);
        $rows = array_map('str_getcsv', explode("\n", $fileData));
        $header = array_shift($rows);

        // Retrieve all employee details once
        $employees = DB::table('employee_details')->get()->keyBy('employee_id')->toArray();

        // Prepare data for batch insert/update and collect errors
        $batchData = [];
        $errors = [];

        // Process each row
        foreach ($rows as $row) {
            if (count($row) == count($header)) {
                $data = array_combine($header, $row);

                // Retrieve user_id from employee_id from the pre-fetched employee details
                if (isset($employees[$data['EMP ID']])) {
                    $employee = $employees[$data['EMP ID']];

                    $batchData[] = [
                        'user_id' => $employee->user_id,
                        'csa' => $data['CSA/Non CSA'] ?? null,
                        'pt_ft' => $data['PT/FT'] ?? null,
                        'personal_email' => $data['Personal Email'] ?? null,
                        'official_email' => $data['Official Email'] ?? null,
                        'bgv' => $data['BGV Status'] ?? null,
                        'induaction_date' => $data['Induction Date'] ?? null,
                        'loi' => $data['LOI Yes'] ?? null,
                        'loi_date' => $data['LOI Date'] ?? null,
                        'document_status' => $data['Document Status'] ?? null,
                        'biometric' => $data['Biometric Yes/No'] ?? null,
                        'handover_training_date' => $data['Handover To Training'] ?? null,
                        'batch_no' => $data['Batch Number'] ?? $data['Batch Number (HR)'] ?? null,
                        'batch_no_train' => $data['Batch Number (Training)'] ?? null,
                        'training_start' => $data['Training Start Date'] ?? null,
                        'training_end' => $data['Training End Date'] ?? null,
                        'clt_status' => $data['CLT Certification Status'] ?? null,
                        'offer_letter' => $data['Offer Letter Yes/No'] ?? null,
                        'offer_later_date' => $data['Offer Letter Date'] ?? null,
                        'bank_acc_status' => $data['Bank Account Status'] ?? null,
                        'ojt_start' => $data['OJT Start Date'] ?? null,
                        'ojt_end' => $data['OJT End Date'] ?? null,
                        'ojt_cert_status' => $data['OJT Certification Status'] ?? null,
                        'ojt_cert_date' => $data['OJT Certification Date'] ?? null,
                        'handover_ops_date' => $data['Handover to OPS'] ?? null,
                        'reason_for_leav' => $data['Reason For Leaving'] ?? null,
                        'notice_period_days' => $data['Notice Period'] ?? null,
                        'notice_period_served' => $data['Notice Period Served in Days'] ?? null,
                        'notice_served' => $data['Notice Served'] ?? null,
                        'remark' => $data['Remarks'] ?? null,
                        'fnf_elg' => $data['FnF Eligility'] ?? null,
                        'fnf_status' => $data['FnF Status'] ?? null,
                        'fnf_date' => $data['FnF Date'] ?? null,
                        'exp_letter' => $data['Experience Letter Status'] ?? null,
                        'att_email' => $data['Attrition Email'] ?? null,
                    ];
                } else {
                    $errors[] = "Employee with ID {$data['EMP ID']} not found.";
                } 
            } else {
                $errors[] = "Invalid row format for row: " . implode(", ", $row);
            }
        }

         // Chunk size for batch insert
        $chunkSize = 500;

        // Batch insert/update in chunks
        foreach (array_chunk($batchData, $chunkSize) as $chunk) {
            DB::table('employee_elcm')->upsert($batchData, ['user_id'], [
                  'csa', 'personal_email', 'official_email', 'bgv', 'induaction_date', 'loi', 'loi_date', 'document_status', 'biometric', 'handover_training_date', 'batch_no', 'batch_no_train','training_start', 'training_end', 'clt_status', 'offer_letter', 'offer_later_date', 'bank_acc_status', 'ojt_start', 'ojt_end', 'ojt_cert_status', 'ojt_cert_date', 'handover_ops_date', 'reason_for_leav', 'notice_period_days', 'notice_period_served', 'notice_served', 'remark', 'fnf_elg', 'fnf_status', 'fnf_date', 'exp_letter', 'att_email'
            ]);
        }

        // Return with success and errors
        return redirect()->back()->with('success', 'CSV data has been uploaded and processed successfully.')->with('uploadErrors', $errors);
    }

}