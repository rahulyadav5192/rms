@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-gradient-primary text-white text-center py-4">
            <h3 class="mb-0 text-dark">Manager Travel Dashboard</h3>
            <p class="text-dark mb-0">Team Travel Requests</p>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Destination</th>
                            <th>Amount (₹)</th>
                            <th>Transport</th>
                            <th>Manager Status</th>
                            <th>Account Status</th>
                            <th>Account Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($travelRequests as $index => $travel)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $travel->trip_type == 1 ? 'One Way' : 'Two Way' }}</td>
                            <td>{{ $travel->employee_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($travel->start_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($travel->end_date)->format('d M Y') }}</td>
                            <td>{{ $travel->destination }}</td>
                            <td>₹{{ number_format($travel->expenses, 2) }}</td>
                            <td>{{ ucfirst($travel->transport_mode) }}</td>
                            <td>
                                @switch($travel->manager_status)
                                    @case(0) <span class="badge badge-success">Approved</span> @break
                                    @case(1) <span class="badge badge-warning">Pending</span> @break
                                    @case(2) <span class="badge badge-danger">Rejected</span> @break
                                    @case(3) <span class="badge badge-info">Need Changes</span> @break
                                    @default <span class="badge badge-secondary">Unknown</span>
                                @endswitch
                            </td>
                            <td>
                                @switch($travel->approve_status)
                                    @case(0) <span class="badge badge-success">Approved</span> @break
                                    @case(1) <span class="badge badge-danger">Rejected</span> @break
                                    @case(2) <span class="badge badge-warning">Pending</span> @break
                                    @default <span class="badge badge-secondary">Unknown</span>
                                @endswitch
                            </td>
                            <td>{{ $travel->account_remark ?? '' }}</td>
                            <td>
                                <a href="{{ route('manager.travel.details', [$travel->department_id, $travel->id]) }}" 
                                   class="btn btn-outline-primary btn-sm">View Details</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection