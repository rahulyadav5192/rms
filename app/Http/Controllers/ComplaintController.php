<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use DB;

class ComplaintController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Complaints';
        $this->middleware(function ($request, $next) {
            // abort_403(!in_array('attendance', $this->user->modules));
            // $this->viewAttendancePermission = user()->permission('view_attendance');

            return $next($request);
        });
    }

    // Save complaint
    public function store(Request $request)
    {
        $request->validate([
            'subject'   => 'required|string|max:255',
            'category'  => 'required|string|max:100',
            'complaint' => 'required|string|min:10',
        ]);

        Complaint::create([
            'user_id'   => Auth::id(),
            'subject'   => $request->subject,
            'category'  => $request->category,
            'complaint' => $request->complaint,
            'status'    => 0, // Pending
        ]);

        return redirect()->back()->with('message', 'Your complaint has been submitted successfully.');
    }

    // Show list of complaints
    public function index()
    {
        $this->complaints = Complaint::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('complaints.index', $this->data);
    }
    
     // HR view of all complaints
    public function manage()
    {
        // check permission
        if (user()->permission('view_employees') !== 'all') {
            abort(403, 'Unauthorized access');
        }

        $query = DB::table('complaints')
            ->join('users', 'complaints.user_id', '=', 'users.id')
            ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
            ->leftJoin('branches', 'employee_details.branch_id', '=', 'branches.id')
            ->leftJoin('teams', 'employee_details.department_id', '=', 'teams.id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id')
            ->select(
                'complaints.*',
                'users.name as employee_name',
                'users.email as employee_email',
                'employee_details.employee_id',
                'branches.name as branch_name',
                'teams.team_name as department',
                'designations.name as designation'
            )
            ->orderBy('complaints.created_at', 'desc');
        
        // Apply branch restriction only for user id = 13884
        if (auth()->user()->id == 13884) {
            $query->where('employee_details.branch_id', 7);
        }
        
        $this->complaints = $query->get();


        return view('complaints.manage', $this->data);
    }

    // Update complaint status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:0,1,2', // Pending, Resolved, Rejected
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->status = $request->status;
        $complaint->save();

        return redirect()->back()->with('message', 'Complaint status updated successfully.');
    }
}
