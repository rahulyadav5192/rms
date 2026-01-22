<div class="attendance-cell-details">
    <div class="row">
        <!-- Sheet Data Section -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa fa-file-alt"></i> Sheet Data (Manual Upload)</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Clock In:</th>
                            <td>{{ $sheetData && $sheetData->in_time ? \Carbon\Carbon::parse($sheetData->in_time)->format('H:i:s') : '--' }}</td>
                        </tr>
                        <tr>
                            <th>Clock Out:</th>
                            <td>{{ $sheetData && $sheetData->out_time ? \Carbon\Carbon::parse($sheetData->out_time)->format('H:i:s') : '--' }}</td>
                        </tr>
                        <tr>
                            <th>Working Hours:</th>
                            <td>
                                @if($sheetHours > 0)
                                    <strong class="text-primary">{{ number_format($sheetHours, 2) }} hours</strong>
                                @else
                                    --
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Attendance Status:</th>
                            <td>
                                @if($sheetData && $sheetData->attendance_status)
                                    <span class="badge badge-info">{{ $sheetData->attendance_status }}</span>
                                @else
                                    --
                                @endif
                            </td>
                        </tr>
                        @if($sheetData)
                        <tr>
                            <th>Process:</th>
                            <td>{{ $sheetData->process ?? '--' }}</td>
                        </tr>
                        <tr>
                            <th>Department:</th>
                            <td>{{ $sheetData->department ?? '--' }}</td>
                        </tr>
                        <tr>
                            <th>Supervisor:</th>
                            <td>{{ $sheetData->supervisor_name ?? '--' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Punch Data Section -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-purple text-white" style="background-color: #9c27b0;">
                    <h5 class="mb-0"><i class="fa fa-fingerprint"></i> Punch Data (Biometric)</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Punch In:</th>
                            <td>{{ $punchData && $punchData->clock_in_time ? \Carbon\Carbon::parse($punchData->clock_in_time)->format('H:i:s') : '--' }}</td>
                        </tr>
                        <tr>
                            <th>Punch Out:</th>
                            <td>{{ $punchData && $punchData->clock_out_time ? \Carbon\Carbon::parse($punchData->clock_out_time)->format('H:i:s') : '--' }}</td>
                        </tr>
                        <tr>
                            <th>Working Hours:</th>
                            <td>
                                @if($punchHours > 0)
                                    <strong class="text-purple" style="color: #7b1fa2;">{{ number_format($punchHours, 2) }} hours</strong>
                                @else
                                    --
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Late:</th>
                            <td>
                                @if($punchData && $punchData->late)
                                    <span class="badge badge-warning">{{ $punchData->late }}</span>
                                @else
                                    --
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Half Day:</th>
                            <td>
                                @if($punchData && $punchData->half_day)
                                    <span class="badge badge-info">{{ $punchData->half_day }}</span>
                                @else
                                    --
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Biometric Logs Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fa fa-list"></i> Biometric Logs (All Punches for {{ $dateFormatted }})</h5>
                </div>
                <div class="card-body">
                    @if($biometricLogs && count($biometricLogs) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Timestamp</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($biometricLogs as $index => $log)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $log['timestamp'] }}</td>
                                            <td><strong>{{ $log['time'] }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <strong>Total Punches: {{ count($biometricLogs) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="fa fa-info-circle"></i> No biometric logs found for this date.
                            @if(!$user->employeeDetail || !$user->employeeDetail->bio_machine_id || !$user->employeeDetail->bio_uid)
                                <br><small>Employee may not have biometric machine ID or UID configured.</small>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .attendance-cell-details .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .attendance-cell-details .card-header {
        font-weight: 600;
    }
    .attendance-cell-details .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
</style>

