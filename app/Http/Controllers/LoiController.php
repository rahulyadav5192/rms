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
use Barryvdh\DomPDF\Facade\Pdf;


class LoiController extends AccountBaseController
{
    
    
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Letters';
        
    }
    
    
    public function letters() {
        $this->emp = DB::table('employee_details')->where('employee_details.user_id',auth()->user()->id)
                     ->join('users','employee_details.user_id','users.id')
                     ->join('designations','employee_details.designation_id','designations.id')
                     ->join('teams','employee_details.department_id','teams.id')
                     ->select('employee_details.*','users.*','teams.team_name','designations.name as designations_name',DB::raw('FLOOR(DATEDIFF(CURRENT_DATE(), employee_details.joining_date) / 30) as months_since_joining'))
                     ->first();
                     
        $documents = DB::table('employee_docs')->where('user_id', auth()->user()->id)->get();
        $hasAdharCard = false;
        $hasPanCard = false;
        $has10th = false;
        $has12th = false;
        $hasPhoto = false;
        $hasack = false;
    
        foreach($documents as $d) {
            if (strpos($d->name, "Adhar Card") !== false) {
                $hasAdharCard = true;
            }
    
            if (strpos($d->name, "Pan Card") !== false) {
                $hasPanCard = true;
            }
    
            if (strpos($d->name, "10th Marksheet") !== false) {
                $has10th = true;
            }
    
            if (strpos($d->name, "12th Marksheet") !== false) {
                $has12th = true;
            }
    
            if (strpos($d->name, "Passport Size Photo") !== false) {
                $hasPhoto = true;
            }
            if (strpos($d->name, "Acknowledgment Letter") !== false) {
                $hasack = true;
            }
        }
    
        $isPending = count($documents) < 1;
        $isPartial = !$hasAdharCard || !$hasPanCard || !$has10th || !$has12th || !$hasPhoto ||!$hasack;
        
        
        if($isPending){
            $this->doc_status = 'Pending';
        }
        elseif($isPartial){
            $this->doc_status = 'Pending';
        }
        else{
            $this->doc_status = 'NotPending';
        }
        
        
        // Fetch the data for the logged-in user
        // $employee = DB::table('employee_details as e')
        //     ->join('users as u', 'e.user_id', '=', 'u.id')
        //     ->join('branches as b', 'e.branch_id', '=', 'b.id')
        //     ->join('teams as d', 'e.department_id', '=', 'd.id')
        //     ->select(
        //         'b.name as branch',
        //         'e.employee_id',
        //         'd.team_name as teams',
        //         'u.name as users_name',
        //         DB::raw('FLOOR(DATEDIFF(CURRENT_DATE(), e.joining_date) / 30) as months_since_joining'),
        //         'e.joining_date'
        //     )
        //     ->where('u.id', user()->id)
        //     ->where('u.status', 'active')
        //     ->first();

        // Calculate the number of completed 11-month periods
        $completedPeriods = intdiv($this->emp->months_since_joining, 11);

        $letters = [];
        for ($i = 0; $i <= $completedPeriods; $i++) {
            $letters[] = [
                'period' => $i + 1,
                // 'start_date' => date('Y-m-d', strtotime("+".($i * 11)." months", strtotime($employee->joining_date))),
                'date' => date('Y-m-d', strtotime("+".(($i + 1) * 11)." months", strtotime($this->emp->joining_date))),
            ];
        }
        
        $this->letters =$letters ;
        
        
        return view('letter.list',$this->data);
    }
    
    
    public function viewContractLetter($period)
    {
        // Get the currently logged-in user
        // $user = Auth::user();

        // Fetch the data for the logged-in user
        $employee = DB::table('employee_details as e')
            ->join('users as u', 'e.user_id', '=', 'u.id')
            ->join('branches as b', 'e.branch_id', 'b.id')
            ->join('teams as d', 'e.department_id', 'd.id')
            ->select(
                'b.name as branch',
                'e.employee_id',
                'd.team_name as teams',
                'u.name as name',
                DB::raw('FLOOR(DATEDIFF(CURRENT_DATE(), e.joining_date) / 30) as months_since_joining'),
                'e.joining_date'
            )
            ->where('e.user_id', auth()->user()->id)
            // ->where('u.status', 'active')
            ->first();
            
            // return auth()->user()->id;
         if(!$employee){
             return 'Something went wrong';
         }  
            
        $completedPeriods = intdiv($employee->months_since_joining, 11);

        // Check if the requested period is valid
        if ($period < 1 || $period > ($completedPeriods + 1)) {
            return redirect('/account/myletters')->with('message', 'Invalid contract requested');
        }

        // Calculate the date for the specific period
        $date = date('Y-m-d', strtotime("+".($period * 11)." months", strtotime($employee->joining_date)));

        return view('letter.11month', compact('employee', 'period', 'date'));
    }
    
    
    
    public function viewMyLoi(){
        $this->emp = DB::table('employee_details')->where('employee_details.user_id',auth()->user()->id)
                     ->join('users','employee_details.user_id','users.id')
                     ->join('designations','employee_details.designation_id','designations.id')
                     ->join('teams','employee_details.department_id','teams.id')
                     ->join('branches','employee_details.branch_id','branches.id')
                     ->select('employee_details.*','users.*','teams.team_name','designations.name as designations_name','branches.name as branch_name','branches.address as b_address')
                     ->first();
//         if(auth()->user()->id == 235){
//           $pdf = Pdf::loadView('letter.new_loi', ['emp' => $this->emp])
//           ->setPaper('a4', 'portrait');

// return $pdf->download('Offer_Letter_'.$this->emp->name.'.pdf');


//         }
        // return view('letter.loi',$this->data);
        return view('letter.new_loi',$this->data);
    }
    
   public function downloadMyLoi()
     {
         $this->emp = DB::table('employee_details')
             ->where('employee_details.user_id', auth()->user()->id)
             ->join('users', 'employee_details.user_id', 'users.id')
             ->join('designations', 'employee_details.designation_id', 'designations.id')
             ->join('teams', 'employee_details.department_id', 'teams.id')
             ->join('branches', 'employee_details.branch_id', 'branches.id')
             ->select(
                 'employee_details.*',
                 'users.*',
                 'teams.team_name',
                 'designations.name as designations_name',
                 'branches.name as branch_name',
                 'branches.address as b_address'
             )
             ->first();

         $filename = 'LOI_' . $this->emp->employee_id . '_' . date('Y-m-d') . '.pdf';

         
             $pdf = PDF::loadView('letter.new_loi', ['emp' => $this->emp]);
         

         $pdf->setPaper('A3', 'portrait');
         $pdf->setOption('margin-top', 15);
         $pdf->setOption('margin-right', 20); // Increased to 20mm
         $pdf->setOption('margin-bottom', 15);
         $pdf->setOption('margin-left', 15);

         $pdf->setOption('auto-page-break', true);
         $pdf->setOption('dpi', 150);
         $pdf->setOption('enable-local-file-access', true);
         $pdf->setOption('isHtml5ParserEnabled', true);
         $pdf->setOption('default-font', 'Helvetica');

         return $pdf->download($filename);
     }

    
    
    // upload sheet  and show offer letter data 
    public function upload_oldata (Request $request){
        
        $query = DB::table('users');
            $query->join('employee_details','employee_details.user_id','users.id')
            ->whereNotNull('employee_details.offer_salary_month')
            ->whereNotNull('employee_details.joining_date')
            ->whereNotNull('employee_details.employment_type')
            ->latest('employee_details.id');
            if(isset($_GET['branch'])){
                if($_GET['branch'] != 'all'){
                    $b_id = (int) $_GET['branch'];
                $query->where('employee_details.branch_id',$b_id);
                }
                
            }
            if(isset($_GET['status'])){
                if($_GET['status'] == 'active'){
                    $query->where('users.status','active');
                }elseif($_GET['status'] == 'deactive'){
                    $query->where('users.status','deactive');
                }else{
                    
                }
                
            } else {
                $query->where('users.status','active');
            }
            // if(isset($_GET['status'])){
            //     if($_GET['status'] != 'all'){
            //         $s_id = $_GET['status'];
            //         $query->where('users.login', '=', "disable");                }
                
            // }
            if(isset($_GET['department'])){
                if($_GET['department'] != 'all'){
                    $d_id = (int) $_GET['department'];
                $query->whereRaw('ABS(department_id) = ?', [$d_id]);
                }
                
            }
            
            if(isset($_GET['designation'])){
                if($_GET['designation'] != 'all'){
                    $ds_id = (int)$_GET['designation'];
                $query->whereRaw('ABS(designation_id) = ?', [$ds_id]);
                }
                
            }
            
            if (isset($_GET['search'])) {
                $name = $_GET['search'];
                $query->where(function ($query) use ($name) {
                    $query->where('users.name', 'LIKE', '%' . $name . '%')
                        ->orWhere('users.username', 'LIKE', '%' . $name . '%');
                });
            }            
              
            $query->join('branches','branches.id','employee_details.branch_id')->join('designations','designations.id','employee_details.designation_id')
            ->join('teams','employee_details.department_id','teams.id')
                ->select(
                    'users.*', 'employee_details.*', 'designations.*',
                    'users.name as employee_name',
                    'users.id as user_iddd',
                    'users.status as status',
                    'branches.name as branche_name',
                    'designations.name as designation_name',
                    'teams.team_name as deapartment'
                    )->orderBy('users.id','DESC');
                    
             $this->all =  $query->paginate(20); 
             
             $this->skills = Skill::all();
            $this->departments = Team::all();
            $this->designations = Designation::allDesignations();
            $this->branches = Branch::allBranches();
            $this->roles = Role::where('name', '<>', 'client')
                ->orderBy('id')->get();
        return view('letter.upload_data',$this->data);
    }
    
    
    public function viewLoi($user_id){
        // if(user()->id != 14738){
        //     return "Under Maintanance! Please Try After 1 Hour";
        // }
        $this->emp = DB::table('employee_details')->where('employee_details.user_id',$user_id)
                     ->join('users','employee_details.user_id','users.id')
                     ->join('branches','employee_details.branch_id','branches.id')
                     ->join('designations','employee_details.designation_id','designations.id')
                     ->join('teams','employee_details.department_id','teams.id')
                     ->select('employee_details.*','users.*','teams.team_name','designations.name as designations_name','designations.id as d_id','branches.name as branch_name','branches.address as b_address')
                     ->first();
        // if ($this->emp->joining_date >= '2025-08-21') {
        //     if($this->emp->department_id == 1){
        //         return view('letter.new_ol_after_21_aug_blinkit', $this->data);
        //     }
        // }
        
        if (
    $this->emp->joining_date >= '2025-11-01' &&
    $this->emp->joining_date <= '2025-11-25' &&
    in_array($this->emp->branch_id, [1, 7]) &&
    in_array($this->emp->department_id, [28, 31]) &&
    $this->emp->designation_id == 12
) {
    return view('letter.newol_after_1nov_voice_im', $this->data);
}

        
        if (
                $this->emp->joining_date >= '2025-11-26' && 
                in_array($this->emp->branch_id, [1, 7]) && 
                in_array($this->emp->department_id, [28, 31]) && 
                $this->emp->designation_id == 12
            ) {
                return view('letter.newol_after_26nov_voice_im', $this->data);
        }
        
        if ($this->emp && $this->emp->joining_date >= '2025-01-01') {
            // Redirect to the new letter
            if (in_array($this->emp->department_id, [28, 31])  && in_array($this->emp->designation_id, [12])  && $this->emp->gender == "male") {
                return view('letter.newol_after1jan_all', $this->data);
            } else {
                return view('letter.newol_after1jan_all', $this->data); // Replace with your actual route
            }

        }
        return view('letter.ol',$this->data);
    }
    
    public function submit_oldata(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:csv,txt' // Assuming maximum file size is 2MB
        ]);

        // Process the uploaded CSV file
        $file = $request->file('file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));
        
        // Remove the header row
        unset($data[0]);

        // Collect data to update in a single connection
        $updateData = [];
        foreach ($data as $row) {
            $empId = $row[0];
            $salary = (string) $row[1];
            $dateOfJoining = $row[2];
            $employment_type = strtolower($row[3]) == 'part time' ? 'part_time' : 'full_time';

            $updateData[] = [
                'emp_id' => $empId,
                'offer_salary_month' => $salary,
                'joining_date' => $dateOfJoining,
                'employment_type' => $employment_type
            ];
        }

        // Update the database in a single connection
        DB::beginTransaction();
        // try {
            foreach ($updateData as $data) {
                $result = DB::table('employee_details')->where('employee_id', $data['emp_id'])->update([
                    'offer_salary_month' => $data['offer_salary_month'],
                    'joining_date' => $data['joining_date'],
                    'employment_type' => $data['employment_type']
                ]);
    
                if ($result === 0) {
                    // If update failed, store the data for later reference
                    $failedUpdates[] = $data['emp_id'];
                }
            } 
            DB::commit();
            // return $result;
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     return redirect()->back()->with('error', 'Failed to update data. Please try again.');
        // }
    
        // Check if any updates failed
        if (!empty($failedUpdates)) {
            $mess = 'There Are Some Issues While Updating These Emp Ids: ';
            foreach ($failedUpdates as $d) {
                $mess .= $d . ', ';
            }
            // Remove the trailing comma and space
            $mess = rtrim($mess, ', ');

            return redirect()->back()->with('mess', $mess);
            
        }
    
        return redirect()->back()->with('mess', 'Data Uploaded Successfully');
    }
    
    
    
    
    
    
    // Offer LEtter 
    
    public function ViewMyOl(){
        $this->emp = DB::table('employee_details')->where('employee_details.user_id',auth()->user()->id)
                     ->join('users','employee_details.user_id','users.id')
                     ->join('branches','employee_details.branch_id','branches.id')
                     ->join('designations','employee_details.designation_id','designations.id')
                     ->join('teams','employee_details.department_id','teams.id')
                     ->select('employee_details.*','users.*','teams.team_name','designations.name as designations_name','designations.id as d_id','branches.name as branch_name','branches.address as b_address')
                     ->first();
                     
        // if()
        // return $this->emp;
        if (
    $this->emp->joining_date >= '2025-11-01' &&
    $this->emp->joining_date <= '2025-11-25' &&
    in_array($this->emp->branch_id, [1, 7]) &&
    in_array($this->emp->department_id, [28, 31]) &&
    $this->emp->designation_id == 12
        ) {
            return view('letter.newol_after_1nov_voice_im', $this->data);
        }
        if (
            $this->emp->joining_date >= '2025-11-26' && 
            in_array($this->emp->branch_id, [1, 7]) && 
            in_array($this->emp->department_id, [28, 31]) && 
            $this->emp->designation_id == 12
        ) {
            return view('letter.newol_after_26nov_voice_im', $this->data);
        }
        // Check if the joining date is on or after January 1, 2025
        if ($this->emp && $this->emp->joining_date >= '2025-01-01') {
            // Redirect to the new letter 
            // return $this->emp->department_id;
            if (in_array($this->emp->department_id, [28, 31])  && in_array($this->emp->designation_id, [12])) {
                // return view('letter.newol_after1jan_voice', $this->data);
                return view('letter.newol_after1jan_all', $this->data);
            } else {
                return view('letter.newol_after1jan_all', $this->data); // Replace with your actual route
            }

        }
        return view('letter.ol',$this->data);
    }
    
    public function downloadViewMyOl()
{
    $this->emp = DB::table('employee_details')
        ->where('employee_details.user_id', auth()->user()->id)
        ->join('users', 'employee_details.user_id', 'users.id')
        ->join('branches', 'employee_details.branch_id', 'branches.id')
        ->join('designations', 'employee_details.designation_id', 'designations.id')
        ->join('teams', 'employee_details.department_id', 'teams.id')
        ->select(
            'employee_details.*',
            'users.*',
            'teams.team_name',
            'designations.name as designations_name',
            'designations.id as d_id',
            'branches.name as branch_name',
            'branches.address as b_address'
        )
        ->first();

    // Set the filename
    $filename = 'OfferLetter_' . $this->emp->employee_id . '_' . date('Y-m-d') . '.pdf';

    // Determine the view based on the logic
    $viewName = 'letter.ol'; // Default view

    if ($this->emp && $this->emp->joining_date >= '2025-01-01') {
        if (in_array($this->emp->department_id, [28, 31]) && in_array($this->emp->d_id, [12])) {
            $viewName = 'letter.newol_after1jan_voice';
        } else {
            $viewName = 'letter.newol_after1jan_all';
        }
    }else{
        $viewName = 'letter.ol';
    }

    // Load the appropriate view with data (assuming $this->data includes 'emp' or other required vars)
    $pdf = PDF::loadView($viewName, $this->data);

    // Set paper and margin options to prevent issues
    $pdf->setPaper('A2', 'portrait');
    $pdf->setOption('margin-top', 15);
    $pdf->setOption('margin-right', 20); // Increased to prevent right-side cutoff
    $pdf->setOption('margin-bottom', 15);
    $pdf->setOption('margin-left', 15);

    // Enable auto page break and other options
    $pdf->setOption('auto-page-break', false);
    $pdf->setOption('dpi', 150);
    $pdf->setOption('enable-local-file-access', true);
    $pdf->setOption('isHtml5ParserEnabled', true);
    $pdf->setOption('default-font', 'Helvetica');

    return $pdf->download($filename);
}
    
    
    public function ViewMyackold(){
        $this->emp = DB::table('employee_details')->where('employee_details.user_id',auth()->user()->id)
                     ->join('users','employee_details.user_id','users.id')
                     ->join('designations','employee_details.designation_id','designations.id')
                     ->join('teams','employee_details.department_id','teams.id')
                     ->select('employee_details.*','users.*','teams.team_name','designations.name as designations_name','employee_details.designation_id','employee_details.joining_date')
                     ->first();
                     
        if(!$this->emp){
            return 'Invalid Request! Please Try After Some Time';
        }
        // Array of specific designation IDs
        $designationsForAckLetter = [2, 12, 15, 78, 79, 46];
        
        if (in_array($this->emp->designation_id, $designationsForAckLetter)) {
            return view('ack_letter', $this->data);
        } else {
            return view('ack_letter_non_csa', $this->data);
        }
    }
    public function ViewMyack(){
        $this->emp = DB::table('employee_details')->where('employee_details.user_id',auth()->user()->id)
                     ->join('users','employee_details.user_id','users.id')
                     ->join('designations','employee_details.designation_id','designations.id')
                     ->join('teams','employee_details.department_id','teams.id')
                     ->select('employee_details.*','users.*','teams.team_name','designations.name as designations_name','employee_details.designation_id','employee_details.joining_date')
                     ->first();
                     
        if(!$this->emp){
            return 'Invalid Request! Please Try After Some Time';
        }
        
        if ($this->emp->joining_date >= '2025-08-21') {
            if($this->emp->department_id == 1 && $this->emp->branch_id == 1){
                return view('letter.new_ack_after_21_aug_blinkit', $this->data);
            }
            if(in_array($this->emp->department_id, [28,42,18,31,29]) && $this->emp->branch_id == 1){
                return view('letter.new_ack_after_21_aug_swiggy_im', $this->data);
            }
            if(in_array($this->emp->department_id, [43,42]) && $this->emp->branch_id == 7){
                return view('letter.new_ack_after_21_aug_black_orm', $this->data);
            }
            if(in_array($this->emp->department_id, [43,42]) && $this->emp->branch_id == 7){
                return view('letter.new_ack_after_21_aug_kolkatta_swiggy_im', $this->data);
            }
        }
        
    
        // Define department and designation IDs
        $internationalDepts = [20, 33, 24, 17, 14, 12, 35, 9, 7, 5, 4, 26, 25, 2];
        $csaDesignations = [2, 12, 15, 78, 79, 46];
        $ormDept = 43;
        
        $this->data['isInternational'] = in_array($this->emp->department_id, $internationalDepts);
        $this->data['isCSA'] = in_array($this->emp->designation_id, $csaDesignations);
        $this->data['isORM'] = ($this->emp->department_id == $ormDept);
    
        return view('ack_letter_new', $this->data);
    }
    
    public function viewMyExp(){
        $this->employee = DB::table('employee_details')->where('employee_details.user_id',auth()->user()->id)
                     ->join('users','employee_details.user_id','users.id')
                     ->join('designations','employee_details.designation_id','designations.id')
                     ->join('teams','employee_details.department_id','teams.id')
                     ->select('employee_details.*','users.*','teams.team_name','designations.name as designations_name')
                     ->first();
                     
        if(!$this->employee and !is_null($this->employee->last_date)){
            return 'Invalid Request! Please Try After Some Time';
        }
        // return $this->emp;
        return view('letter.new_exp',$this->data);
    }
    
    public function viewExp($user_id){
        $this->employee = DB::table('employee_details')->where('employee_details.user_id',$user_id)
                     ->join('users','employee_details.user_id','users.id')
                     ->join('branches','employee_details.branch_id','branches.id')
                     ->join('designations','employee_details.designation_id','designations.id')
                     ->join('teams','employee_details.department_id','teams.id')
                     ->select('employee_details.*','users.*','teams.team_name','designations.name as designations_name','branches.name as branch_name','branches.address as b_address')
                     ->first();
                     
        if(!$this->employee and !is_null($this->employee->last_date)){
            return 'Invalid Request! Please Try After Some Time';
        }
        // return $this->emp;
        return view('letter.new_exp',$this->data);
    }
    
    




}