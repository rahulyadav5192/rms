<div class="employee-month-details">
    <div class="mb-3">
        <h5 class="mb-1">{{ $user->name }} @if($user->employeeDetail && $user->employeeDetail->employee_id)<small class="text-muted">({{ $user->employeeDetail->employee_id }})</small>@endif</h5>
        <p class="mb-0 text-muted">{{ $monthLabel }}</p>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th style="width: 18%;">Date</th>
                    <th style="width: 12%;">Check In</th>
                    <th style="width: 12%;">Check Out</th>
                    <th style="width: 12%;">Sheet Hours</th>
                    <th style="width: 12%;">Punch In</th>
                    <th style="width: 12%;">Punch Out</th>
                    <th style="width: 12%;">Punch Hours</th>
                </tr>
            </thead>
            <tbody>
                @forelse($days as $day)
                    <tr>
                        <td>{{ $day['dateFormatted'] }}</td>
                        <td>{{ $day['clock_in'] ? \Carbon\Carbon::parse($day['clock_in'])->format('H:i:s') : '--' }}</td>
                        <td>{{ $day['clock_out'] ? \Carbon\Carbon::parse($day['clock_out'])->format('H:i:s') : '--' }}</td>
                        <td>
                            @if($day['sheet_hours'] > 0)
                                <strong>{{ number_format($day['sheet_hours'], 2) }} h</strong>
                            @else
                                --
                            @endif
                        </td>
                        <td>{{ $day['punch_in'] ? \Carbon\Carbon::parse($day['punch_in'])->format('H:i:s') : '--' }}</td>
                        <td>{{ $day['punch_out'] ? \Carbon\Carbon::parse($day['punch_out'])->format('H:i:s') : '--' }}</td>
                        <td>
                            @if($day['punch_hours'] > 0)
                                <strong>{{ number_format($day['punch_hours'], 2) }} h</strong>
                            @else
                                --
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No attendance data found for this month.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .employee-month-details table th,
    .employee-month-details table td {
        font-size: 12px;
        vertical-align: middle;
    }
</style>


