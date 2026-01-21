<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessFlexiDbMonthJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600; // up to 1 hour for full month

    public function __construct(
        public int $year,
        public int $month,
        public string $requestId
    ) {
    }

    public function handle(): void
    {
        $currentYear = $this->year;
        $currentMonth = str_pad((string) $this->month, 2, '0', STR_PAD_LEFT);
        $daysInMonth = Carbon::createFromDate($currentYear, $currentMonth, 1)->daysInMonth;

        Log::info('ProcessFlexiDbMonthJob started', [
            'requestId' => $this->requestId,
            'year' => $currentYear,
            'month' => $currentMonth,
            'daysInMonth' => $daysInMonth,
        ]);

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $todayDate = Carbon::createFromDate($currentYear, $currentMonth, $day)->format('Y-m-d');

            // Step 1: Only get user_ids missing shift for the day
            $employeeIds = DB::table('employee_details')
                ->leftJoin('employee_shift_schedules', function ($join) use ($todayDate) {
                    $join->on('employee_shift_schedules.user_id', '=', 'employee_details.user_id')
                        ->whereDate('employee_shift_schedules.date', $todayDate);
                })
                ->whereNotNull('employee_details.bio_machine_id')
                ->whereNotNull('employee_details.bio_uid')
                ->whereNull('employee_shift_schedules.user_id')
                ->pluck('employee_details.user_id');

            if ($employeeIds->isEmpty()) {
                continue;
            }

            // Step 2: Fetch employee + machine data in one go
            $employees = DB::table('employee_details')
                ->join('bio_machine', 'bio_machine.id', '=', 'employee_details.bio_machine_id')
                ->whereIn('employee_details.user_id', $employeeIds)
                ->select('employee_details.*', 'bio_machine.*', 'bio_machine.id as bioID')
                ->get()
                ->keyBy('user_id');

            // Step 3: Fetch attendance data in one query
            $existingAttendances = DB::table('attendances')
                ->whereIn('user_id', $employeeIds)
                ->where('date', $todayDate)
                ->get()
                ->keyBy('user_id');

            // Step 4: For each user, get biometric logs only once
            foreach ($employees as $emp) {
                $punches = DB::table('att_temp')
                    ->where('ip', $emp->bioID)
                    ->where('id', $emp->bio_uid)
                    ->whereDate('timestamp', $todayDate)
                    ->orderBy('timestamp')
                    ->pluck('timestamp');

                $datacount = $punches->count();

                if ($datacount === 0) {
                    continue;
                }

                $clock_in = $punches->first();
                $clock_out = $punches->last();

                // Attendance exists: update out time if needed
                if ($existingAttendances->has($emp->user_id)) {
                    if ($datacount >= 2) {
                        DB::table('attendances')
                            ->where('id', $existingAttendances[$emp->user_id]->id)
                            ->update(['clock_out_time' => $clock_out]);
                    }
                    continue;
                }

                // New attendance: insert (same logic as LiveController::flexi_db_month)
                $late = 'no';
                $clockInTime = substr($clock_in, 11, 8);
                $halfday = ($datacount <= 1) ? 'yes' : 'no';

                if (!empty($emp->late_mark_duration) && $emp->late_mark_duration > 0 && !empty($emp->start_time)) {
                    try {
                        $clockInParsed = Carbon::parse($clockInTime);
                        $startTimeParsed = Carbon::parse($emp->start_time);
                        $lateMinutes = $startTimeParsed->diffInMinutes($clockInParsed, false);
                        if ($lateMinutes > $emp->late_mark_duration) {
                            $late = 'yes';
                        }
                    } catch (\Throwable $e) {
                        // ignore bad time formats
                    }
                }

                DB::table('attendances')->insert([
                    'user_id' => $emp->user_id,
                    'company_id' => 1,
                    'location_id' => 1,
                    'clock_in_time' => $clock_in,
                    'clock_out_time' => $datacount > 1 ? $clock_out : null,
                    'date' => $todayDate,
                    'late' => $late,
                    'work_from_type' => 'office',
                    'half_day' => $halfday,
                ]);
            }
        }

        Log::info('ProcessFlexiDbMonthJob finished', [
            'requestId' => $this->requestId,
            'year' => $currentYear,
            'month' => $currentMonth,
        ]);
    }
}


