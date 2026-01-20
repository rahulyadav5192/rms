@extends('layouts.app')

@push('styles')
    <style>
        .h-200 {
            height: 340px;
            overflow-y: auto;
        }

        .dashboard-card {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.3s ease;
        }

        .dashboard-card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card h4 {
            color: #333333;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }

        .dashboard-card .value {
            font-size: 20px;
            color: #444444;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .user-activity-timeline {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .user-activity-timeline h4 {
            color: #333333;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }

        .user-activity-timeline li {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            color: #666666;
            font-size: 14px;
        }

        .user-activity-timeline li:last-child {
            border-bottom: none;
        }

        .user-activity-timeline i {
            color: #666666;
            margin-right: 8px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
            color: #444444;
            font-size: 14px;
        }

        .data-table th {
            background: #f5f5f5;
            color: #333333;
            font-weight: 600;
            text-transform: uppercase;
        }

        .data-table tr:hover {
            background: #f9f9f9;
        }

        /* Conditional row colors for Present Days */
        .present-1 { background-color: #ffff !important; color: white; }
        .present-0 { background-color: #dc3545 !important; color: white; }
        .present-0.5 { background-color: #17a2b8 !important; color: white; }
    </style>
@endpush

@section('content')
    <div class="px-4 py-0 py-lg-3 border-top-0 admin-dashboard">
        <div class="row">
            <!-- Employee Profile Card -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h4>Employee Profile</h4>
                    <div class="value">Employee Name: {{ $filteredData[0]['employee_name'] ?? 'N/A' }} (ID: {{ $user_id ?? 'N/A' }})</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h4>Filter by Month and Year</h4>
                    <div class="d-flex align-items-center">
                        <form action="{{ route('employee.detail', $user_id ?? '') }}" method="GET" class="d-flex">
                            <div class="mr-3">
                                <select name="month" class="form-control p-2 f-14 f-w-500 border-additional-grey" onchange="this.form.submit()">
                                    @foreach ($months as $month)
                                        <option value="{{ $month }}" {{ \Carbon\Carbon::parse($startDate)->month === $month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="year" class="form-control p-2 f-14 f-w-500 border-additional-grey" onchange="this.form.submit()">
                                    @foreach ($years as $yearOption)
                                        <option value="{{ $yearOption }}" {{ \Carbon\Carbon::parse($startDate)->year === $yearOption ? 'selected' : '' }}>
                                            {{ $yearOption }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Total Cards -->
            <div class="col-md-4 mt-4">
                <div class="dashboard-card">
                    <h4>Total Present Days</h4>
                    <div class="value">{{ number_format($totalPresentDays, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4 mt-4">
                <div class="dashboard-card">
                    <h4>Total Target Login Hrs</h4>
                    <div class="value">{{ number_format($totalTargetLoginHrsSec / 3600, 2) }}</div>
                </div>
            </div>
            <div class="col-md-12 mt-4">
                <div class="dashboard-card">
                    <h4>Total Login</h4>
                    <div class="value">{{ number_format($totalLoginSec / 3600, 2) }}</div>
                </div>
            </div>

            <!-- Custom Tabs -->
            <div class="col-md-12 mt-4">
                <div class="dashboard-card">
                    <div class="custom-tabs">
                        <button class="tab-button active" onclick="openTab('manual')">Manual</button>
                        <button class="tab-button" onclick="openTab('biometric')">Biometric</button>
                    </div>

                    <div class="tab-content">
                        <!-- Manual Tab -->
                        <div class="tab-pane active" id="manual">
                            <h4>Filtered Data ({{ $currentMonthYear }})</h4>
                            @if (empty($filteredData))
                                <p>No data available for the selected month.</p>
                            @else
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Employee Name</th>
                                            <th>TL Name</th>
                                            <th>LOB</th>
                                            <th>EMP Type</th>
                                            <th>Shift</th>
                                            <th>Present Days</th>
                                            <th>Target Login Hrs</th>
                                            <th>Total Login</th>
                                            <th>Extra Login Hrs</th>
                                            <th>Less Login Hrs</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($filteredData as $day)
                                            <tr class="{{ $day['present_day'] == 1 ? 'present-1' : ($day['present_day'] == 0 ? 'present-0' : ($day['present_day'] == 0.5 ? 'present-0.5' : '')) }}">
                                                <td>{{ $day['date'] }}</td>
                                                <td>{{ $day['employee_name'] }}</td>
                                                <td>{{ $day['tl_name'] ?? '' }}</td>
                                                <td>{{ $day['lob'] ?? '' }}</td>
                                                <td>{{ $day['emp_type'] ?? '' }}</td>
                                                <td>{{ $day['shift'] ?? '' }}</td>
                                                <td>{{ $day['present_day'] }}</td>
                                                <td>{{ number_format($day['target_login_hrs_sec'] / 3600, 2) }}</td>
                                                <td>{{ number_format($day['total_login_sec'] / 3600, 2) }}</td>
                                                <td>{{ number_format(max(0, ($day['total_login_sec'] / 3600) - ($day['target_login_hrs_sec'] / 3600)), 2) }}</td>
                                                <td>{{ number_format(max(0, ($day['target_login_hrs_sec'] / 3600) - ($day['total_login_sec'] / 3600)), 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>

                        <!-- Biometric Tab -->
                        <div class="tab-pane" id="biometric">
                            <h4>Biometric Data ({{ $currentMonthYear }})</h4>
                            @if (empty($biometricData))
                                <p>No biometric data available for the selected month.</p>
                            @else
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Clock In Time</th>
                                            <th>Clock Out Time</th>
                                            <th>Login Hour</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($biometricData as $record)
                                            <tr>
                                                <td>{{ $record['date'] }}</td>
                                                <td>{{ $record['clock_in_time'] ?? '' }}</td>
                                                <td>{{ $record['clock_out_time'] ?? '' }}</td>
                                                <td>{{ number_format($record['login_hour'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Activity Timeline -->
            <div class="col-md-12 mt-4">
                <div class="user-activity-timeline">
                    <h4>User Activity Timeline</h4>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-user mr-2"></i> {{ user()->name }} - Logged in - {{ \Carbon\Carbon::now()->format('h:i a') }}</li>
                        @foreach ($filteredData as $day)
                            <li><i class="fas fa-clock mr-2"></i> {{ user()->name }} - Attended on {{ $day['date'] }} - {{ number_format($day['total_login_sec'] / 3600, 2) }} hrs</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openTab(tabName) {
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('active');
            });
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            document.getElementById(tabName).classList.add('active');
            document.querySelector(`.tab-button[onclick="openTab('${tabName}')"]`).classList.add('active');
        }

        document.addEventListener('DOMContentLoaded', () => {
            openTab('manual');
        });
    </script>
@endpush

@push('styles')
    <style>
        .custom-tabs {
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 20px;
        }
        .tab-button {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 5px;
            cursor: pointer;
            color: #333;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-bottom: none;
            border-radius: 5px 5px 0 0;
            transition: all 0.3s ease;
        }
        .tab-button:hover {
            color: #007bff;
            background: #e9ecef;
        }
        .tab-button.active {
            color: #007bff;
            background: #ffffff;
            border-bottom: 2px solid #007bff;
            font-weight: 600;
        }
        .tab-content {
            position: relative;
            overflow: hidden;
        }
        .tab-pane {
            display: none;
            padding: 15px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 0 5px 5px 5px;
            animation: slideFade 0.4s ease-in-out;
        }
        .tab-pane.active {
            display: block;
        }
        @keyframes slideFade {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush