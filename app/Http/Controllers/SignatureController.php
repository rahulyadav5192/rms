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
use Intervention\Image\Facades\Image;


class SignatureController extends Controller
{
    
    
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.leaves';
        
    }
    
    
    public function upload(Request $request)
    {
        $dataUrl = $request->input('signature');
        $user = user()->id;
        if (!$dataUrl) {
            return back()->with('error', 'No signature provided.');
        }

        // Decode base64 and create image
        $signature = Image::make($dataUrl);
        $fileName = 'signature_' . rand(10000000,99999999) . '_user_'. $user.'.png';
        $filePath = public_path('signatures/' . $fileName);

        // Ensure the folder exists
        if (!file_exists(public_path('signatures'))) {
            mkdir(public_path('signatures'), 0755, true);
        }

        $signature->save($filePath);
        
        $update = DB::table('employee_details')->where('user_id',$user)->update([
                    'sign_upload' => 0,
                    'sign_file' => $fileName
                    ]);

        return redirect()->back()->with('success','Signature uploaded successfully!');
        
        
    }

    
    
    

    

}