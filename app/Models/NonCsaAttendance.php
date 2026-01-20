<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NonCsaAttendance extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attendances_non_csa';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'process',
        'sub_process',
        'department',
        'emp_id',
        'email_id',
        'name',
        'supervisor_name',
        'designation',
        'month_year',
        'week_number',
        'date',
        'attendance_status',
        'in_time',
        'out_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'in_time' => 'string',
        'out_time' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user associated with the attendance record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the employee detail associated with the attendance record.
     */
    public function employeeDetail()
    {
        return $this->belongsTo(EmployeeDetail::class, 'emp_id', 'employee_id');
    }
}