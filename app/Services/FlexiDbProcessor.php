<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlexiDbProcessor
{
    /**
     * Process flexi attendance for a given date.
     *
     * Logic is based on existing LiveController::flexi_db2() / flexi_db() behavior:
     * - Pull employees with bio machine + bio_uid, excluding users with shift schedules for the date
     * - Read att_temp punches for that date
     * - For each user:
     *   - If attendance exists: update clock_out_time only when punch_count >= 2
     *   - Else: insert new attendance row
     *
     * @return array{date:string, inserted:int, updated:int, processed:int}
     */
    public function process(string $dateYmd): array
    {
        $inserted = 0;
        $updated = 0;
        $processed = 0;

        // Fetch all eligible employees
        $employees = DB::table('employee_details')
            ->join('bio_machine', 'bio_machine.id', 'employee_details.bio_machine_id')
            ->whereNotNull('employee_details.bio_machine_id')
            ->whereNotNull('employee_details.bio_uid')
            ->leftJoin('employee_shift_schedules', function ($join) use ($dateYmd) {
                $join->on('employee_shift_schedules.user_id', '=', 'employee_details.user_id')
                    ->whereDate('employee_shift_schedules.date', $dateYmd);
            })
            ->whereNull('employee_shift_schedules.user_id')
            ->select('employee_details.*', 'bio_machine.*', 'bio_machine.id as bioID')
            ->get();

        if ($employees->isEmpty()) {
            return [
                'date' => $dateYmd,
                'inserted' => 0,
                'updated' => 0,
                'processed' => 0,
            ];
        }

        // Fetch all punches for those employees for the date
        $attendanceDataRaw = DB::table('att_temp')
            ->whereIn('ip', $employees->pluck('bioID')->unique()->toArray())
            ->whereIn('id', $employees->pluck('bio_uid')->toArray())
            ->whereDate('timestamp', $dateYmd)
            ->orderBy('uid', 'ASC')
            ->get();

        if ($attendanceDataRaw->isEmpty()) {
            return [
                'date' => $dateYmd,
                'inserted' => 0,
                'updated' => 0,
                'processed' => 0,
            ];
        }

        // Group punches by bio_uid => clock_in/clock_out/punch_count
        $attendanceData = [];

        foreach ($attendanceDataRaw as $record) {
            $bioUid = $record->id; // bio_uid is mapped to `id` in att_temp

            if (!isset($attendanceData[$bioUid])) {
                $attendanceData[$bioUid] = [
                    'records' => [],
                ];
            }

            $attendanceData[$bioUid]['records'][] = $record;
        }

        foreach ($attendanceData as $bioUid => &$data) {
            $records = $data['records'];
            $data['punch_count'] = count($records);
            $data['clock_in'] = $records[0]->timestamp;
            $data['clock_out'] = $records[count($records) - 1]->timestamp;
        }
        unset($data);

        // Fetch existing attendance records for the date
        $existingAttendances = DB::table('attendances')
            ->whereIn('user_id', $employees->pluck('user_id')->toArray())
            ->where('date', $dateYmd)
            ->get()
            ->keyBy('user_id');

        // Prepare bulk inserts + updates
        $inserts = [];
        $updates = [];

        foreach ($employees as $emp) {
            $processed++;
            $bioUid = $emp->bio_uid;

            if (!isset($attendanceData[$bioUid])) {
                continue;
            }

            $attData = $attendanceData[$bioUid];
            $clockIn = $attData['clock_in'];
            $punchCount = (int) $attData['punch_count'];
            $clockOut = $punchCount >= 2 ? $attData['clock_out'] : null;

            $attendance = $existingAttendances[$emp->user_id] ?? null;

            if ($attendance) {
                // Update existing record if there are 2 or more punches
                if ($punchCount >= 2) {
                    $updates[] = [
                        'id' => $attendance->id,
                        'clock_out_time' => $clockOut,
                    ];
                }
            } else {
                // Prepare new attendance record (keep same logic as legacy)
                $clockInTime = Carbon::parse($clockIn)->format('H:i:s');
                $startTime = '';
                $late = 'no';

                if (isset($emp->late_mark_duration) && $emp->late_mark_duration > 0) {
                    $clockInParsed = Carbon::parse($clockInTime);
                    $startTimeParsed = Carbon::parse($startTime);
                    $lateMinutes = $startTimeParsed->diffInMinutes($clockInParsed, false);

                    if ($lateMinutes > $emp->late_mark_duration) {
                        $late = 'yes';
                    }
                }

                $clockInTime = substr($clockIn, 11, 8);
                $halfday = 'no';

                if ($punchCount <= 1) {
                    $clockOut = null;
                }

                $inserts[] = [
                    'user_id' => $emp->user_id,
                    'company_id' => 1,
                    'location_id' => 1,
                    'clock_in_time' => $clockIn,
                    'clock_out_time' => $clockOut,
                    'date' => $dateYmd,
                    'late' => $late,
                    'work_from_type' => 'office',
                    'half_day' => $halfday,
                ];
            }
        }

        if (!empty($inserts)) {
            DB::table('attendances')->insert($inserts);
            $inserted = count($inserts);
        }

        if (!empty($updates)) {
            // Keep legacy behavior (per-row update), but do it in chunks to reduce memory spikes.
            foreach (array_chunk($updates, 500) as $chunk) {
                foreach ($chunk as $update) {
                    DB::table('attendances')
                        ->where('id', $update['id'])
                        ->update(['clock_out_time' => $update['clock_out_time']]);
                    $updated++;
                }
            }
        }

        Log::info('FlexiDbProcessor completed', [
            'date' => $dateYmd,
            'inserted' => $inserted,
            'updated' => $updated,
            'processed' => $processed,
        ]);

        return [
            'date' => $dateYmd,
            'inserted' => $inserted,
            'updated' => $updated,
            'processed' => $processed,
        ];
    }
}


