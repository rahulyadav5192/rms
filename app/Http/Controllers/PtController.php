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

class PtController extends AccountBaseController
{
    
    
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Progress Tracker';
        
    }
    
    public function index()
    {
        // $skills = SkillDevelopment::with('department', 'user', 'skill')->get();
        $this->tasks = DB::table('pt_task')
                        ->join('users','pt_task.user_id','users.id')
                        ->join('employee_details','users.id','employee_details.user_id')
                        ->join('skills','skills.id','pt_task.skill_id')
                        ->join('pt_percentage','pt_percentage.id','pt_task.perc_id')
                        ->where('pt_task.trash_status',0)
                        ->orderBY('pt_task.user_id')
                        ->latest('pt_task.id')
                        ->select('pt_task.*','users.name','pt_task.id as ptid','skills.name as skill_name','pt_percentage.*')
                        ->get();
                        // return $this->tasks;
        return view('pt.index', $this->data);
    }
    public function index_emp()
    {
        // $skills = SkillDevelopment::with('department', 'user', 'skill')->get();
        $this->tasks = DB::table('pt_task')
                        ->join('users','pt_task.user_id','users.id')
                        ->join('employee_details','users.id','employee_details.user_id')
                        ->join('skills','skills.id','pt_task.skill_id')
                        ->join('pt_percentage','pt_percentage.id','pt_task.perc_id')
                        ->where('pt_task.trash_status',0)
                        ->where('pt_task.user_id',auth()->user()->id)
                        ->orderBY('pt_task.user_id')
                        ->latest('pt_task.id')
                        ->select('pt_task.*','users.name','pt_task.id as ptid','skills.name as skill_name','pt_percentage.*')
                        ->get();
        return view('pt.index_emp', $this->data);
    }
    
    public function pt_task_edit_emp($id){
        $this->tracker = DB::table('pt_task')
                        ->join('users','pt_task.user_id','users.id')
                        ->join('employee_details','users.id','employee_details.user_id')
                        ->join('skills','skills.id','pt_task.skill_id')
                        ->join('pt_percentage','pt_percentage.id','pt_task.perc_id')
                        ->where('pt_task.user_id',auth()->user()->id)
                        ->where('pt_task.id',$id)
                        ->orderBY('pt_task.user_id')
                        ->latest('pt_task.id')
                        ->select('pt_task.*','users.name','pt_task.id as ptid','skills.name as skill_name','pt_percentage.*')
                        ->first();
        $this->files = DB::table('pt_task_files')->where('task_id',$id)->where('trash_status',0)->latest('id')->get();
        return view('pt.edit_emp', $this->data);
        
    }
    
    public function pt_task_delete($id){
        $this->tracker = DB::table('pt_task')
                        
                        ->where('pt_task.id',$id)
                        
                        ->update([
                            'trash_status'=>1
                            ]);
        return redirect()->back()->with('success', 'Tracker successfully deleted!');
        
    }
    
    public function pt_task_edit_emp_store(Request $request, $id){
        // Validate the request data
        $request->validate([
            'notes' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'status' => 'required|in:0,1,2',
        ]);
    
        // try {
            // Retrieve the tracker record
            $tracker = DB::table('pt_task')->where('id', $id)->first();
    
            // Check if file was uploaded
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = 'uploads/' . $fileName;
                $file->move(public_path('uploads'), $fileName);
            } else {
                $fileName = $tracker->file_name ?? null;
                $filePath = $tracker->file_path ?? null;
            }
    
            // Update the tracker in the database
            DB::table('pt_task')->where('id', $id)->update([
                'emp_status' => $request->input('status'),
                'updated_at' => now(),
            ]);
            
            DB::table('pt_task_files')->insert([
                'task_id' => $tracker->id,
                'notes' => $request->input('notes'),
                'file_name' => $fileName,
                'file_path' => $filePath,
                'updated_at' => now(),
            ]);
    
            // Redirect with success message
            return redirect()->back()->with('success', 'Tracker successfully updated!');
    
        // } catch (\Exception $e) {
        //     // Log the error
        //     \Log::error('Error updating tracker: ' . $e->getMessage());
    
        //     // Redirect with error message
        //     return redirect()->back()->with('error', 'There was an error updating the tracker. Please try again.');
        // }

    }
    
    
    
    public function pt_task_edit_hr($id){
        $this->tracker = DB::table('pt_task')
                        ->join('users','pt_task.user_id','users.id')
                        ->join('employee_details','users.id','employee_details.user_id')
                        ->join('skills','skills.id','pt_task.skill_id')
                        ->join('pt_percentage','pt_percentage.id','pt_task.perc_id')
                        ->where('pt_task.trash_status',0)
                        ->where('pt_task.id',$id)
                        ->orderBY('pt_task.user_id')
                        ->latest('pt_task.id')
                        ->select('pt_task.*','users.name','skills.name as skill_name','pt_task.id as ptid','pt_percentage.*')
                        ->first();
        $this->files = DB::table('pt_task_files')->where('task_id',$id)->where('trash_status',0)->latest('id')->get();
        return view('pt.edit_hr', $this->data);
        
    }
    
    public function pt_task_edit_hr_store(Request $request, $id){
        // return $id;
        // Validate the request data
        $request->validate([
            // 'notes' => 'required|string',
            // 'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            // 'status' => 'required|in:0,1,2',
        ]);
    
        // try {
            // Retrieve the tracker record
            // $tracker = DB::table('pt_task')->where('id', $id)->first();
    
            // Check if file was uploaded
            // if ($request->hasFile('file')) {
            //     $file = $request->file('file');
            //     $fileName = time() . '_' . $file->getClientOriginalName();
            //     $filePath = 'uploads/' . $fileName;
            //     $file->move(public_path('uploads'), $fileName);
            // } else {
            //     $fileName = $tracker->file_name ?? null;
            //     $filePath = $tracker->file_path ?? null;
            // }
    
            // Update the tracker in the database
            DB::table('pt_task')->where('id', $id)->update([
                'hr_status' => $request->input('status'),
                'hr_notes' => $request->input('notes'),
                'updated_at' => now(),
            ]);
            
            // DB::table('pt_task_files')->insert([
            //     'task_id' => $tracker->id,
                
            //     'file_name' => $fileName,
            //     'file_path' => $filePath,
            //     'updated_at' => now(),
            // ]);
    
            // Redirect with success message
            return redirect()->back()->with('success', 'Tracker successfully updated!');
    
        // } catch (\Exception $e) {
        //     // Log the error
        //     \Log::error('Error updating tracker: ' . $e->getMessage());
    
        //     // Redirect with error message
        //     return redirect()->back()->with('error', 'There was an error updating the tracker. Please try again.');
        // }

    }
    
    
    public function create()
    {
        $this->employees = User::allEmployees(null, true, 'all');
        $this->departments = Team::all();
        $this->skillsList = Skill::all(); 
        $this->developmentTypes = DB::table('pt_percentage')->where('trash_status',0)->get();
        return view('pt.create',$this->data);
    }
    
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'perc_id' => 'required|array', // Validate as an array of development types
            'perc_id.*' => 'integer',      // Each perc_id should be an integer
            'user_id' => 'required|array', // Validate as an array of user IDs
            'user_id.*' => 'integer',      // Each user_id should be an integer
            'skill_id' => 'required|array', // Validate as an array of skills
            'skill_id.*' => 'integer',     // Each skill_id should be an integer
            'timeline' => 'required|array', // Validate as an array of timelines
            'timeline.*' => 'string',      // Each timeline should be a string
            'task' => 'required|array',    // Validate as an array of tasks
            'task.*' => 'string',          // Each task should be a string
            'notes' => 'nullable|array',   // Validate as an array of notes
            'notes.*' => 'nullable|string',// Each note should be a string
        ]);
    
        try {
            // Loop through each user ID
            foreach ($request->input('user_id') as $userId) {
                // Loop through each skill and task for the current user
                for ($i = 0; $i < count($request->input('skill_id')); $i++) {
                    DB::table('pt_task')->insert([
                        'perc_id' => $request->input('perc_id')[$i], // Development type
                        'user_id' => $userId,                         // User ID
                        'skill_id' => $request->input('skill_id')[$i], // Skill ID
                        'timeline' => $request->input('timeline')[$i], // Timeline
                        'task' => $request->input('task')[$i],         // Task description
                        'notes' => $request->input('notes')[$i] ?? null, // Notes (nullable)
                        'hr_status' => $request->input('hr_status')[$i] ?? 'Pending', // Default status
                        'added_by' => auth()->user()->id,              // Added by current authenticated user
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
    
            // Redirect with success message
            return redirect()->back()->with('success', 'Skill tasks successfully created for all selected users!');
    
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error saving skill tasks: ' . $e->getMessage());
    
            // Redirect with error message
            return redirect()->back()->with('error', 'There was an error saving the skill tasks. Please try again.');
        }
    }






    
    
    
    
    
}