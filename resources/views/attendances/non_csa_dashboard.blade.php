@extends('layouts.app')

@push('styles')
<style>
    .attendance-dashboard-container {
        overflow: auto;
        position: relative;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        max-height: calc(100vh - 250px);
        overflow-x: auto;
        overflow-y: auto;
    }

    .attendance-table-wrapper {
        overflow: visible;
        max-width: 100%;
        position: relative;
    }

    .attendance-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12px;
        min-width: 1400px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .attendance-table thead th {
        background: #4a5568;
        color: #fff;
        position: sticky;
        top: 0;
        z-index: 10;
        border: 1px solid #2d3748;
        padding: 12px 8px;
        text-align: center;
        font-weight: 600;
        white-space: nowrap;
        font-size: 11px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .attendance-table thead th:first-child {
        position: sticky;
        left: 0;
        top: 0;
        z-index: 12;
        background: #4a5568;
        min-width: 220px;
        padding: 12px 16px;
        box-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    .attendance-table tbody td {
        border: 1px solid #e2e8f0;
        padding: 8px 6px;
        text-align: center;
        vertical-align: middle;
        position: relative;
        background: #fff;
        transition: background-color 0.2s ease;
    }

    .attendance-table tbody tr:hover td {
        background: #f7fafc;
    }

    .attendance-table tbody td:first-child {
        position: sticky;
        left: 0;
        z-index: 9;
        background: #fff;
        font-weight: 500;
        text-align: left;
        padding: 12px 16px;
        min-width: 220px;
        box-shadow: 2px 0 4px rgba(0,0,0,0.05);
    }

    .attendance-table tbody tr:hover td:first-child {
        background: #f7fafc;
        box-shadow: 2px 0 4px rgba(0,0,0,0.08);
    }

    .attendance-table tbody tr:hover td:first-child {
        background: #f8f9fa;
    }

    .attendance-cell {
        min-height: 70px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 8px 6px;
        font-size: 11px;
        line-height: 1.4;
        border-radius: 4px;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .attendance-cell:hover {
        transform: scale(1.02);
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        background-color: rgba(0,0,0,0.05) !important;
    }

    .attendance-cell.clock-line {
        border-bottom: 2px solid rgba(0,0,0,0.1);
        padding-bottom: 4px;
        margin-bottom: 4px;
        width: 100%;
        font-weight: 600;
        background-color: #e3f2fd;
        padding: 4px;
        border-radius: 3px;
    }

    .attendance-cell.punch-line {
        color: #64748b;
        font-size: 10px;
        width: 100%;
        opacity: 0.9;
        background-color: #f3e5f5;
        padding: 4px;
        border-radius: 3px;
        margin-top: 2px;
    }

    .attendance-cell.status-full-day {
        background-color: #d4edda;
        border-left: 4px solid #28a745;
    }

    .attendance-cell.status-short-hours {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
    }

    .attendance-cell.status-missing {
        background-color: #f8d7da;
        border-left: 4px solid #dc3545;
    }

    .attendance-cell.status-absent {
        background-color: #f8d7da;
        border-left: 4px solid #dc3545;
    }

    .attendance-cell.status-normal {
        background-color: #e7f3ff;
        border-left: 4px solid #4a90e2;
    }

    .time-display {
        font-weight: 600;
        color: #1e293b;
        font-size: 11px;
        letter-spacing: 0.3px;
    }

    .time-display.missing {
        color: #94a3b8;
        font-style: italic;
        font-weight: 500;
    }

    .summary-row {
        background-color: #f8f9fa !important;
        font-weight: 600;
    }

    .summary-row td {
        background-color: #f8f9fa !important;
    }

    .summary-row td:first-child {
        background-color: #f8f9fa !important;
    }

    .summary-column {
        background-color: #e9ecef;
        font-weight: 700;
        text-align: center;
        color: #495057;
        font-size: 13px;
        min-width: 80px;
    }

    .table-indicators {
        position: absolute;
        top: 10px;
        right: 20px;
        background: #fff;
        padding: 12px 16px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        z-index: 100;
        font-size: 12px;
    }

    .indicator-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .indicator-item:last-child {
        margin-bottom: 0;
    }

    .indicator-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .indicator-color.sheet-clock {
        background-color: #e3f2fd;
        border: 2px solid #2196f3;
    }

    .indicator-color.punch-clock {
        background-color: #f3e5f5;
        border: 2px solid #9c27b0;
    }

    .indicator-label {
        font-weight: 500;
        color: #333;
        min-width: 120px;
    }

    .indicator-hours {
        color: #666;
        font-size: 11px;
    }

    .employee-name-cell {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
    }

    .employee-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .filter-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .dashboard-summary {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .summary-card {
        background: #fff;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        flex: 1;
        min-width: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .summary-card h5 {
        margin: 0 0 8px 0;
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        text-align: center;
    }

    .summary-card .value {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        text-align: center;
    }

    .date-header {
        writing-mode: vertical-rl;
        text-orientation: mixed;
        transform: rotate(180deg);
        min-width: 70px;
        width: 70px;
    }

    .attendance-table thead th:not(:first-child):not(.summary-column) {
        min-width: 70px;
        width: 70px;
    }

    .attendance-table tbody td:not(:first-child):not(.summary-column) {
        min-width: 70px;
        width: 70px;
    }

    .date-header-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        padding: 4px 0;
    }

    .day-number {
        font-weight: 700;
        font-size: 14px;
        color: #fff;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    .day-name {
        font-size: 10px;
        color: rgba(255,255,255,0.9);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
        .attendance-table {
            font-size: 9px;
        }
        
        .attendance-cell {
            font-size: 8px;
            min-height: 40px;
        }
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .pagination-info {
        font-size: 14px;
        color: #666;
    }

    .pagination-wrapper {
        margin-top: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 4px;
    }
</style>
@endpush

@section('filter-section')
    <x-filters.filter-box>
        <div class="select-box d-flex py-2 pr-2 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.month')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="month" id="month">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="select-box d-flex py-2 pr-2 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.year')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="year" id="year">
                    @foreach(range(Carbon\Carbon::now()->year - 5, Carbon\Carbon::now()->year + 5) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if(user()->permission('view_attendance') == 'all')
            <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.department')</p>
                <div class="select-status">
                    <select class="form-control select-picker" name="department" id="department" data-live-search="true">
                        <option value="all">@lang('app.all')</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ (request('department') == $dept->id) ? 'selected' : '' }}>
                                {{ $dept->team_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('Branch')</p>
                <div class="select-status">
                    <select class="form-control select-picker" name="branch" id="branch" data-live-search="true">
                        <option value="all">@lang('app.all')</option>
                        @foreach($branches as $br)
                            <option value="{{ $br->id }}" {{ (request('branch') == $br->id) ? 'selected' : '' }}>
                                {{ $br->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.designation')</p>
                <div class="select-status">
                    <select class="form-control select-picker" name="designation" id="designation" data-live-search="true">
                        <option value="all">@lang('app.all')</option>
                        @foreach($designations as $des)
                            <option value="{{ $des->id }}" {{ (request('designation') == $des->id) ? 'selected' : '' }}>
                                {{ $des->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0 input-with-icon">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.name')</p>
            <div class="select-status position-relative">
                <input type="text" class="form-control" name="name" id="name" 
                       placeholder="Name Or Id" value="{{ request('name') }}">
            </div>
        </div>

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.status')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="active_status" id="active_status">
                    <option value="" {{ request('active_status') === '' ? 'selected' : '' }}>@lang('app.all')</option>
                    <option value="0" {{ request('active_status') == '0' || (!request()->has('active_status')) ? 'selected' : '' }}>@lang('app.active')</option>
                    <option value="1" {{ request('active_status') == '1' ? 'selected' : '' }}>@lang('app.inactive')</option>
                </select>
            </div>
        </div>

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Employee Type</p>
            <div class="select-status">
                <select class="form-control select-picker" name="employee_type" id="employee_type">
                    <option value="all" {{ request('employee_type') == 'all' || !request('employee_type') ? 'selected' : '' }}>All</option>
                    <option value="csa" {{ request('employee_type') == 'csa' ? 'selected' : '' }}>CSA</option>
                    <option value="non_csa" {{ request('employee_type') == 'non_csa' ? 'selected' : '' }}>Non-CSA</option>
                </select>
            </div>
        </div>

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Attendance Type</p>
            <div class="select-status">
                <select class="form-control select-picker" name="attendance_type" id="attendance_type">
                    <option value="all" {{ request('attendance_type') == 'all' ? 'selected' : '' }}>All</option>
                    <option value="missing" {{ request('attendance_type') == 'missing' ? 'selected' : '' }}>Only Missing</option>
                    <option value="short_hours" {{ request('attendance_type') == 'short_hours' ? 'selected' : '' }}>Only Short Hours</option>
                </select>
            </div>
        </div>

        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
    </x-filters.filter-box>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Indicators and Export Button -->
                        <div class="d-flex justify-content-between align-items-center mb-3" style="flex-wrap: wrap; gap: 15px;">
                            <div class="d-flex align-items-center" style="gap: 20px; flex-wrap: wrap;">
                                <div class="indicator-item" style="margin: 0;">
                                    <div class="indicator-color sheet-clock"></div>
                                    <div>
                                        <div class="indicator-label" style="font-size: 13px; min-width: auto; font-weight: 500;">Sheet Clock In/Out</div>
                                        <div class="indicator-hours" style="font-size: 11px;">Working Hours: Sheet Data</div>
                                    </div>
                                </div>
                                <div class="indicator-item" style="margin: 0;">
                                    <div class="indicator-color punch-clock"></div>
                                    <div>
                                        <div class="indicator-label" style="font-size: 13px; min-width: auto; font-weight: 500;">Punch In/Out</div>
                                        <div class="indicator-hours" style="font-size: 11px;">Working Hours: Biometric Data</div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('attendances.export_non_csa_dashboard', request()->all()) }}" 
                                   class="btn btn-primary">
                                    <i class="fa fa-download"></i> Export to Excel
                                </a>
                            </div>
                        </div>

                        <!-- Attendance Table -->
                        <div class="attendance-dashboard-container">
                            <div class="attendance-table-wrapper">
                                <table class="attendance-table">
                                    <thead>
                                        <tr>
                                            <th class="employee-header">Employee</th>
                                            @foreach($allDates as $dateInfo)
                                                <th class="date-header">
                                                    <div class="date-header-content">
                                                        <span class="day-number">{{ $dateInfo['day'] }}</span>
                                                        <span class="day-name">{{ $dateInfo['dayName'] }}</span>
                                                    </div>
                                                </th>
                                            @endforeach
                                            <th class="summary-column">Total Present</th>
                                            <th class="summary-column">Total Absent</th>
                                            <th class="summary-column">Short Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employees as $employee)
                                            @php
                                                $empData = $attendanceData[$employee->id] ?? null;
                                                $empSummary = $empData['summary'] ?? ['present_days' => 0, 'absent_days' => 0, 'short_hour_days' => 0];
                                            @endphp
                                            <tr>
                                                <td class="employee-name-cell"
                                                    data-user-id="{{ $employee->id }}"
                                                    data-employee-name="{{ $employee->name }}"
                                                    data-employee-id="{{ $employee->employee_id ?? '' }}">
                                                    @if($employee->image)
                                                        <img src="{{ asset('user-uploads/avatar/' . $employee->image) }}" 
                                                             alt="{{ $employee->name }}" class="employee-avatar">
                                                    @else
                                                        <div class="employee-avatar" style="background: #ddd; display: flex; align-items: center; justify-content: center; color: #666;">
                                                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div style="font-weight: 600;">{{ $employee->name }}</div>
                                                        @if($employee->employee_id)
                                                            <div style="font-size: 9px; color: #666;">{{ $employee->employee_id }}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                @foreach($allDates as $dateInfo)
                                                    @php
                                                        $date = $dateInfo['date'];
                                                        $dayData = $empData['dates'][$date] ?? null;
                                                    @endphp
                                                    <td>
                                                        @if($dayData)
                                                            <div class="attendance-cell status-{{ $dayData['cell_status'] }}" 
                                                                 data-user-id="{{ $employee->id }}"
                                                                 data-date="{{ $date }}"
                                                                 data-employee-name="{{ $employee->name }}"
                                                                 data-employee-id="{{ $employee->employee_id ?? '' }}">
                                                                <div class="clock-line">
                                                                    <div class="time-display {{ empty($dayData['clock_in']) ? 'missing' : '' }}">
                                                                        {{ $dayData['clock_in'] ? \Carbon\Carbon::parse($dayData['clock_in'])->format('H:i') : '--' }}
                                                                    </div>
                                                                    <div class="time-display {{ empty($dayData['clock_out']) ? 'missing' : '' }}">
                                                                        {{ $dayData['clock_out'] ? \Carbon\Carbon::parse($dayData['clock_out'])->format('H:i') : '--' }}
                                                                    </div>
                                                                    @if(isset($dayData['total_hours']) && $dayData['total_hours'] > 0)
                                                                        <div style="font-size: 9px; margin-top: 2px; font-weight: 600; color: #1976d2;">
                                                                            Sheet: {{ number_format($dayData['total_hours'], 1) }}h
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="punch-line">
                                                                    <div>{{ $dayData['punch_in'] ? \Carbon\Carbon::parse($dayData['punch_in'])->format('H:i') : '--' }}</div>
                                                                    <div>{{ $dayData['punch_out'] ? \Carbon\Carbon::parse($dayData['punch_out'])->format('H:i') : '--' }}</div>
                                                                    @if(isset($dayData['punch_hours']) && $dayData['punch_hours'] > 0)
                                                                        <div style="font-size: 9px; margin-top: 2px; font-weight: 600; color: #7b1fa2;">
                                                                            Punch: {{ number_format($dayData['punch_hours'], 1) }}h
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="attendance-cell status-absent">
                                                                <div style="color: #999;">Absent</div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="summary-column">{{ $empSummary['present_days'] }}</td>
                                                <td class="summary-column">{{ $empSummary['absent_days'] }}</td>
                                                <td class="summary-column">{{ $empSummary['short_hour_days'] }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ count($allDates) + 4 }}" class="text-center py-4">
                                                    No employees found matching the filters.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if(isset($employeesPaginator) && $employeesPaginator->hasPages())
                            <div class="pagination-wrapper d-flex justify-content-between align-items-center">
                                <div class="pagination-info">
                                    Showing {{ $employeesPaginator->firstItem() ?? 0 }} to {{ $employeesPaginator->lastItem() ?? 0 }} 
                                    of {{ $employeesPaginator->total() }} employees
                                </div>
                                <div>
                                    {{ $employeesPaginator->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize select pickers
        $('.select-picker').selectpicker();

        // Filter change handler
        function applyFilters() {
            var params = new URLSearchParams();
            
            params.append('month', $('#month').val());
            params.append('year', $('#year').val());
            params.append('department', $('#department').val());
            params.append('branch', $('#branch').val());
            params.append('designation', $('#designation').val());
            params.append('name', $('#name').val());
            params.append('active_status', $('#active_status').val());
            params.append('employee_type', $('#employee_type').val());
            params.append('attendance_type', $('#attendance_type').val());
            
            window.location.href = '{{ route("attendances.non_csa_dashboard") }}?' + params.toString();
        }

        $('#month, #year, #department, #branch, #designation, #active_status, #employee_type, #attendance_type').on('change', function() {
            applyFilters();
        });

        $('#name').on('keypress', function(e) {
            if (e.which === 13) {
                applyFilters();
            }
        });

        $('#reset-filters').on('click', function() {
            window.location.href = '{{ route("attendances.non_csa_dashboard") }}';
        });

        // Handle day cell click to show per-day modal
        $(document).on('click', '.attendance-cell', function() {
            var userId = $(this).data('user-id');
            var date = $(this).data('date');
            var employeeName = $(this).data('employee-name');
            var employeeId = $(this).data('employee-id');
            
            if (!userId || !date) return;
            
            var url = "{{ route('attendances.non_csa_cell_details', [':userId', ':date']) }}";
            url = url.replace(':userId', userId).replace(':date', date);
            
            $('#attendanceCellModal .modal-title').html('Attendance Details - ' + employeeName + ' (' + employeeId + ') - ' + date);
            $('#attendanceCellModal .modal-body').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
            $('#attendanceCellModal').modal('show');
            
            $.easyAjax({
                url: url,
                type: "GET",
                blockUI: true,
                container: '#attendanceCellModal',
                success: function (response) {
                    if (response.status == 'success') {
                        $('#attendanceCellModal .modal-body').html(response.html);
                    } else {
                        $('#attendanceCellModal .modal-body').html('<div class="alert alert-danger">Failed to load data.</div>');
                    }
                },
                error: function() {
                    $('#attendanceCellModal .modal-body').html('<div class="alert alert-danger">Error loading data.</div>');
                }
            });
        });

        // Handle employee name click to show full month details
        $(document).on('click', '.employee-name-cell', function() {
            var userId = $(this).data('user-id');
            var employeeName = $(this).data('employee-name');
            var employeeId = $(this).data('employee-id');

            if (!userId) return;

            var url = "{{ route('attendances.non_csa_employee_month_details', ':userId') }}";
            url = url.replace(':userId', userId);

            var month = $('#month').val();
            var year = $('#year').val();

            $('#attendanceEmployeeMonthModal .modal-title').html(
                'Monthly Attendance - ' + employeeName + (employeeId ? ' (' + employeeId + ')' : '')
            );
            $('#attendanceEmployeeMonthModal .modal-body').html(
                '<div class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>'
            );
            $('#attendanceEmployeeMonthModal').modal('show');

            $.easyAjax({
                url: url,
                type: "GET",
                data: {
                    month: month,
                    year: year
                },
                blockUI: true,
                container: '#attendanceEmployeeMonthModal',
                success: function (response) {
                    if (response.status == 'success') {
                        $('#attendanceEmployeeMonthModal .modal-body').html(response.html);
                    } else {
                        $('#attendanceEmployeeMonthModal .modal-body').html('<div class="alert alert-danger">Failed to load data.</div>');
                    }
                },
                error: function() {
                    $('#attendanceEmployeeMonthModal .modal-body').html('<div class="alert alert-danger">Error loading data.</div>');
                }
            });
        });
    });
</script>
@endpush

<!-- Attendance Cell Details Modal -->
<div class="modal fade" id="attendanceCellModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attendance Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center py-4">
                    <i class="fa fa-spinner fa-spin"></i> Loading...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Employee Month Details Modal -->
<div class="modal fade" id="attendanceEmployeeMonthModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Monthly Attendance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center py-4">
                    <i class="fa fa-spinner fa-spin"></i> Loading...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
