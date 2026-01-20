<?php

namespace App\Http\Requests\Admin\Employee;

use App\Models\EmployeeDetails;
use App\Http\Requests\CoreRequest;
use App\Traits\CustomFieldsRequestTrait;
use App\Models\User;
class UpdateRequest extends CoreRequest
{
    use CustomFieldsRequestTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */


    public function rules()
    {
        // Resolve route param (may be ID or model)
        $employeeRoute = $this->route('employee');
        $userId = is_object($employeeRoute) ? $employeeRoute->user_id : $employeeRoute;
    
        $detailID = EmployeeDetails::where('user_id', $userId)->first();
        $user = User::find($userId);
    
        $setting = company();
    
        $rules = [
            'employee_id' => 'required|max:50|unique:employee_details,employee_id,' . optional($detailID)->id . ',id,company_id,' . company()->id,
    
            // base email rules ONLY
            'email' => ['nullable', 'max:100'],
    
            'name'  => 'required|max:50',
            'hourly_rate' => 'nullable|numeric',
            'department' => 'required',
            'designation' => 'required',
            'joining_date' => 'required',
            'last_date' => 'nullable|date_format:"' . $setting->date_format . '"|after_or_equal:joining_date',
            'country' => 'nullable',
            'date_of_birth' => 'nullable|date_format:"' . $setting->date_format . '"|before_or_equal:' . now($setting->timezone)->toDateString(),
            'probation_end_date' => 'nullable|date_format:"' . $setting->date_format . '"|after_or_equal:joining_date',
            'notice_period_start_date' => 'nullable|required_with:notice_period_end_date|date_format:"' . $setting->date_format . '"',
            'notice_period_end_date' => 'nullable|required_with:notice_period_start_date|date_format:"' . $setting->date_format . '"|after_or_equal:notice_period_start_date',
            'internship_end_date' => 'nullable|date_format:"' . $setting->date_format . '"|after_or_equal:joining_date',
            'contract_end_date' => 'nullable|date_format:"' . $setting->date_format . '"|after_or_equal:joining_date',
        ];
    
        /**
         * ✅ EMAIL UNIQUE CHECK ONLY IF EMAIL CHANGED
         */
        if (
            $this->filled('email') &&
            $user &&
            $this->email !== $user->email
        ) {
            $rules['email'][] = Rule::unique('users', 'email');
        }
    
        /**
         * Slack username rule (unchanged, just null-safe)
         */
        if ($detailID) {
            $rules['slack_username'] =
                'nullable|unique:employee_details,slack_username,' . $detailID->id . ',id,company_id,' . company()->id;
        } else {
            $rules['slack_username'] =
                'nullable|unique:employee_details,slack_username,null,id,company_id,' . company()->id;
        }
    
        /**
         * Password rule
         */
        if (request()->password != '') {
            $rules['password'] = 'required|min:8|max:50';
        }
    
        /**
         * Telegram rule (fixed & safe)
         */
        if (request()->telegram_user_id) {
            $rules['telegram_user_id'] = [
                'nullable',
                Rule::unique('users', 'telegram_user_id')->ignore($userId),
            ];
        }
    
        $rules = $this->customFieldRules($rules);
    
        return $rules;
    }


    public function attributes()
    {
        $attributes = [];

        $attributes = $this->customFieldsAttributes($attributes);

        return $attributes;
    }
    
    // previous rule
    // public function rules()
    // {
    //     $detailID = EmployeeDetails::where('user_id', $this->route('employee'))->first();
    //     $setting = company();
    //     $rules = [
    //         'employee_id' => 'required|max:50|unique:employee_details,employee_id,'.$detailID->id.',id,company_id,' . company()->id,
    //         'email' => 'nullable|max:100|unique:users,email,'.$this->route('employee').',id,company_id,' . company()->id,
    //         'name'  => 'required|max:50',
    //         'hourly_rate' => 'nullable|numeric',
    //         'department' => 'required',
    //         'designation' => 'required',
    //         'joining_date' => 'required',
    //         'last_date' => 'nullable|date_format:"' . $setting->date_format . '"|after_or_equal:joining_date',
    //         'country' => 'nullable',
    //         'date_of_birth' => 'nullable|date_format:"' . $setting->date_format . '"|before_or_equal:'.now($setting->timezone)->toDateString(),
    //         'probation_end_date' => 'nullable|date_format:"' . $setting->date_format . '"|after_or_equal:joining_date',
    //         'notice_period_start_date' => 'nullable|required_with:notice_period_end_date|date_format:"' . $setting->date_format . '"',
    //         'notice_period_end_date' => 'nullable|required_with:notice_period_start_date|date_format:"' . $setting->date_format . '"|after_or_equal:notice_period_start_date',
    //         'internship_end_date' => 'nullable|date_format:"' . $setting->date_format . '"|after_or_equal:joining_date',
    //         'contract_end_date' => 'nullable|date_format:"' . $setting->date_format . '"|after_or_equal:joining_date',
    //     ];

    //     if ($detailID) {
    //         $rules['slack_username'] = 'nullable|unique:employee_details,slack_username,'.$detailID->id.',id,company_id,' . company()->id;
    //     }
    //     else {
    //         $rules['slack_username'] = 'nullable|unique:employee_details,slack_username,null,id,company_id,' . company()->id;
    //     }

    //     if (request()->password != '') {
    //         $rules['password'] = 'required|min:8|max:50';
    //     }

    //     if (request()->telegram_user_id) {
    //         $rules['telegram_user_id'] = 'nullable|unique:users,telegram_user_id,' . $detailID->user_id.',id,company_id,' . company()->id;
    //     }

    //     $rules = $this->customFieldRules($rules);

    //     return $rules;
    // }

}
