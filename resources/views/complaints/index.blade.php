@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')
<style>
.search-status {
    display: flex;
}  
.search-status .btn-primary.rounded.f-14.p-2.mr-3 {
    margin-left: 20px;
}

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
    <form action="{{ url('account/hr-complaints') }}" id="complaintForm" method="GET"> 
        <x-filters.filter-box>
        </x-filters.filter-box>
    </form>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="card action-bar">

            {{-- Flash message --}}
            @if(Session::has('message'))
            <div class="alert alert-info" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
            
            {{-- ✅ Show Manage Complaints button only if user has permission --}}
        @php
            $addDesignationPermission = user()->permission('view_employees');
        @endphp

        @if($addDesignationPermission == 'all' && in_array(auth()->user()->id, ['13884', '1', '440']))
            <div class="mb-3">
                <a href="{{ route('complaints.manage') }}" class="btn btn-success">
                    <i class="fa fa-tasks"></i> Manage All Complaints
                </a>
            </div>
        @endif

            {{-- Complaint Submission Form --}}
            <form class="m-4" action="{{ url('complaint_save') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col">
                        <label for="subject">Complaint Subject</label>
                        <input type="text" class="form-control" name="subject" id="subject" placeholder="Enter subject" required>
                        @error('subject')
                          <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col">
                        <label for="category">Category</label>
                        <select name="category" class="form-control" id="category" required>
                            <option value="">-- Select Category --</option>
                            <option value="Workplace Issue">Workplace Issue</option>
                            <option value="Payroll/Salary">Payroll / Salary</option>
                            <option value="Leave/Attendance">Leave / Attendance</option>
                            <option value="Harassment">Harassment</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('category')
                          <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="complaint">Complaint Details</label>
                    <textarea class="form-control" id="complaint" name="complaint" rows="5" placeholder="Describe your issue in detail..." required></textarea>
                    @error('complaint')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Submit Complaint</button>
            </form>

            <hr class="dashed" style="border-top: 3px dashed #bbb;">

            {{-- Complaint History Table --}}
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Category</th>
                        <th scope="col">Details</th>
                        <th scope="col">Created On</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($complaints as $key => $c)
                    @php 
                      $c->created_at = new DateTime($c->created_at);
                    @endphp
                    <tr>
                        <th scope="row">{{ $key+1 }}</th>
                        <td>{{ $c->subject }}</td>
                        <td>{{ $c->category }}</td>
                        <td style="max-width: 480px;">{{ $c->complaint }}</td>
                        <td>{{ $c->created_at->format('d-m-Y') }}</td>
                        <td>
                            @if($c->status == 0)
                                <span class="text-info">Pending</span>
                            @elseif($c->status == 1)
                                <span class="text-success">Resolved</span>
                            @else
                                <span class="text-danger">Rejected</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection

@push('scripts')
@endpush
