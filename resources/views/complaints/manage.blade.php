@extends('layouts.app')

@section('content')
<style>
    /* === Complaints & Manage Page Styling === */
.content-wrapper {
    padding: 20px;
    background: #f8f9fb;
    min-height: 100vh;
}

.card.action-bar {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    padding: 20px;
    transition: all 0.3s ease;
}

.card.action-bar:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

/* Headings */
.card h4 {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

/* Form Styling */
form label {
    font-weight: 600;
    color: #555;
}

form input, 
form select, 
form textarea {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 10px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

form input:focus, 
form select:focus, 
form textarea:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.4);
    outline: none;
}

/* Buttons */
.btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 8px 16px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    color: #fff;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.4);
}

.btn-success {
    background: linear-gradient(45deg, #28a745, #218838);
    border: none;
    color: #fff;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40,167,69,0.4);
}

/* Table Styling */
.table {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.table thead {
    background: #485c72;
    color: #fff;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 0.5px;
}

.table thead th {
    border: none !important;
    padding: 14px;
}

.table tbody tr {
    transition: background 0.2s ease;
}

.table tbody tr:nth-child(even) {
    background: #f9fbff;
}

.table tbody tr:hover {
    background: #eef4ff;
}

.table td, 
.table th {
    padding: 12px 14px;
    vertical-align: middle;
}

/* Status Badges */
.badge {
    border-radius: 20px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 500;
}

.badge-info {
    background: #17a2b8;
}

.badge-success {
    background: #28a745;
}

.badge-danger {
    background: #dc3545;
}

/* Complaint Description */
.table td {
    word-wrap: break-word;
    max-width: 350px;
}

/* Alert Styling */
.alert {
    border-radius: 8px;
    font-weight: 500;
}

</style>
<div class="content-wrapper">
    <div class="card action-bar">

        @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
        </div>
        @endif

        <h4 class="m-3">HR Complaints Management</h4>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee</th>
                    <th>Email</th>
                    <th>Branch</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Subject</th>
                    <th>Category</th>
                    <th>Complaint</th>
                    <th>Created</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($complaints as $key => $c)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $c->employee_name }} <br> (ID: {{ $c->employee_id }})</td>
                    <td>{{ $c->employee_email }}</td>
                    <td>{{ $c->branch_name ?? 'N/A' }}</td>
                    <td>{{ $c->department ?? 'N/A' }}</td>
                    <td>{{ $c->designation ?? 'N/A' }}</td>
                    <td>{{ $c->subject }}</td>
                    <td>{{ $c->category }}</td>
                    <td style="max-width:350px;">{{ $c->complaint }}</td>
                    <td>{{ \Carbon\Carbon::parse($c->created_at)->format('d-m-Y') }}</td>
                    <td>
                        @if($c->status == 0)
                            <span class="badge badge-info">Pending</span>
                        @elseif($c->status == 1)
                            <span class="badge badge-success">Resolved</span>
                        @else
                            <span class="badge badge-danger">Rejected</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('complaints.updateStatus', $c->id) }}" method="POST" class="d-inline">
                            @csrf
                            <select name="status" class="form-control form-control-sm mb-2" onchange="this.form.submit()">
                                <option value="0" {{ $c->status == 0 ? 'selected' : '' }}>Pending</option>
                                <option value="1" {{ $c->status == 1 ? 'selected' : '' }}>Resolved</option>
                                <option value="2" {{ $c->status == 2 ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection
