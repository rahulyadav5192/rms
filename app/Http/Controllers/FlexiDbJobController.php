<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessFlexiDbJob;
use App\Jobs\ProcessFlexiDbMonthJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FlexiDbJobController extends Controller
{
    /**
     * Queue flexi_db processing and return immediately.
     *
     * Query params:
     * - date (optional): YYYY-MM-DD. If omitted, uses same random logic as legacy flexi_db.
     */
    public function queueFlexiDb(Request $request)
    {
        $date = $request->query('date');

        if (!$date) {
            $arr = rand(1, 5);
            $date = $arr != 3 ? Carbon::now()->format('Y-m-d') : Carbon::now()->subDay(1)->format('Y-m-d');
        }

        $requestId = (string) Str::uuid();

        ProcessFlexiDbJob::dispatch($date, $requestId);

        return response()->json([
            'queued' => true,
            'requestId' => $requestId,
            'date' => $date,
        ]);
    }

    /**
     * Queue flexi_db_month processing (full month) and return immediately.
     *
     * Query params:
     * - year (optional): defaults to current year
     * - month (optional): defaults to previous day’s month (same as legacy flexi_db_month)
     */
    public function queueFlexiDbMonth(Request $request)
    {
        $now = Carbon::now();
        $year = (int) ($request->query('year') ?: $now->format('Y'));
        $month = (int) ($request->query('month') ?: $now->subDay()->format('m'));
        $startDay = $request->query('start_day');
        $endDay = $request->query('end_day');

        $requestId = (string) Str::uuid();

        ProcessFlexiDbMonthJob::dispatch(
            $year,
            $month,
            $requestId,
            is_null($startDay) ? null : (int) $startDay,
            is_null($endDay) ? null : (int) $endDay
        );

        return response()->json([
            'queued' => true,
            'requestId' => $requestId,
            'year' => $year,
            'month' => $month,
            'start_day' => is_null($startDay) ? null : (int) $startDay,
            'end_day' => is_null($endDay) ? null : (int) $endDay,
        ]);
    }
}


