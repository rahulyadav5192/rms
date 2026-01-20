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

        .data-table a {
            color: #007bff;
            text-decoration: none;
        }

        .data-table a:hover {
            text-decoration: underline;
        }
    </style>
@endpush

@section('content')
    <div class="px-4 py-0 py-lg-3 border-top-0 admin-dashboard">
        <div class="row">
            <!-- TL Profile Card -->
            <div class="col-md-12">
                <div class="dashboard-card">
                    <h4>TL Profile</h4>
                    <div class="value">{{ user()->name }} (Username: {{ user()->username }})</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="col-md-12">
                <div class="dashboard-card">
                    <h4>Filter by Month and Year</h4>
                    <div class="d-flex align-items-center">
                        <form action="{{ route('tl.dash') }}" method="GET" class="d-flex">
                            <div class="mr-3">
                                <select name="month" class="form-control p-2 f-14 f-w-500 border-additional-grey" onchange="this.form.submit()">
                                    @foreach ($months as $m)
                                        <option value="{{ $m }}" {{ \Carbon\Carbon::parse($startDate)->month === $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="year" class="form-control p-2 f-14 f-w-500 border-additional-grey" onchange="this.form.submit()">
                                    @foreach ($years as $yearOption)
                                        <option value="{{ $yearOption }}" {{ \Carbon\Carbon::now()->year === $yearOption ? 'selected' : '' }}>
                                            {{ $yearOption }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Team Data Card -->
            <div class="col-md-12 mt-4">
                <div class="dashboard-card">
                    <h4>Team Attendance Data ({{ $currentMonthYear }})</h4>
                    @if (empty($employeeData))
                        <p>No team data available for the selected month.</p>
                    @else
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Total Present Days</th>
                                    <th>Total Target Login Hrs</th>
                                    <th>Total Login Hrs</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employeeData as $employee)
                                    <tr>
                                        <td>{{ $employee['employee_name'] }}</td>
                                        <td>{{ number_format($employee['total_present_day'], 2) }}</td>
                                        <td>{{ number_format($employee['total_target_login_hrs_sec'] / 3600, 2) }}</td>
                                        <td>{{ number_format($employee['total_login_sec'] / 3600, 2) }}</td>
                                        <td>
                                            <a href="{{ url('account/employee-detail', $employee['user_id']) }}?year={{ $year }}&month={{ $month }}">View Details</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection