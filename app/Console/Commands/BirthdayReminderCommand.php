<?php

namespace App\Console\Commands;

use App\Events\BirthdayReminderEvent;
use App\Models\Company;
use App\Models\EmployeeDetails;
use Illuminate\Console\Command;

class BirthdayReminderCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send birthday notification to everyone';

    /**
     * Handle the command.
     *
     * This method retrieves a list of companies, and for each company it retrieves
     * all employees with an upcoming birthday and triggers the "BirthdayReminderEvent".
     */
    public function handle()
    {
        // Get the current day in the format of "m-d"
        $currentDay = now()->format('m-d');

        // Retrieve all companies
        $companies = Company::select('id')->get();

        // Loop through each company
        foreach ($companies as $company) {
            // Retrieve all active employees with an upcoming birthday for the current company
            $upcomingBirthday = EmployeeDetails::whereRaw('DATE_FORMAT(`date_of_birth`, "%m-%d") = "' . $currentDay . '"')
                ->whereHas('user', function ($query) use ($company) {
                    $query->where('status', 'active')
                        ->where('company_id', $company->id);
                })
                ->orderBy('date_of_birth')
                ->select('company_id', 'date_of_birth', 'user.name', 'user.image', 'user.id')
                ->get()->toArray();

            // If there is any employee with an upcoming birthday, trigger the "BirthdayReminderEvent"
            if ($upcomingBirthday != null) {
                event(new BirthdayReminderEvent($company, $upcomingBirthday));
            }
        }
    }

}
