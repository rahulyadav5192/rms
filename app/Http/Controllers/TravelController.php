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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Helpers\GreenApiHelper;


class TravelController extends AccountBaseController
{
    
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Travel Allowance';
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'destination' => 'required|string|max:255',
            'work_summary' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'transport_mode' => 'required|in:flight,train,bus,car',
            'notes' => 'nullable|string',
        ]);

        $id = DB::table('travel_allowances')->insertGetId([
            'user_id' => user()->id,
            'trip_type' => $validated['trip_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'destination' => $validated['destination'],
            'work_summary' => $validated['work_summary'],
            'expenses' => $validated['amount'],
            'transport_mode' => $validated['transport_mode'],
            'notes' => $validated['notes'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $adminPhone = '971502404786'; // Include country code, no + sign
        $userName = user()->name;
    
        $message = "🧾 *New Travel Allowance Request*\n"
                 . "👤 User: $userName\n"
                 . "📍 Destination: {$validated['destination']}\n"
                 . "📅 Date: {$validated['start_date']}\n"
                 . "💼 Purpose: {$validated['work_summary']}\n"
                 . "💰 Amount: ₹{$validated['amount']}\n"
                 . "🚗 Mode: {$validated['transport_mode']}";
    
        $this->sendWhatsAppMessage($adminPhone, $message);

        return response()->json([
            'success' => true,
            'message' => 'Travel allowance added successfully',
            'data' => array_merge(['id' => $id], $validated)
        ]);
    }

    public function edit($id)
    {
        $travel = DB::table('travel_allowances')
            ->where('id', $id)
            ->where('user_id', user()->id)
            ->first();

        if (!$travel) {
            return response()->json(['success' => false, 'message' => 'Travel allowance not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $travel]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'trip_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'destination' => 'required|string|max:255',
            'work_summary' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'transport_mode' => 'nullable|in:flight,train,bus,car',
            'notes' => 'nullable|string',
        ], [
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        // Get validated data
        $validated = $validator->validated();
        $validated['expenses'] = $validated['amount'];

        $updated = DB::table('travel_allowances')
            ->where('id', $id)
            ->where('user_id', user()->id)
            ->update([
                'trip_type' => $validated['trip_type'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'destination' => $validated['destination'],
                'work_summary' => $validated['work_summary'],
                'expenses' => $validated['amount'],
                'transport_mode' => $validated['transport_mode'],
                'notes' => $validated['notes'],
                'updated_at' => now(),
            ]);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Travel allowance updated successfully',
                'data' => array_merge(['id' => $id], $validated)
            ]);
        }
 
        return response()->json(['success' => false, 'message' => 'Travel allowance not found'], 404);
    }

    public function destroy($id)
    {
        $deleted = DB::table('travel_allowances')
            ->where('id', $id)
            ->where('user_id', user()->id)
            ->update([
                'trash_status' =>1,
                'trashed_by' =>user()->id
                ]);

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Travel allowance deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Travel allowance not found'], 404);
    }
    
    // accounts 
    
    public function travelRequestsList()
    {
        $this->travelRequests = DB::table('travel_allowances')
            ->join('users', 'travel_allowances.user_id', '=', 'users.id')
            ->join('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->join('teams', 'teams.id', '=', 'employee_details.department_id')
            ->where('travel_allowances.trash_status',0)
            ->where('travel_allowances.manager_status',0)
            ->select('travel_allowances.*', 'users.name as user_name','teams.team_name')
            ->orderBy('travel_allowances.id', 'desc')
            ->get();
    
        return view('travel.travel_requests', $this->data);
    }
    /**
     * Display detailed view for a specific travel request (for accounts).
     */
    public function accountsTravelDetails($travel_id)
    {
        // Fetch travel request details (manager-approved only)
        $this->travel = DB::table('travel_allowances')
            ->join('users', 'travel_allowances.user_id', '=', 'users.id')
            ->where('travel_allowances.id', $travel_id)
            ->where('travel_allowances.manager_status', 0) // Ensure manager-approved
            ->select(
                'travel_allowances.id',
                'users.name as user_name',
                'travel_allowances.start_date',
                'travel_allowances.end_date',
                'travel_allowances.destination',
                'travel_allowances.expenses',
                'travel_allowances.transport_mode',
                'travel_allowances.approve_status',
                'travel_allowances.account_remark',
                'travel_allowances.invoice_acc_appr',
                'travel_allowances.i_ac_remark'
            )
            ->first();

        if (!$this->travel) {
            return redirect()->back()->with('error', 'Travel request not found or not approved by manager.');
        }

        // Fetch invoices only if manager has approved them
        $this->invoices = DB::table('travel_invoices')
            ->where('travel_id', $travel_id)
            ->where('travel_allowances.invoice_approve_manager', 0) // Only if manager approved invoices
            ->join('travel_allowances', 'travel_invoices.travel_id', '=', 'travel_allowances.id')
            ->select('travel_invoices.*')
            ->get();

        return view('travel.accounts_details', $this->data);
    }

    /**
     * Update accounts approval status for travel request.
     */
    public function changeTravelRequestStatus(Request $request, $id)
    {
        $request->validate([
            'approve_status' => 'required|in:0,1', // Renamed 'status' to 'approve_status' for clarity
            'accounts_remark' => 'nullable|string|max:255', // Added remark field
        ]);

        $updated = DB::table('travel_allowances')
            ->where('id', $id)
            ->update([
                'approve_status' => $request->approve_status,
                'account_remark' => $request->accounts_remark,
                'appoved_by' => user()->id,
                'updated_at' => now(),
            ]);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to update status'], 500);
    }

    /**
     * Update accounts approval status for invoices.
     */
    public function updateInvoiceAccountsStatus(Request $request, $id)
    {
        $request->validate([
            'invoice_acc_appr' => 'required|in:0,1,2,3',
            'i_ac_remark' => 'nullable|string|max:255',
        ]);

        $updated = DB::table('travel_allowances')
            ->where('id', $id)
            ->where('manager_status', 0) // Ensure manager-approved
            ->where('invoice_approve_manager', 0) // Ensure manager-approved invoices
            ->update([
                'invoice_acc_appr' => $request->invoice_acc_appr,
                'i_ac_remark' => $request->i_ac_remark,
                'updated_at' => now(),
            ]);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Invoice approval status updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to update invoice status'], 500);
    }
    
    
    
   
    /**
     * Show travel requests based on the manager's assigned team.
     */
    public function managerApprovalPage($team_id)
    {
        // Get manager ID
        $manager_id = auth()->user()->id;

        // Check if manager handles this department/team
        $managedTeam = DB::table('employee_details')
            ->where('user_id', $manager_id)
            ->where('department_id', $team_id)
            ->exists();

        if (!$managedTeam) {
            abort(403, 'Unauthorized access');
        }

        // Fetch travel requests for employees under this team
        $this->travelRequests = DB::table('travel_allowances')
            ->join('users', 'travel_allowances.user_id', '=', 'users.id')
            ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
            ->where('employee_details.department_id', $team_id)
            ->select(
                'travel_allowances.*',
                'users.name as employee_name',
                'employee_details.department_id'
            )
            ->orderBy('travel_allowances.id', 'desc')
            ->get();

        // Fetch invoices for all travel requests in this team
        $this->invoices = DB::table('travel_invoices')
            ->join('travel_allowances', 'travel_invoices.travel_id', '=', 'travel_allowances.id')
            ->join('employee_details', 'travel_allowances.user_id', '=', 'employee_details.user_id')
            ->where('employee_details.department_id', $team_id)
            ->select(
                'travel_invoices.id as invoice_id',
                'travel_invoices.travel_id',
                'travel_invoices.description',
                'travel_invoices.file_path',
                'travel_invoices.created_at'
            )
            ->get();

        return view('travel.travel_manager',$this->data);
    }
    
    public function managerTravelDetails($team_id, $travel_id)
    {
        // Get manager ID
        $manager_id = auth()->user()->id;

        // Check if manager handles this department/team
        $managedTeam = DB::table('employee_details')
            ->where('user_id', $manager_id)
            ->where('department_id', $team_id)
            ->exists();

        if (!$managedTeam) {
            abort(403, 'Unauthorized access');
        }

        // Fetch travel request details
        $this->travel = DB::table('travel_allowances')
            ->join('users', 'travel_allowances.user_id', '=', 'users.id')
            ->join('employee_details', 'users.id', '=', 'employee_details.user_id')
            ->where('travel_allowances.id', $travel_id)
            ->where('employee_details.department_id', $team_id)
            ->select(
                'travel_allowances.id',
                'users.name as employee_name',
                'travel_allowances.start_date',
                'travel_allowances.end_date',
                'travel_allowances.destination',
                'travel_allowances.expenses',
                'travel_allowances.transport_mode',
                'travel_allowances.manager_status',
                'travel_allowances.manager_remark',
                'travel_allowances.invoice_approve_manager',
                'travel_allowances.i_m_remark'
            )
            ->first();

        if (!$this->travel) {
            return redirect()->back()->with('error', 'Travel request not found.');
        }

        // Fetch invoices for this travel request
        $this->invoices = DB::table('travel_invoices')
            ->where('travel_id', $travel_id)
            // ->select('id', 'travel_id', 'description', 'file_path', 'created_at')
            ->get();

        return view('travel.manager_details',$this->data);
    }

    /**
     * Update manager approval status and remark.
     */
    public function updateManagerStatus(Request $request, $id)
    {
        $request->validate([
            'manager_status' => 'required|in:0,1,2,3',
            'manager_remark' => 'nullable|string|max:255',
        ]);

        // Update travel request using Query Builder
        DB::table('travel_allowances')
            ->where('id', $id)
            ->update([
                'manager_status' => $request->manager_status,
                'manager_remark' => $request->manager_remark,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Manager status updated successfully!'
        ]);
    }
    
    
    public function updateInvoiceManagerStatus(Request $request, $id)
    {
        $request->validate([
            'invoice_approve_manager' => 'required|in:0,1,2,3',
            'i_m_remark' => 'nullable|string|max:255',
        ]);

        DB::table('travel_allowances')
            ->where('id', $id)
            ->update([
                'invoice_approve_manager' => $request->invoice_approve_manager,
                'i_m_remark' => $request->i_m_remark,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice approval status updated successfully!'
        ]);
    }
    
    
    public function viewDetails($id)
    {
        $this->travel = DB::table('travel_allowances')->where('id', $id)->first();
        $this->invoices = DB::table('travel_invoices')->where('travel_id', $id)->get();
    
        if (!$this->travel) {
            return redirect()->back()->with('error', 'Travel request not found.');
        }
    
        return view('travel.details',$this->data);
    }
    
    public function uploadInvoices(Request $request, $id)
    {
        $request->validate([
            'expense_type.*' => 'required|in:local_travel,hotel,food,client_engagement,miscellaneous',
            'narration.*' => 'required|string|max:255',
            'amount.*' => 'required|numeric|min:0',
            'invoice_files.*' => 'required|mimes:pdf,jpg,png,jpeg|max:10240',
        ]);
    
        $travel = DB::table('travel_allowances')->where('id', $id)->first();
        if (!$travel) {
            return redirect()->back()->with('error', 'Travel request not found.');
        }
    
        $expenseTypes = $request->input('expense_type', []);
        $narrations = $request->input('narration', []);
        $amounts = $request->input('amount', []);
        $files = $request->file('invoice_files', []);
    
        foreach ($files as $index => $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/invoices'), $filename);
    
            DB::table('travel_invoices')->insert([
                'travel_id' => $id,
                'file_path' => $filename,
                'expense_type' => $expenseTypes[$index] ?? 'miscellaneous', // Default if missing
                'description' => $narrations[$index] ?? 'N/A', // Default if missing
                'amount' => $amounts[$index] ?? 0, // Default if missing
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    
        return redirect()->back()->with('success', 'Invoices uploaded successfully.');
    }
    
    public function deleteInvoice($travelId, $invoiceId)
    {
        $invoice = DB::table('travel_invoices')
            ->where('id', $invoiceId)
            ->where('travel_id', $travelId)
            ->first();

        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }

        // Delete the file from storage
        $filePath = public_path('uploads/invoices/' . $invoice->file_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // Delete the invoice record from the database
        DB::table('travel_invoices')
            ->where('id', $invoiceId)
            ->where('travel_id', $travelId)
            ->delete();

        return redirect()->back()->with('success', 'Invoice deleted successfully.');
    }
    
    
    
    
    
    
    // api
    
    // ✅ WhatsApp send function (reusable inside this controller)
    private function sendWhatsAppMessage($phoneNumber, $message)
    {
        $instanceId = env('GREEN_API_INSTANCE_ID');
        $token = env('GREEN_API_TOKEN');
    
        $url = "https://api.green-api.com/waInstance{$instanceId}/sendMessage/{$token}";
    
        $response = Http::post($url, [
            'chatId' => $phoneNumber . '@c.us',
            'message' => $message,
        ]);
    
        return $response->successful();
    }


    
    
    
    
}
     