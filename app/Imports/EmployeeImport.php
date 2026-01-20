<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class EmployeeImport implements ToArray
{

    public static function fields(): array
    {
        return array(
            array('id' => 'name', 'name' => __('modules.employees.employeeName'), 'required' => 'Yes',),
            array('id' => 'email', 'name' => __('modules.employees.employeeEmail'), 'required' => 'No',),
            array('id' => 'mobile', 'name' => __('app.mobile'), 'required' => 'No',),
            array('id' => 'gender', 'name' => __('modules.employees.gender'), 'required' => 'No',),
            array('id' => 'employee_id', 'name' => __('modules.employees.employeeId'), 'required' => 'Yes'),
            array('id' => 'joining_date', 'name' => __('modules.employees.joiningDate'), 'required' => 'No'),
            array('id' => 'address', 'name' => __('app.address'), 'required' => 'No'),
            // array('id' => 'hourly_rate', 'name' => __('modules.employees.hourlyRate'), 'required' => 'No'),
            array('id' => 'branch_id', 'name' => 'Branch', 'required' => 'No'),
            array('id' => 'department_id', 'name' => 'Department', 'required' => 'No'),
            array('id' => 'designation_id', 'name' => 'Designation', 'required' => 'No'),
            array('id' => 'aadhar', 'name' => 'Aadhar', 'required' => 'No'),
            array('id' => 'pan', 'name' => 'Pan', 'required' => 'No'),
            array('id' => 'dob', 'name' => 'dob', 'required' => 'No'),
        );
    }

    public function array(array $array): array
    {
        return $array;
    }

}
