<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentAttendance extends Model
{
    protected $table = 'agent_attendance';

    protected $fillable = [
        'week', 'ucid', 'sr_no', 'date', 'center', 'user_id', 'name_of_employee',
        'email_id', 'tl_name', 'sn_team_lead', 'am_name', 'lob', 'status',
        'batch_no', 'designation', 'emp_type', 'shift', 'attendance',
        'target_login_hrs', 'cc_login_hrs', 'sf_login_hrs', 'total_login',
        'present_day'
    ];

    public function employee()
    {
        return $this->belongsTo(EmployeeDetails::class, 'user_id', 'user_id'); 
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}