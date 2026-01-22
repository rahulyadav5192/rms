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
use Illuminate\Support\Facades\File;

class ProcessFlexiDbMonthJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600; // up to 1 hour for full month

    public function __construct(
        public int $year,
        public int $month,
        public string $requestId,
        public ?int $startDay = null,
        public ?int $endDay = null
    ) {
    }

    public function handle(): void
    {
        // When running via sync/http, avoid timing out mid-month.
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '1024M');

        $currentYear = $this->year;
        $currentMonth = str_pad((string) $this->month, 2, '0', STR_PAD_LEFT);
        $daysInMonth = Carbon::createFromDate($currentYear, $currentMonth, 1)->daysInMonth;

        // NOTE: This job might be unserialized from older queued payloads where these
        // typed properties were not present, so we must use isset() to avoid
        // "must not be accessed before initialization" fatals.
        $startDayValue = isset($this->startDay) ? $this->startDay : null;
        $endDayValue = isset($this->endDay) ? $this->endDay : null;

        $startDay = $startDayValue ?: 1;
        $endDay = $endDayValue ?: $daysInMonth;

        $startDay = max(1, min($daysInMonth, $startDay));
        $endDay = max(1, min($daysInMonth, $endDay));
        if ($endDay < $startDay) {
            [$startDay, $endDay] = [$endDay, $startDay];
        }

        // Some installs run with LOG_LEVEL=error, which suppresses info logs.
        // Write a dedicated debug log file for this job run.
        $debugLogPath = storage_path('logs/flexi_db_month_' . $this->requestId . '.log');
        File::append($debugLogPath, '[' . now() . "] START year={$currentYear} month={$currentMonth} startDay={$startDay} endDay={$endDay}\n");

        Log::error('ProcessFlexiDbMonthJob started', [
            'requestId' => $this->requestId,
            'year' => $currentYear,
            'month' => $currentMonth,
            'daysInMonth' => $daysInMonth,
            'startDay' => $startDay,
            'endDay' => $endDay,
        ]);

        for ($day = $startDay; $day <= $endDay; $day++) {
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
                File::append($debugLogPath, '[' . now() . "] {$todayDate} SKIP no employees\n");
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

            // Step 4: Fetch ALL att_temp data for this day in ONE query, aggregated by (ip, id)
            // This is MUCH faster than querying per employee
            $bioIds = $employees->pluck('bioID')->unique()->toArray();
            $bioUids = $employees->pluck('bio_uid')->unique()->toArray();

            $attendanceDataRaw = DB::table('att_temp')
                ->whereIn('ip', $bioIds)
                ->whereIn('id', $bioUids)
                ->whereDate('timestamp', $todayDate)
                ->select(
                    'ip as bioID',
                    'id as bio_uid',
                    DB::raw('MIN(timestamp) as clock_in'),
                    DB::raw('MAX(timestamp) as clock_out'),
                    DB::raw('COUNT(*) as punch_count')
                )
                ->groupBy('ip', 'id')
                ->get()
                ->keyBy(function ($item) {
                    return $item->bioID . '_' . $item->bio_uid;
                });

            // Step 5: Prepare bulk insert and update arrays
            $inserts = [];
            $updates = [];

            foreach ($employees as $emp) {
                $key = $emp->bioID . '_' . $emp->bio_uid;
                $attData = $attendanceDataRaw->get($key);

                if (!$attData || $attData->punch_count === 0) {
                    continue;
                }

                $clock_in = $attData->clock_in;
                $clock_out = $attData->clock_out;
                $datacount = $attData->punch_count;

                // Attendance exists: update out time if needed
                if ($existingAttendances->has($emp->user_id)) {
                    if ($datacount >= 2) {
                        $updates[] = [
                            'id' => $existingAttendances[$emp->user_id]->id,
                            'clock_out_time' => $clock_out,
                        ];
                    }
                    continue;
                }

                // New attendance: prepare insert (same logic as LiveController::flexi_db_month)
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

                $inserts[] = [
                    'user_id' => $emp->user_id,
                    'company_id' => 1,
                    'location_id' => 1,
                    'clock_in_time' => $clock_in,
                    'clock_out_time' => $datacount > 1 ? $clock_out : null,
                    'date' => $todayDate,
                    'late' => $late,
                    'work_from_type' => 'office',
                    'half_day' => $halfday,
                ];
            }

            // Step 6: Perform bulk operations
            $insertedCount = 0;
            $updatedCount = 0;

            if (!empty($inserts)) {
                DB::table('attendances')->insert($inserts);
                $insertedCount = count($inserts);
            }

            if (!empty($updates)) {
                foreach ($updates as $update) {
                    DB::table('attendances')
                        ->where('id', $update['id'])
                        ->update(['clock_out_time' => $update['clock_out_time']]);
                }
                $updatedCount = count($updates);
            }

            // lightweight progress log so we can confirm it reaches later dates
            if ($day === $startDay || $day === $endDay || $day % 5 === 0 || $insertedCount > 0 || $updatedCount > 0) {
                File::append($debugLogPath, '[' . now() . "] PROGRESS {$todayDate} (day {$day}) inserted={$insertedCount} updated={$updatedCount}\n");
                Log::error('ProcessFlexiDbMonthJob progress', [
                    'requestId' => $this->requestId,
                    'date' => $todayDate,
                    'day' => $day,
                    'inserted' => $insertedCount,
                    'updated' => $updatedCount,
                ]);
            }
        }

        File::append($debugLogPath, '[' . now() . "] FINISH year={$currentYear} month={$currentMonth} startDay={$startDay} endDay={$endDay}\n");

        Log::error('ProcessFlexiDbMonthJob finished', [
            'requestId' => $this->requestId,
            'year' => $currentYear,
            'month' => $currentMonth,
            'startDay' => $startDay,
            'endDay' => $endDay,
        ]);
    }
}


