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

class EmployeeController extends AccountBaseController
{
    use ImportExcel;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.employees';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('employees', $this->user->modules));
            return $next($request);
        });
        
        
        
        
    }

    /**
     * @param EmployeesDataTable $dataTable
     * @return mixed|void
     */
      
      
    public function knowledge(){
        return view('know',$this->data);
    }
     
     
    public function policies()
    {
        // dd(user()->policy_manage == 1);
        if(user()->policy_manage == 0 or in_array('admin', user_roles())){
             return redirect('/police_create');
        }else{
            return redirect('/police_accept');
           
        }
    }

    public function police_accept()
    {
        // if(user()->id != 1){
        //     return "Server Is Currently On Maintenance! Please Try After Some Time";
        // } 
        
        $this->emp = DB::table('employee_details')->where('employee_details.user_id',auth()->user()->id)
                     ->join('users','employee_details.user_id','users.id')
                     ->join('designations','employee_details.designation_id','designations.id')
                     ->join('teams','employee_details.department_id','teams.id')
                     ->join('branches','employee_details.branch_id','branches.id')
                     ->select('employee_details.*','users.*','teams.team_name','designations.name as designations_name','branches.name as branch_name','branches.address as b_address')
                     ->first();
        if(!$this->emp){
            return "Designation And Department Missing! Please Contact To Team HR";
        }
        
                     
        $policyIds = DB::table('pilicy_accept')->where('user_id',user()->id)->where('trash_status',0)->get();
        $this->policy = DB::table('policies')->where('trash_status',0)->get();
        $this->pageTitle = 'Policy';
        // $pushSetting = new \stdClass();
        // $pushSetting->status = 'Null';
        // $pusherSettings = new \stdClass();
        // $pusherSettings->status = 'Null';
        $p = [];
        
        $this->all_accept = false;
        if(count($policyIds) == count($this->policy)){
            $this->all_accept = true;
        }
        foreach ($policyIds as $policy) {
            $p[] = $policy->policy_id;
        }
        $this->policy_accepted = $p;
        return view('policy',$this->data);
    }
    
    
    public function policy_accept_user($id)
    {
        $this->pageTitle = 'Policy';
        $policyIds = DB::table('pilicy_accept')->insert([
            'policy_id' => $id,
            'user_id' => user()->id
            ]);
        $policyIds = DB::table('pilicy_accept')->where('user_id',user()->id)->get();
        $this->policy = DB::table('policies')->where('trash_status',0)->get();
       
        
        if(count($policyIds) >= count($this->policy)){
            session()->flash('show_confetti', true);
            return redirect('account/settings/profile-settings?tab=documents')->with('success','Policies Accepted Successfully!');
        }
        return redirect('/police_accept');
        
    } 
    
    public function policy_accept_all()
{
    $userId = user()->id;

    // Get all policy IDs that are not trashed
    $allPolicies = DB::table('policies')
        ->where('trash_status', 0)
        ->pluck('id')
        ->toArray();

    // Get already accepted policy IDs by user
    $acceptedPolicies = DB::table('pilicy_accept')
        ->where('user_id', $userId)
        ->pluck('policy_id')
        ->toArray();

    // Filter out policies that user already accepted
    $remainingPolicies = array_diff($allPolicies, $acceptedPolicies);

    // If already accepted all
    if (empty($remainingPolicies)) {
        return redirect('account/settings/profile-settings?tab=documents')
            ->with('info', 'You have already accepted all policies!');
    }

    // Insert only new ones
    $insertData = collect($remainingPolicies)->map(function ($policyId) use ($userId) {
        return [
            'policy_id' => $policyId,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    })->toArray();

    DB::table('pilicy_accept')->insert($insertData);

    // Show confetti ✅
    session()->flash('show_confetti', true);

    return redirect('account/settings/profile-settings?tab=documents')
        ->with('success', 'All remaining policies accepted successfully!');
}



    
    public function police_create()
    {
       $this->pageTitle = 'Policy'; 
        $this->policy = DB::table('policies')->where('trash_status',0)->get();
            return view('/police_create',$this->data);
        
    }  
    
    public function policy_change(Request $request,$id)
    {
        $this->policy = DB::table('policies')->where('id',$id)->update([
            'title' => $request->title,
            'policy' => $request->des,
        ]);
        return redirect('/police_create?mess="Policy Updated"');
        
    }
    
    public function policy_add(Request $request)
    {
        $this->pageTitle = 'Policy';
        $this->policy = DB::table('policies')->insert([
            'title' => $request->title,
            'policy' => $request->des,
        ]);
        return redirect('/police_create?mess="Policy Added"');
        
    } 
    
    public function policy_delete($id)
    {
        
        $this->policy = DB::table('policies')->where('id',$id)->update([
            'trash_status' => 1,
        ]);
        $this->policy = DB::table('pilicy_accept')->where('policy_id',$id)->update([
            'trash_status' => 1,
        ]);
        return redirect('/police_create?mess="Policy Deleted"');
        
    } 
    
    public function joining_form(){
         $this->pageTitle = 'Joining Form';
        $this->user_data = DB::table('users')->where('id',user()->id)->first();
        $this->refer = DB::table('reference')->where('user_id',user()->id)->get();
        $this->work = DB::table('work_exp')->where('user_id',user()->id)->get();
        $this->emp = DB::table('employee_details')->where('user_id',user()->id)->first();
        $this->bankDetails = DB::table('employee_bank_details')->where('user_id',user()->id)->first();
        return view('join_form',$this->data);
    }
    
    public function submit_join_form(Request $request){
        // dd($request->name);
        
        
        $rules = [
        // Personal / Contact
        'f_name' => 'required|string|max:255',
        'l_name' => 'required|string|max:255',
        'center_city' => 'nullable|string|max:100',
        'email' => 'required|email|max:255',
        'mobile' => 'required|digits:10',
        'alt_contact_no' => 'required|string|max:20',
        'relation_with_alt_no' => 'required|in:mother,father,spouse,other',
        'relation_with_alt_name' => 'nullable|string|max:255',
        'father_name' => 'required|string|max:255',
        'mother_name' => 'required|string|max:255',
        'last_education' => 'required|string|max:50',
        'date_of_birth' => 'required|date',
        'age' => 'required|integer',
        'gender' => 'required|in:male,female,others',
        'blood_group' => 'nullable|string|max:10',
        'nationality' => 'nullable|string|max:100',

        // Address
        'local_add' => 'required|string|max:500',
        'local_city' => 'required|string|max:100',
        'local_state' => 'required|string|max:100',
        'local_pin' => 'required|string|max:20',
        'per_add' => 'required|string|max:500',
        'per_city' => 'required|string|max:100',
        'per_state' => 'required|string|max:100',
        'per_code' => 'required|string|max:20',
        'aadhar_no' => 'required|string|max:20',
        'pan_no' => 'required|string|max:20',
        'driving_no' => 'nullable|string|max:50',
        'medical_issue' => 'nullable|string|max:500',

        // Account Details
        'bank_name' => 'required|string|max:255',
        'acc_holder_name' => 'required|string|max:255',
        'account_number' => 'required|string|max:50',
        'branch_name' => 'required|string|max:255',
        'account_type' => 'nullable|in:savings,current,salary',
        'ifsc_code' => 'required|string|max:20',
    ];

    $messages = [
        // Personal / Contact
        'f_name.required' => 'First Name is required.',
        'l_name.required' => 'Last Name is required.',
        'email.required' => 'Email is required.',
        'mobile.required' => 'Mobile number is required.',
        'mobile.digits' => 'Mobile number must be exactly 10 digits.',
        'relation_with_alt_no.required' => 'Please select a relation for the alternate contact.',
        'relation_with_alt_no.in' => 'Invalid relation selected.',
        'father_name.required' => 'Father\'s Name is required.',
        'mother_name.required' => 'Mother\'s Name is required.',
        'last_education.required' => 'Last Qualification is required.',
        'date_of_birth.required' => 'Date of Birth is required.',
        'age.required' => 'Age is required.',
        'gender.required' => 'Gender is required.',

        // Address
        'local_add.required' => 'Current Address is required.',
        'local_city.required' => 'Current City is required.',
        'local_state.required' => 'Current State is required.',
        'local_pin.required' => 'Current Pin is required.',
        'per_add.required' => 'Permanent Address is required.',
        'per_city.required' => 'Permanent City is required.',
        'per_state.required' => 'Permanent State is required.',
        'per_code.required' => 'Permanent Pin is required.',
        'aadhar_no.required' => 'Aadhaar number is required.',
        'pan_no.required' => 'PAN number is required.',

        // Bank
        'bank_name.required' => 'Bank Name is required.',
        'acc_holder_name.required' => 'Account Holder Name is required.',
        'account_number.required' => 'Account Number is required.',
        'branch_name.required' => 'Branch Name is required.',
        'ifsc_code.required' => 'IFSC Code is required.',
    ];

    // Validate request
    $validated = $request->validate($rules, $messages);
        
        
        $user_id = user()->id;
        if($request->reference_name){
            $loop = count($request->reference_name);
            // return $loop;
            for ($i = 0; $i < $loop; $i++) {
              $save = DB::table('reference')->insert([
                  'user_id' => user()->id,
                  'reference_name' => $request->reference_name[$i],
                  'reference_contact' => $request->reference_contact[$i],
                  'reference_relation' => $request->reference_relation[$i],
                  ]);
            }
        }
        if($request->experience_designation){
            $loop = count($request->experience_designation);
            for ($i = 0; $i < $loop; $i++) {
              $save = DB::table('work_exp')->insert([
                  'user_id' => user()->id,
                  'designation' => $request->experience_designation[$i],
                  'org' => $request->experience_organization[$i],
                  'ctc' => $request->experience_ctc[$i],
                  'reason_leavingc' => $request->experience_reason_leaving[$i],
                  ]);
            }
        }
        
        $save = DB::table('employee_details')->where('user_id',user()->id)->update([
            'f_name' => ucfirst(strtolower($request->f_name)),
            'l_name' => $request->l_name === 'NA' ? '' : ucfirst(strtolower($request->l_name)),
            'center_city' => $request->center_city,
            'center_state' => $request->center_state,
            'alt_contact_no' => $request->alt_contact_no,
            'father_name' => ucfirst(strtolower($request->father_name)),
            'father_occ' => $request->father_occ,
            'f_contact' => $request->f_contact,
            'mother_name' => ucfirst(strtolower($request->mother_name)),
            'mother_occ' => $request->mother_occ,
            'm_contact' => $request->m_contact,
            'guardian_name' => $request->guardian_name, 
            'guardian_occ' => $request->guardian_occ,
            'g_contact' => $request->g_contact,
            'guardian_relation' => $request->guardian_relation,
            'date_of_birth' => $request->date_of_birth,
            'documented_dob' => $request->documented_dob,
            'age' => $request->age,
            'blood_group' => $request->blood_group,
            'nationality' => $request->nationality,
            'medical_issue' => $request->medical_issue,
            'local_add' => $request->local_add,
            'local_city' => $request->local_city,
            'local_state' => $request->local_state,
            'local_pin' => $request->local_pin,
            'per_add' => $request->per_add,
            'per_city' => $request->per_city,
            'per_state' => $request->per_state,
            'per_code' => $request->per_code,
            'aadhar_no' => $request->aadhar_no,
            'pan_no' => $request->pan_no, 
            'driving_no' => $request->driving_no,
            'last_education' => $request->last_education,
            'relation_with_alt_no' => $request->relation_with_alt_no,
            'relation_with_alt_name' => $request->relation_with_alt_name,
            
            
            ]);
            $requiredKeys = ['name', 'gender', 'mobile','date_of_birth', 'father_name', 'local_add','aadhar_no'];
            $filled = 0;
            foreach ($requiredKeys as $key) {
                if (!$request->has($key)) {
                    // Handle the case when a required key is missing
                    $filled = 1;
                }
            }
            $save_user = DB::table('users')->where('id',$user_id)->update([
                // 'name' => $request->name,
                'gender' => $request->gender,
                'mobile' => $request->mobile,
                'form_filled' => 0
            ]);
            
            // return $save;
            $this->updateBankDetails($request, user()->id);
            
            return redirect('police_accept')->with('success','Form Submitted Successfully! Please Upload Signature And Accept All Policies');
            // return redirect()->back();
    }
    
    private function updateBankDetails($request, $userId)
    {
        $validatedData = $request->validate([
            'bank_name' => 'required',
            'account_number' => 'required',
            'ifsc_code' => 'required',
            'branch_name' => 'nullable',
            'acc_holder_name' => 'required',
        ]);
    
        // Check if bank details already exist for the user
        $existingRecord = DB::table('employee_bank_details')->where('user_id', $userId)->first();
    
        if ($existingRecord) {
            // Update existing bank details
            DB::table('employee_bank_details')->where('user_id', $userId)->update($validatedData);
        } else {
            // Add new bank details
            DB::table('employee_bank_details')->insert(array_merge($validatedData, ['user_id' => $userId]));
        }
    }


    public function index(EmployeesDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_employees');

        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        if (!request()->ajax()) {
            $query = DB::table('users');
            $query->join('employee_details', 'employee_details.user_id', 'users.id');
            
            if (isset($_GET['branch'])) {
                if ($_GET['branch'] != 'all') {
                    $b_id = (int) $_GET['branch'];
                    $query->where('employee_details.branch_id', $b_id);
                }
            }
            
            if (auth()->user()->id == 1906) {
                $query->where('employee_details.branch_id', 5);
            }
            if (auth()->user()->id == 13) {
                $query->where('employee_details.branch_id', 5);
            }
            if (auth()->user()->id == 5437) {
                $query->where('employee_details.branch_id', 8);
            }
            // if (auth()->user()->id == 13884) {
            //     $query->where('employee_details.branch_id', 7);
            // }
            if (in_array(auth()->user()->id, [11405,13884])) {
                $query->where('employee_details.branch_id', 7);
            }
            
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 'active') {
                    $query->where('users.status', 'active');
                } elseif ($_GET['status'] == 'deactive') {
                    $query->where('users.status', 'deactive');
                }
            } else {
                $query->where('users.status', 'active');
            }
            
            if (isset($_GET['department']) && $_GET['department'] != 'all') {
                $d_id = (int) $_GET['department'];
                $query->whereRaw('ABS(department_id) = ?', [$d_id]);
            }
            
            if (isset($_GET['designation']) && $_GET['designation'] != 'all') {
                $ds_id = (int) $_GET['designation'];
                $query->whereRaw('ABS(designation_id) = ?', [$ds_id]);
            }
            
            if (isset($_GET['search']) && trim($_GET['search']) !== '') {

                $search = trim($_GET['search']);
            
                // Check if comma-separated values exist
                if (strpos($search, ',') !== false) {
            
                    // Convert to array & clean values
                    $ids = array_filter(array_map('trim', explode(',', $search)));
            
                    $query->whereIn('employee_details.employee_id', $ids);
            
                } else {
            
                    // Normal search (name / username / employee_id)
                    $query->where(function ($query) use ($search) {
                        $query->where('users.name', 'LIKE', '%' . $search . '%')
                              ->orWhere('users.username', 'LIKE', '%' . $search . '%')
                              ->orWhere('employee_details.employee_id', 'LIKE', '%' . $search . '%');
                    });
                }
            }

            
            if (isset($_GET['offer_letter'])) {
                $offer = $_GET['offer_letter'];
                if ($offer == 'yes') {
                    $query->whereNotNull('employee_details.offer_salary_month')
                          ->whereNotNull('employee_details.joining_date')
                          ->whereNotNull('employee_details.employment_type');
                } elseif ($offer == 'no') {
                    $query->where(function($query) {
                        $query->whereNull('employee_details.offer_salary_month')
                              ->orWhereNull('employee_details.joining_date')
                              ->orWhereNull('employee_details.employment_type');
                    });
                }
            }
            
            if (isset($_GET['bio_met'])) {
                if ($_GET['bio_met'] == 'no') {
                    $query->whereNull('employee_details.bio_uid');
                } elseif ($_GET['bio_met'] == 'yes') {
                    $query->whereNotNull('employee_details.bio_uid');
                }
            }
            if (isset($_GET['form_filled']) && $_GET['form_filled'] !="all") {
                if ($_GET['form_filled'] == '1') {
                    $query->where('users.form_filled',1);
                } elseif ($_GET['form_filled'] == '0') {
                    $query->where('users.form_filled',0);
                }
            }
            
            if (isset($_GET['type']) && $_GET['type'] !== 'all') {
                if ($_GET['type'] == 'full_time') {
                    $query->where('employee_details.employment_type', 'full_time');
                } elseif ($_GET['type'] == 'part_time') {
                    $query->where('employee_details.employment_type', 'part_time');
                }
            }
            
            // Additional joins
            $query->leftJoin('branches', 'branches.id', '=', 'employee_details.branch_id')
                  ->leftJoin('designations', 'designations.id', '=', 'employee_details.designation_id')
                  ->leftJoin('teams', 'teams.id', '=', 'employee_details.department_id')
                  ->leftJoin('employee_bank_details', 'employee_bank_details.user_id', '=', 'users.id');
            
             $query->select(
            'users.id as user_iddd',
            'users.name as employee_name',
            'users.email',
            'users.status as status',
            'employee_details.employee_id',
            'employee_details.local_add',
            'employee_details.per_add',
            'employee_details.joining_date',
            'employee_details.designation_id',
            // 'teams.name as deapartment',
            'designations.name as designation_name',
            'branches.name as branche_name',
            'teams.team_name as deapartment',
            DB::raw('
                CASE
                    WHEN (
                        SELECT COUNT(*) FROM employee_docs 
                        WHERE user_id = users.id AND 
                              (
                                name LIKE "%Adhar Card%" OR 
                                name LIKE "%Pan Card%" OR 
                                name LIKE "%10th Marksheet%" OR 
                                name LIKE "%12th Marksheet%" OR 
                                name LIKE "%Passport Size Photo%" OR 
                                name LIKE "%Acknowledgment Letter%"
                              )
                    ) = 0 
                        THEN "not_uploaded"
                    WHEN (
                        SELECT COUNT(DISTINCT
                            CASE
                                WHEN name LIKE "%Adhar Card%" THEN "Adhar"
                                WHEN name LIKE "%Pan Card%" THEN "Pan"
                                WHEN name LIKE "%10th Marksheet%" THEN "10th"
                                WHEN name LIKE "%12th Marksheet%" THEN "12th"
                                WHEN name LIKE "%Passport Size Photo%" THEN "Photo"
                                WHEN name LIKE "%Acknowledgment Letter%" THEN "Acknowledgment Letter"
                            END
                        )
                        FROM employee_docs 
                        WHERE user_id = users.id
                    ) = 6 
                        THEN "completed"
                    ELSE "partial"
                END as document_status
            '),
            DB::raw('
                CASE 
                    WHEN employee_bank_details.user_id IS NOT NULL THEN "Yes"
                    ELSE "No"
                END as account_details
            ')
            )->orderBy('users.id', 'DESC');
            
//             if (!empty($_GET['uploaded_status']) && isset($_GET['documents']) && is_array($_GET['documents'])) {
//     $uploadedStatus = $_GET['uploaded_status']; // uploaded / not_uploaded
//     $selectedDocs = $_GET['documents']; // array of doc names

//     if ($uploadedStatus == 'uploaded') {
//         // Employee MUST have ALL selected docs
//         foreach ($selectedDocs as $docName) {
//             $query->whereExists(function ($q) use ($docName) {
//                 $q->select(DB::raw(1))
//                   ->from('employee_docs')
//                   ->whereRaw('employee_docs.user_id = users.id')
//                   ->where('employee_docs.name', 'LIKE', '%' . addslashes($docName) . '%');
//             });
//         }
//     } elseif ($uploadedStatus == 'not_uploaded') {
//         // Employee MUST NOT have ANY of the selected docs
//         foreach ($selectedDocs as $docName) {
//             $query->whereNotExists(function ($q) use ($docName) {
//                 $q->select(DB::raw(1))
//                   ->from('employee_docs')
//                   ->whereRaw('employee_docs.user_id = users.id')
//                   ->where('employee_docs.name', 'LIKE', '%' . addslashes($docName) . '%');
//             });
//         }
//     }
// }








            
            // Document status filter
            if (isset($_GET['doc']) && $_GET['doc'] != 'all') {
                $docFilter = $_GET['doc'];
                $query->having('document_status', '=', $docFilter);
            }
            if (isset($_GET['account_status']) && $_GET['account_status'] != 'all') {
                if ($_GET['account_status'] == 'completed') {
                    $query->whereNotNull('employee_bank_details.user_id');
                } elseif ($_GET['account_status'] == 'pending') {
                    $query->whereNull('employee_bank_details.user_id');
                }
            }

            
            $totalEmployees = $query->count();
            $perPage = 20;
            $employees = $query->paginate($perPage);

            $this->employees = $employees;
            $this->totalEmployees = $totalEmployees;
            $doc = array(); 
            
            $this->doc = $doc;
            $this->skills = Skill::all();
            $this->departments = Team::all();
            $this->designations = Designation::allDesignations();
            $this->branches = Branch::allBranches();
            $this->roles = Role::where('name', '<>', 'client')
                ->orderBy('id')->get();
        }
        
        // dd($this);
        return $dataTable->render('employees.index', $this->data);
    }
    
    
    
    
    function getDocumentStatus($documents) {
        $requiredDocs = ["Adhar Card", "Pan Card", "10th Marksheet", "12th Marksheet", "Passport Size Photo"];
        $docNames = $documents->pluck('name')->toArray();
    
        foreach ($requiredDocs as $doc) {
            if (!in_array($doc, $docNames)) {
                return 'partial';
            }
        }
    
        if (count($documents) < 1) {
            return 'not_uploaded';
        }
    
        return 'completed';
    }


    
    



    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->pageTitle = __('app.add') . ' ' . __('app.employee');

        $addPermission = user()->permission('add_employees');
        abort_403(!in_array($addPermission, ['all', 'added']));
        $latest = EmployeeDetails::latest('id')->first();
        $last_employee_id = $latest->employee_id;
        $current_month_year = date('my');
        if (substr($last_employee_id, 3, 4) != $current_month_year) {
        $new_employee_id = $current_month_year . '001';
        } else {
        $last_employee_number = intval(substr($last_employee_id, -3));
        $new_employee_number = $last_employee_number + 1;
        $new_employee_id = $current_month_year . str_pad($new_employee_number, 3, '0', STR_PAD_LEFT);
        }


        $this->teams = Team::all();
        $this->designations = Designation::allDesignations();
        $this->branches = Branch::allBranches();
        $this->nif = "NIF";
        $this->lastEmployeeID = $new_employee_id;
        $this->skills = Skill::all()->pluck('name')->toArray();
        $this->countries = countries();
        $this->employees = User::allEmployees(null, true);
        $this->languages = LanguageSetting::where('status', 'enabled')->get();
        $this->roles = Role::where('name', '<>', 'client')->get();

        $employee = new EmployeeDetails();

        if (!empty($employee->getCustomFieldGroupsWithFields())) {
            $this->fields = $employee->getCustomFieldGroupsWithFields()->fields;
        }

        $this->view = 'employees.ajax.create';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }
        //  dd($this->lastEmployeeID);

        return view('employees.create', $this->data);

    }

    public function assignRole(Request $request)
    {
        $changeEmployeeRolePermission = user()->permission('change_employee_role');

        abort_403($changeEmployeeRolePermission != 'all');

        $userId = $request->userId;
        $roleId = $request->role;
        $employeeRole = Role::where('name', 'employee')->first();

        $user = User::withoutGlobalScope(ActiveScope::class)->findOrFail($userId);

        RoleUser::where('user_id', $user->id)->delete();
        $user->roles()->attach($employeeRole->id);

        if ($employeeRole->id != $roleId) {
            $user->roles()->attach($roleId);
        }

        $user->assignUserRolePermission($roleId);

        $userSession = new AppSettingController();
        $userSession->deleteSessions([$user->id]);

        return Reply::success(__('messages.roleAssigned'));
    }

    /**
     * @param StoreRequest $request
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(StoreRequest $request)
    {
        $addPermission = user()->permission('add_employees');
        abort_403(!in_array($addPermission, ['all', 'added']));

        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = $request->employee_id;
            $user->password = bcrypt($request->employee_id);
            $user->mobile = $request->mobile;
            //$user->country_id = $request->country;
            $user->gender = $request->gender;
            $user->locale = 'en';

            if ($request->has('login')) {
                $user->login = $request->login;
            }

            if ($request->has('email_notifications')) {
                $user->email_notifications = $request->email_notifications ? 1 : 0;
            }

            if ($request->hasFile('image')) {
                Files::deleteFile($user->image, 'avatar');
                $user->image = Files::upload($request->image, 'avatar', 300);
            }

            if ($request->has('telegram_user_id')) {
                $user->telegram_user_id = $request->telegram_user_id;
            }

            $user->save();

            $tags = json_decode($request->tags);

            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    // check or store skills
                    $skillData = Skill::firstOrCreate(['name' => strtolower($tag->value)]);

                    // Store user skills
                    $skill = new EmployeeSkill();
                    $skill->user_id = $user->id;
                    $skill->skill_id = $skillData->id;
                    $skill->save();
                }
            }

            if ($user->id) {
                $employee = new EmployeeDetails();
                $employee->user_id = $user->id;
                $this->employeeData($request, $employee);
                $employee->save();

                // To add custom fields data
                if ($request->custom_fields_data) {
                    $employee->updateCustomFieldData($request->custom_fields_data);
                }
            }

                    $employeeRole = Role::where('name', 'employee')->first();
                    $user->attachRole($employeeRole);
                    $user->assignUserRolePermission($employeeRole->id);
                    $this->logSearchEntry($user->id, $user->name, 'employees.show', 'employee');            

            // Commit Transaction
            DB::commit();




        } catch (\Swift_TransportException $e) {
            // Rollback Transaction
            DB::rollback();

            return Reply::error('Please configure SMTP details to add employee. Visit Settings -> notification setting to set smtp '.$e->getMessage(), 'smtp_error');
        } catch (\Exception $e) {
            logger($e->getMessage());
            // Rollback Transaction
            DB::rollback();

            return Reply::error('Some error occurred when inserting the data. Please try again or contact support '. $e->getMessage());
        }


        if (request()->add_more == 'true') {
            $html = $this->create();

            return Reply::successWithData(__('messages.recordSaved'), ['html' => $html, 'add_more' => true]);
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('employees.index')]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function applyQuickAction(Request $request)
    {
        switch ($request->action_type) {
        case 'delete':
            $this->deleteRecords($request);

            return Reply::success(__('messages.deleteSuccess'));
        case 'change-status':
            $this->changeStatus($request);

            return Reply::success(__('messages.updateSuccess'));
        default:
            return Reply::error(__('messages.selectAction'));
        }
    }

    protected function deleteRecords($request)
    {
        abort_403(user()->permission('delete_employees') != 'all');

        User::withoutGlobalScope(ActiveScope::class)->whereIn('id', explode(',', $request->row_ids))->delete();
    }

    protected function changeStatus($request)
    {
        abort_403(user()->permission('edit_employees') != 'all');

        User::withoutGlobalScope(ActiveScope::class)->whereIn('id', explode(',', $request->row_ids))->update(['status' => $request->status]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->employee = User::withoutGlobalScope(ActiveScope::class)->with('employeeDetail', 'reportingTeam')->findOrFail($id);

        $this->editPermission = user()->permission('edit_employees');

        $userRoles = $this->employee->roles->pluck('name')->toArray();

        abort_403(!in_array('admin', user_roles()) && in_array('admin', $userRoles));

        abort_403(!($this->editPermission == 'all'
            || ($this->editPermission == 'added' && $this->employee->employeeDetail->added_by == user()->id)
            || ($this->editPermission == 'owned' && $this->employee->employeeDetail->reporting_to == user()->id)
            || ($this->editPermission == 'both' && ($this->employee->employeeDetail->reporting_to == user()->id || $this->employee->employeeDetail->added_by == user()->id))
        ));

        $this->pageTitle = __('app.update') . ' ' . __('app.employee');
        $this->skills = Skill::all()->pluck('name')->toArray();
        $this->teams = Team::allDepartments();
        $this->designations = Designation::allDesignations();
        $this->branches = Branch::allBranches();
        $this->bankDetails = DB::table('employee_bank_details')->where('user_id', $id)->first();
        //$this->countries = countries();
        //$this->languages = LanguageSetting::where('status', 'enabled')->get();
        $exceptUsers = [$id];
        $this->roles = Role::where('name', '<>', 'client')->get();
        $this->userRoles = $this->employee->roles->pluck('name')->toArray();

        /** @phpstan-ignore-next-line */
        if (count($this->employee->reportingTeam) > 0) {
            /** @phpstan-ignore-next-line */
            $exceptUsers = array_merge($this->employee->reportingTeam->pluck('user_id')->toArray(), $exceptUsers);
        }

        $this->employees = User::allEmployees($exceptUsers, true);

        if (!is_null($this->employee->employeeDetail)) {
            $this->employeeDetail = $this->employee->employeeDetail->withCustomFields();

            if (!empty($this->employeeDetail->getCustomFieldGroupsWithFields())) {
                $this->fields = $this->employeeDetail->getCustomFieldGroupsWithFields()->fields;
            }
        }

        if (request()->ajax()) {
            $html = view('employees.ajax.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'employees.ajax.edit';

        return view('employees.create', $this->data);

    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function update(UpdateRequest $request, $id)
    {

        $user = User::withoutGlobalScope(ActiveScope::class)->findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password != '') {
            $user->password = bcrypt($request->password);
        }

        $user->mobile = $request->mobile;
        $user->country_id = $request->country;
        $user->gender = $request->gender;
        $user->locale = $request->locale;

        if (request()->has('status')) {
            $user->status = $request->status;
        }
        
        if (request()->has('policy') && $request->policy == '0') {
            $user->policy_manage =$request->policy;
        }

        if ($id != user()->id) {
            $user->login = $request->login;
        }

        if ($request->has('email_notifications')) {
            $user->email_notifications = $request->email_notifications;
        }

        if ($request->image_delete == 'yes') {
            Files::deleteFile($user->image, 'avatar');
            $user->image = null;
        }

        if ($request->hasFile('image')) {

            Files::deleteFile($user->image, 'avatar');
            $user->image = Files::upload($request->image, 'avatar', 300);
        }

        if ($request->has('telegram_user_id')) {
            $user->telegram_user_id = $request->telegram_user_id;
        }

        $user->save();
        
        // Check if a record already exists for the user
        $existingRecord = DB::table('employee_bank_details')->where('user_id', $id)->first();
        
        // Prepare data to update or insert
        $bank_data = [];
        if ($request->has('bank_name')) {
            $bank_data['bank_name'] = $request->input('bank_name');
        }
        if ($request->has('account_number')) {
            $bank_data['account_number'] = $request->input('account_number');
        }
        if ($request->has('ifsc_code')) {
            $bank_data['ifsc_code'] = $request->input('ifsc_code');
        }
        if ($request->has('branch_name')) {
            $bank_data['branch_name'] = $request->input('branch_name');
        }
        
        // If no data is provided, skip the operation
        if (!empty($bank_data) && $request->bank_name != '') {
            if ($existingRecord) {
                // Update existing bank details
                DB::table('employee_bank_details')->where('user_id', $id)->update($bank_data);
            } else {
                // Add the user_id to the data and insert a new record
                $bank_data['user_id'] = $id;
                DB::table('employee_bank_details')->insert($bank_data);
            }
        }



        $roleId = request()->role;

        $userRole = Role::where('id', request()->role)->first();

        if ($roleId != '' && $userRole->name != $user->user_other_role) {

            $employeeRole = Role::where('name', 'employee')->first();

            $user = User::withoutGlobalScope(ActiveScope::class)->findOrFail($user->id);

            RoleUser::where('user_id', $user->id)->delete();
            $user->roles()->attach($employeeRole->id);

            if ($employeeRole->id != $roleId) {
                $user->roles()->attach($roleId);
            }

            $user->assignUserRolePermission($roleId);

            $userSession = new AppSettingController();
            $userSession->deleteSessions([$user->id]);
        }

        $tags = json_decode($request->tags);

        if (!empty($tags)) {
            EmployeeSkill::where('user_id', $user->id)->delete();

            foreach ($tags as $tag) {
                // Check or store skills
                $skillData = Skill::firstOrCreate(['name' => strtolower($tag->value)]);

                // Store user skills
                $skill = new EmployeeSkill();
                $skill->user_id = $user->id;
                $skill->skill_id = $skillData->id;
                $skill->save();
            }
        }

        $employee = EmployeeDetails::where('user_id', '=', $user->id)->first();

        if (empty($employee)) {
            $employee = new EmployeeDetails();
            $employee->user_id = $user->id;
        }

        // Store old employee_id to check if it changed
        $oldEmployeeId = $employee->employee_id;

        $this->employeeData($request, $employee);

        // If employee_id changed, update users.username and password to match new employee_id
        if ($request->has('employee_id') && $request->employee_id != '' && $oldEmployeeId != $request->employee_id) {
            $user->username = $request->employee_id;
            $user->password = bcrypt($request->employee_id); // Set password to employee_id
            $user->save();
        }

        $employee->last_date = null;

        if ($request->last_date != '') {
            $employee->last_date = Carbon::createFromFormat($this->company->date_format, $request->last_date)->format('Y-m-d');
        }

        $employee->save();

        // To add custom fields data
        if ($request->custom_fields_data) {
            $employee->updateCustomFieldData($request->custom_fields_data);
        }

        if (user()->id == $user->id) {
            session()->forget('user');
        }

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('employees.index')]);
    }

    /**
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        $user = User::withoutGlobalScope(ActiveScope::class)->findOrFail($id);
        $this->deletePermission = user()->permission('delete_employees');

        abort_403(!($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $user->employeeDetail->added_by == user()->id)));


        if ($user->hasRole('admin') && !in_array('admin', user_roles())) {
            return Reply::error(__('messages.adminCannotDelete'));
        }

        $universalSearches = UniversalSearch::where('searchable_id', $id)->where('module_type', 'employee')->get();

        if ($universalSearches) {
            foreach ($universalSearches as $universalSearch) {
                UniversalSearch::destroy($universalSearch->id);
            }
        }


        User::withoutGlobalScope(ActiveScope::class)->where('id', $id)->delete();
        Notification::where('data', 'like', '{"user_id":' . $id . ',%')->delete();

        $deleteSession = new AppSettingController();
        $deleteSession->deleteSessions([$id]);

        return Reply::success(__('messages.deleteSuccess'));

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->employee = User::with(['employeeDetail.designation', 'employeeDetail.department', 'employeeDetail.branch','appreciations', 'appreciations.award', 'appreciations.award.awardIcon', 'employeeDetail.reportingTo', 'country', 'emergencyContacts', 'reportingTeam' => function($query) {
            $query->join('users', 'users.id', '=', 'employee_details.user_id');
            $query->where('users.status', '=', 'active');
        }, 'reportingTeam.user', 'leaveTypes', 'leaveTypes.leaveType', 'appreciationsGrouped', 'appreciationsGrouped.award', 'appreciationsGrouped.award.awardIcon'])
        ->withoutGlobalScope(ActiveScope::class)
        ->withOut('clientDetails', 'role')
        ->withCount('member', 'agents', 'openTasks')
        ->findOrFail($id);

        $this->employeeLanguage = LanguageSetting::where('language_code', $this->employee->locale)->first();
        $this->viewPermission = user()->permission('view_employees');

        if (!$this->employee->hasRole('employee')) {
            abort(404);
        }
        $this->bankDetails = DB::table('employee_bank_details')->where('user_id', $id)->first();

        abort_403(in_array('client', user_roles()));

        $tab = request('tab');

        if (
            $this->viewPermission == 'all'
            || ($this->viewPermission == 'added' && $this->employee->employeeDetail->added_by == user()->id)
            || ($this->viewPermission == 'owned' && $this->employee->employeeDetail->reporting_to == user()->id)
            || ($this->viewPermission == 'both' && ($this->employee->employeeDetail->reporting_to == user()->id || $this->employee->employeeDetail->added_by == user()->id))
        ) {

            if ($tab == '') {  // Works for profile

                $this->fromDate = now()->timezone($this->company->timezone)->startOfMonth()->toDateString();
                $this->toDate = now()->timezone($this->company->timezone)->endOfMonth()->toDateString();

                $this->lateAttendance = Attendance::whereBetween(DB::raw('DATE(`clock_in_time`)'), [$this->fromDate, $this->toDate])
                    ->where('late', 'yes')->where('user_id', $id)->count();

                $this->leavesTaken = Leave::selectRaw('count(*) as count, SUM(if(duration="half day", 1, 0)) AS halfday')
                    ->where('user_id', $id)
                    ->where('status', 'approved')
                    ->whereBetween(DB::raw('DATE(`leave_date`)'), [$this->fromDate, $this->toDate])
                    ->first();

                $this->leavesTaken = (!is_null($this->leavesTaken)) ? $this->leavesTaken->count - ($this->leavesTaken->halfday * 0.5) : 0;

                $this->taskChart = $this->taskChartData($id);
                $this->ticketChart = $this->ticketChartData($id);

                if (!is_null($this->employee->employeeDetail)) {
                    $this->employeeDetail = $this->employee->employeeDetail->withCustomFields();

                    $customFields = $this->employeeDetail->getCustomFieldGroupsWithFields();

                    if (!empty($customFields)) {
                        $this->fields = $customFields->fields;
                    }
                }

                $taskBoardColumn = TaskboardColumn::completeColumn();

                $this->taskCompleted = Task::join('task_users', 'task_users.task_id', '=', 'tasks.id')
                    ->where('task_users.user_id', $id)
                    ->where('tasks.board_column_id', $taskBoardColumn->id)
                    ->count();

                $hoursLogged = ProjectTimeLog::where('user_id', $id)->sum('total_minutes');
                $breakMinutes = ProjectTimeLogBreak::userBreakMinutes($id);

                $timeLog = intdiv($hoursLogged - $breakMinutes, 60);

                $this->hoursLogged = $timeLog;
            }

        }

        $this->pageTitle = ucfirst($this->employee->name);
        $viewDocumentPermission = user()->permission('view_documents');
        $viewImmigrationPermission = user()->permission('view_immigration');

        switch ($tab) {
        case 'tickets':
            return $this->tickets();
        case 'projects':
            return $this->projects();

        case 'tasks':
            return $this->tasks();
        case 'leaves':
            return $this->leaves();
        case 'timelogs':
            return $this->timelogs();
        case 'documents':
            abort_403(($viewDocumentPermission == 'none'));
            $this->view = 'employees.ajax.documents';
            break;
        case 'emergency-contacts':
            $this->view = 'employees.ajax.emergency-contacts';
            break;
        case 'appreciation':
            $this->appreciations = $this->appreciation($this->employee->id);
            $this->view = 'employees.ajax.appreciations';
            break;
        case 'leaves-quota':
            $this->leaveTypes = LeaveType::byUser($this->employee);
            $this->leavesTakenByUser = Leave::byUserCount($this->employee);
            $this->employeeLeavesQuotas = $this->employee->leaveTypes;
            $this->employeeLeavesQuota = clone $this->employeeLeavesQuotas;
            $allowedLeaves = clone $this->employeeLeavesQuotas;
            $this->allowedLeaves = $allowedLeaves->sum('no_of_leaves');
            $this->view = 'employees.ajax.leaves_quota';
            break;
        case 'shifts':
            abort_403(user()->permission('view_shift_roster') != 'all');
            $this->view = 'employees.ajax.shifts';
            break;
        case 'permissions':
            abort_403(user()->permission('manage_role_permission_setting') != 'all');

            $this->modulesData = Module::with('permissions')->withCount('customPermissions')->get();
            $this->view = 'employees.ajax.permissions';
            break;

        case 'activity':
            $this->activities = UserActivity::where('user_id', $id)->orderBy('id', 'desc')->get();
            $this->view = 'employees.ajax.activity';
            break;

        case 'immigration':
            abort_403($viewImmigrationPermission == 'none');
            $this->passport = Passport::with('country')->where('user_id', $this->employee->id )->first();
            $this->visa = VisaDetail::with('country')->where('user_id', $this->employee->id)->get();
            $this->view = 'employees.ajax.immigration';
            break;

        default:
            $this->view = 'employees.ajax.profile';
            break;
        }

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['views' => $this->view, 'status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->activeTab = $tab ?: 'profile';

        return view('employees.show', $this->data);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return array
     */
    public function taskChartData($id)
    {
        $taskStatus = TaskboardColumn::all();
        $data['labels'] = $taskStatus->pluck('column_name');
        $data['colors'] = $taskStatus->pluck('label_color');
        $data['values'] = [];

        foreach ($taskStatus as $label) {
            $data['values'][] = Task::join('task_users', 'task_users.task_id', '=', 'tasks.id')
                ->where('task_users.user_id', $id)->where('tasks.board_column_id', $label->id)->count();
        }

        return $data;
    }

    /**
     * XXXXXXXXXXX
     *
     * @return array
     */
    public function ticketChartData($id)
    {
        $labels = ['open', 'pending', 'resolved', 'closed'];
        $data['labels'] = [__('app.open'), __('app.pending'), __('app.resolved'), __('app.closed')];
        $data['colors'] = ['#D30000', '#FCBD01', '#2CB100', '#1d82f5'];
        $data['values'] = [];

        foreach ($labels as $label) {
            $data['values'][] = Ticket::where('agent_id', $id)->where('status', $label)->count();
        }

        return $data;
    }

    public function byDepartment($id)
    {
        $users = User::join('employee_details', 'employee_details.user_id', '=', 'users.id');

        if ($id != 0) {
            $users = $users->where('employee_details.department_id', $id);
        }

        $users = $users->select('users.*')->get();

        $options = '';

        foreach ($users as $item) {
            $options .= '<option  data-content="<div class=\'d-inline-block mr-1\'><img class=\'taskEmployeeImg rounded-circle\' src=' . $item->image_url . ' ></div>  ' . $item->name . '" value="' . $item->id . '"> ' . $item->name . ' </option>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $options]);
    }

    public function appreciation($employeeID)
    {
        $viewAppreciationPermission = user()->permission('view_appreciation');

        if($viewAppreciationPermission == 'none'){
            return [];
        }

        $appreciations = Appreciation::with(['award','award.awardIcon', 'awardTo'])->select('id', 'award_id', 'award_to', 'award_date', 'image', 'summary', 'created_at');
        $appreciations->join('awards', 'awards.id', '=', 'appreciations.award_id');

        if ($viewAppreciationPermission == 'added') {
            $appreciations->where('appreciations.added_by', user()->id);
        }

        if ($viewAppreciationPermission == 'owned') {
            $appreciations->where('appreciations.award_to', user()->id);
        }

        if ($viewAppreciationPermission == 'both') {
            $appreciations->where(function ($q) {
                $q->where('appreciations.added_by', '=', user()->id);

                $q->orWhere('appreciations.award_to', '=', user()->id);
            });
        }

        $appreciations = $appreciations->select('appreciations.*')->where('appreciations.award_to', $employeeID)->get();

        return $appreciations;
    }

    public function projects()
    {

        $viewPermission = user()->permission('view_employee_projects');
        abort_403(!in_array($viewPermission, ['all']));

        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->view = 'employees.ajax.projects';

        $dataTable = new ProjectsDataTable();

        return $dataTable->render('employees.show', $this->data);

    }

    public function tickets()
    {
        $viewPermission = user()->permission('view_tickets');
        abort_403(!in_array($viewPermission, ['all']));
        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->tickets = Ticket::all();
        $this->view = 'employees.ajax.tickets';
        $dataTable = new TicketDataTable();

        return $dataTable->render('employees.show', $this->data);

    }

    public function tasks()
    {
        $viewPermission = user()->permission('view_employee_tasks');
        abort_403(!in_array($viewPermission, ['all']));

        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->taskBoardStatus = TaskboardColumn::all();
        $this->view = 'employees.ajax.tasks';

        $dataTable = new TasksDataTable();

        return $dataTable->render('employees.show', $this->data);
    }

    public function leaves()
    {

        $viewPermission = user()->permission('view_leaves_taken');
        abort_403(!in_array($viewPermission, ['all']));

        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->leaveTypes = LeaveType::all();
        $this->view = 'employees.ajax.leaves';

        $dataTable = new LeaveDataTable();

        return $dataTable->render('employees.show', $this->data);
    }

    public function timelogs()
    {

        $viewPermission = user()->permission('view_employee_timelogs');
        abort_403(!in_array($viewPermission, ['all']));

        $tab = request('tab');
        $this->activeTab = $tab ?: 'profile';
        $this->view = 'employees.ajax.timelogs';

        $dataTable = new TimeLogsDataTable();

        return $dataTable->render('employees.show', $this->data);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function inviteMember()
    {
        abort_403(!in_array(user()->permission('add_employees'), ['all']));

        return view('employees.ajax.invite_member', $this->data);

    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function sendInvite(InviteEmailRequest $request)
    {
        $emails = json_decode($request->email);

        if (!empty($emails)) {
            foreach ($emails as $email) {
                $invite = new UserInvitation();
                $invite->user_id = user()->id;
                $invite->email = $email->value;
                $invite->message = $request->message;
                $invite->invitation_type = 'email';
                $invite->invitation_code = sha1(time() . user()->id);
                $invite->save();
            }
        }

        return Reply::success(__('messages.inviteEmailSuccess'));
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function createLink(CreateInviteLinkRequest $request)
    {
        $invite = new UserInvitation();
        $invite->user_id = user()->id;
        $invite->invitation_type = 'link';
        $invite->invitation_code = sha1(time() . user()->id);
        $invite->email_restriction = (($request->allow_email == 'selected') ? $request->email_domain : null);
        $invite->save();

        return Reply::successWithData(__('messages.inviteLinkSuccess'), ['link' => route('invitation', $invite->invitation_code)]);
    }

    /**
     * @param mixed $request
     * @param mixed $employee
     */
    public function employeeData($request, $employee): void
    {
        $employee->employee_id = $request->employee_id;
        $employee->address = $request->address;
        $employee->hourly_rate = $request->hourly_rate;
        $employee->slack_username = $request->slack_username;
        $employee->department_id = $request->department;
        $employee->designation_id = $request->designation;
        $employee->branch_id = $request->branch;
        $employee->reporting_to = $request->reporting_to;
        $employee->about_me = $request->about_me;
        $employee->joining_date = Carbon::createFromFormat($this->company->date_format, $request->joining_date)->format('Y-m-d');
        $employee->date_of_birth = $request->date_of_birth ? Carbon::createFromFormat($this->company->date_format, $request->date_of_birth)->format('Y-m-d') : null;
        $employee->calendar_view = 'task,events,holiday,tickets,leaves';
        $employee->probation_end_date = $request->probation_end_date ? Carbon::createFromFormat($this->company->date_format, $request->probation_end_date)->format('Y-m-d') : null;
        $employee->notice_period_start_date = $request->notice_period_start_date ? Carbon::createFromFormat($this->company->date_format, $request->notice_period_start_date)->format('Y-m-d') : null;
        $employee->notice_period_end_date = $request->notice_period_end_date ? Carbon::createFromFormat($this->company->date_format, $request->notice_period_end_date)->format('Y-m-d') : null;
        $employee->marital_status = $request->marital_status;
        $employee->marriage_anniversary_date = $request->marriage_anniversary_date ? Carbon::createFromFormat($this->company->date_format, $request->marriage_anniversary_date)->format('Y-m-d') : null;
        $employee->employment_type = $request->employment_type;
        $employee->offer_salary_month = $request->offer_salary_month;
        $employee->internship_end_date = $request->internship_end_date ? Carbon::createFromFormat($this->company->date_format, $request->internship_end_date)->format('Y-m-d') : null;
        $employee->contract_end_date = $request->contract_end_date ? Carbon::createFromFormat($this->company->date_format, $request->contract_end_date)->format('Y-m-d') : null;
    }

    public function importMember()
    {
        $this->pageTitle = __('app.importExcel') . ' ' . __('app.employee');

        $addPermission = user()->permission('add_employees');
        // abort_403(!in_array($addPermission, ['all', 'added']));


        if (request()->ajax()) {
            $html = view('employees.ajax.import', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'employees.ajax.import';

        return view('employees.create', $this->data);
    }

    public function importStore(ImportRequest $request)
    {
        $this->importFileProcess($request, EmployeeImport::class);

        $view = view('employees.ajax.import_progress', $this->data)->render();

        return Reply::successWithData(__('messages.importUploadSuccess'), ['view' => $view]);
    }

    public function importProcess(ImportProcessRequest $request)
    {
        $batch = $this->importJobProcess($request, EmployeeImport::class, ImportEmployeeJob::class);

        return Reply::successWithData(__('messages.importProcessStart'), ['batch' => $batch]);
    }

    public function export(Request $request)
{
    try {
        // Check permissions
        abort_403(!in_array(user()->permission('add_employees'), ['all']));
        
        // Build the query
        $query = DB::table('users')
            ->join('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->join('branches', 'branches.id', '=', 'employee_details.branch_id')
            ->join('teams', 'teams.id', '=', 'employee_details.department_id')
            ->join('designations', 'designations.id', '=', 'employee_details.designation_id')
            ->select(
                'users.name as Name',
                'users.username as Employee_ID',
                'users.status as Status',
                'users.email as Email',
                'users.mobile as Mobile',
                'employee_details.address as Address',
                'users.gender as Gender',
                'employee_details.date_of_birth as DOB',
                'employee_details.joining_date as DOJ',
                'branches.name as Branch',
                'teams.team_name as Department',
                'designations.name as Designation',
                'employee_details.offer_salary_month as Offer_Salary'
            );

        // Apply filters
        if ($request->query('branch') && $request->query('branch') != 'all') {
            $b_id = (int) $request->query('branch');
            $query->where('employee_details.branch_id', $b_id);
        }

        if ($request->query('status')) {
            if ($request->query('status') == 'active') {
                $query->where('users.status', 'active');
            } elseif ($request->query('status') == 'deactive') {
                $query->where('users.status', 'deactive');
            }
        } else {
            $query->where('users.status', 'active');
        }

        if ($request->query('department') && $request->query('department') != 'all') {
            $d_id = (int) $request->query('department');
            $query->where('employee_details.department_id', $d_id);
        }

        if ($request->query('designation') && $request->query('designation') != 'all') {
            $ds_id = (int) $request->query('designation');
            $query->where('employee_details.designation_id', $ds_id);
        }

        if ($request->query('search')) {
            $name = $request->query('search');
            $query->where(function ($query) use ($name) {
                $query->where('users.name', 'LIKE', '%' . $name . '%')
                      ->orWhere('users.username', 'LIKE', '%' . $name . '%');
            });
        }

        if ($request->query('form_filled') && $request->query('form_filled') != 'all') {
            $query->where('users.form_filled', (int) $request->query('form_filled'));
        }

        if (auth()->user()->id == 1906) {
            $query->where('employee_details.branch_id', 5);
        }
        if (in_array(auth()->user()->id, [ 11405,13884])) {
            $query->where('employee_details.branch_id', 7);
        }
        if (auth()->user()->id == 5437) {
                $query->where('employee_details.branch_id', 8);
            }

        $query->orderBy('users.id', 'DESC');

        // Stream the CSV response
        $fileName = 'filtered_employees.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $output = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fwrite($output, "\xEF\xBB\xBF");

            // Write headers
            $headerRow = [
                'Name', 'Employee_ID', 'Status', 'Email', 'Mobile', 'Address',
                'Gender','DOB', 'DOJ', 'Branch', 'Department', 'Designation','Offer_Salary'
            ];
            fputcsv($output, $headerRow);

            // Stream data in chunks
            $query->chunk(1000, function ($employees) use ($output) {
                foreach ($employees as $employee) {
                    $row = array_map(function ($value) {
                        return is_null($value) ? '' : $value;
                    }, (array)$employee);
                    fputcsv($output, $row);
                }
            });

            fclose($output);
        }, 200, $headers);
    } catch (\Exception $e) {
        \Log::error('CSV Export Error: ' . $e->getMessage());
        abort(500, 'Error generating CSV: ' . $e->getMessage());
    }
}
    
    private function convertToCsv($data)
    {
        // Define the column mapping
        $columnMapping = [
            'Employee_ID' => 'Employee ID',
            'Name' => 'Name',
            'Email' => 'Email',
            'Mobile' => 'Mobile',
            'Branch' => 'Branch',
            'Address' => 'Address',
            'Department' => 'Department',
            'Designation' => 'Designation',
            'Gender' => 'Gender',
            'DOJ' => 'Date Of Birth',
            'DOJ' => 'Joining Date',
            'Status' => 'Status',
            'Offer_Salary' => 'Offer_Salary',
        ];
    
        // Generate the CSV header with the mapped column names
        $csvHeader = implode(',', array_values($columnMapping)) . "\r\n";
    
        // Generate the CSV rows with the mapped column values
        $csvRows = '';
        foreach ($data as $row) {
            $rowData = [];
            foreach ($columnMapping as $column => $columnName) {
                $rowData[] = $row->$column;
            }
            $csvRows .= implode(',', $rowData) . "\r\n";
        }
    
        // Combine the header and rows to form the complete CSV
        $csv = $csvHeader . $csvRows;
    
        return $csv;
    }
    
    
    public function exportBankDetails(Request $request)
        {
            abort_403(user()->permission('edit_employees') != 'all');
            
            // $this->pageTitle = 'Export Bank Details';
            // Base query
            $query = DB::table('employee_bank_details')
                ->join('users', 'users.id', '=', 'employee_bank_details.user_id')
                ->join('employee_details', 'employee_details.user_id', '=', 'users.id')
                ->join('teams', 'employee_details.department_id', '=', 'teams.id')
                ->join('designations', 'employee_details.designation_id', '=', 'designations.id')
                ->select(
                    'employee_details.employee_id',
                    'users.name as employee_name',
                    'teams.team_name as department_name',
                    'designations.name as designation_name',
                    'employee_bank_details.bank_name',
                    'employee_bank_details.acc_holder_name',
                    'employee_bank_details.account_number',
                    'employee_bank_details.ifsc_code',
                    'employee_bank_details.branch_name',
                    'employee_bank_details.account_type'
                );
            
            // Apply filters
            if ($request->branch && $request->branch !== 'all') {
                $query->where('employee_details.branch_id', $request->branch);
            }
            
            if ($request->status && $request->status !== 'all') {
                $query->where('users.status', $request->status);
            }
    
        
            if ($request->department && $request->department !== 'all') {
                $query->where('employee_details.department_id', $request->department);
            }
        
            if ($request->designation && $request->designation !== 'all') {
                $query->where('employee_details.designation_id', $request->designation);
            }
        
            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('users.name', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('employee_details.employee_id', 'LIKE', '%' . $request->search . '%');
                });
            }
        
            // Pagination
            $perPage = 20; // Or set your preferred items per page
            if ($request->has('export') && $request->export === 'csv') {
                $this->bankDetails = $query->get();    
            }else{
                $this->bankDetails = $query->paginate($perPage);
            }
            
            // Fetch data
        $bankDetails = $this->bankDetails;
    
        // Check if export is requested
        if ($request->has('export') && $request->export === 'csv') {
            $fileName = 'bank_details_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
            ];
    
            $columns = [
                'Employee ID', 'Name','Department Name', 'Designation',
                'Bank Name', 'Account Holder Name', 'Account Number', 'IFSC Code', 'Branch', 'Account Type',
            ];
    
            $callback = function () use ($bankDetails, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
    
                foreach ($bankDetails as $detail) {
                    fputcsv($file, [
                        $detail->employee_id,
                        $detail->employee_name,
                        $detail->department_name,
                        $detail->designation_name,
                        $detail->bank_name,
                        $detail->acc_holder_name,
                        "\t" . $detail->account_number, 
                        $detail->ifsc_code,
                        $detail->branch_name,
                        $detail->account_type,
                    ]);
                }
    
    
                fclose($file);
            };
    
            return response()->stream($callback, 200, $headers);
        }
        
            // Fetch other data for the view
            $this->departments = Team::all();
            $this->designations = Designation::allDesignations();
            $this->branches = Branch::allBranches();
            
            // Prepare the data for the view
            // $this->data = [
            //     'bankDetails' => $this->slips,
            //     'departments' => $this->departments,
            //     'designations' => $this->designations,
            //     'branches' => $this->branches,
            // ];
        
            // Return the view with the prepared data
            return view('salary.export_bank_details', $this->data);
        }
        
        public function all_photo(){
           $photos = DB::table('employee_docs')
    ->join('users', 'employee_docs.user_id', '=', 'users.id')
    ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
    ->where('employee_details.department_id', 20)
    ->where('employee_docs.name', 'Passport Size Photo')
    ->select('employee_docs.user_id', 'employee_docs.hashname')
    ->get();

        foreach ($photos as $photo) {
            echo 'User ID: ' . $photo->user_id . '<br>';
            echo '<img src="' . asset('user-uploads/employee-docs/' . $photo->user_id . '/' . $photo->hashname) . '" style="width:100px;"><br>';
        
        }


        }
        
        
    public function bulk_update(Request $request)
    {
        // 🔒 Permission check
        abort_403(!in_array(user()->permission('add_employees'), ['all', 'added']));

        // Wrap data in $this->data
        $this->branches = Branch::allBranches();
        $this->departments = Team::all();
        $this->designations = Designation::allDesignations();
        $this->employeeIds = $request->employee_id; // prefill from ❗ click

        return view('employees.bulk-update', $this->data);
    }

    /**
     * Handle bulk update
     */
    public function store_bulk_update(Request $request)
    {
        // 🔒 Permission check
        abort_403(!in_array(user()->permission('add_employees'), ['all', 'added']));

        $request->validate([
            'employee_ids' => 'required|string',
            'branch_id' => 'nullable|integer',
            'department_id' => 'nullable|integer',
            'designation_id' => 'nullable|integer',
            'employment_type' => 'nullable|in:full_time,part_time,on_contract,internship,trainee',
            'office_type' => 'nullable|in:wfo,wfh',
            'gender' => 'nullable|in:male,female,others',
            'status' => 'nullable|in:active,deactive',
        ]);

        // Convert comma-separated IDs to array
        $employeeIds = array_filter(
            array_map('trim', explode(',', $request->employee_ids))
        );

        if (empty($employeeIds)) {
            return back()->with('error', 'No valid employee IDs provided');
        }

        // Build update data for employee_details table
        $employeeDetailsUpdate = [];

        if ($request->filled('branch_id')) {
            $employeeDetailsUpdate['branch_id'] = $request->branch_id;
        }
        if ($request->filled('department_id')) {
            $employeeDetailsUpdate['department_id'] = $request->department_id;
        }
        if ($request->filled('designation_id')) {
            $employeeDetailsUpdate['designation_id'] = $request->designation_id;
        }
        if ($request->filled('employment_type')) {
            $employeeDetailsUpdate['employment_type'] = $request->employment_type;
        }
        if ($request->filled('office_type')) {
            $employeeDetailsUpdate['office_type'] = $request->office_type;
        }

        // Build update data for users table
        $usersUpdate = [];

        if ($request->filled('gender')) {
            $usersUpdate['gender'] = $request->gender;
        }
        if ($request->filled('status')) {
            $usersUpdate['status'] = $request->status;
        }

        if (empty($employeeDetailsUpdate) && empty($usersUpdate)) {
            return back()->with('error', 'Nothing to update. Please select at least one field to update.');
        }

        // Get user_ids from employee_details based on employee_ids
        $userIds = DB::table('employee_details')
            ->whereIn('employee_id', $employeeIds)
            ->pluck('user_id')
            ->toArray();

        if (empty($userIds)) {
            return back()->with('error', 'No employees found with the provided employee IDs');
        }

        // Update employee_details table
        if (!empty($employeeDetailsUpdate)) {
        DB::table('employee_details')
            ->whereIn('employee_id', $employeeIds)
                ->update($employeeDetailsUpdate);
        }

        // Update users table
        if (!empty($usersUpdate)) {
            DB::table('users')
                ->whereIn('id', $userIds)
                ->update($usersUpdate);
        }

        $updatedCount = count($userIds);
        return redirect()
            ->route('employees.index')
            ->with('success', "Successfully updated {$updatedCount} employee(s)");
    }
    
    
    
}


