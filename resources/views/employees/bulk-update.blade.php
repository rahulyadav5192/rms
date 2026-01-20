@extends('layouts.app')

@section('content')
<style>
    .card {
    background: #ffffff;
}

.form-control:focus,
.form-select:focus {
    box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
    border-color: #86b7fe;
}

textarea.form-control {
    resize: none;
}

.alert {
    font-size: 0.95rem;
}
.modern-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;

    background-color: #fff;
    border: 1.5px solid #d1d5db;
    border-radius: 12px;
    padding: 12px 44px 12px 14px;

    font-size: 15px;
    font-weight: 500;
    color: #111827;

    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' viewBox='0 0 24 24'%3E%3Cpath stroke='%236b7280' stroke-width='2' d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 14px;

    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.modern-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    outline: none;
}

.modern-select option {
    font-weight: 500;
}

</style>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            {{-- Page Title --}}
            <div class="mb-4">
                <h3 class="fw-bold mb-1">Bulk Update Employees</h3>
                <p class="text-muted mb-0">
                    Update branch, department, or designation for multiple employees at once.
                </p>
            </div>

            {{-- Card --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 p-lg-5">

                    {{-- Alerts --}}
                    @if(session('success'))
                        <div class="alert alert-success rounded-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger rounded-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ url('employees/bulk-update') }}">
                        @csrf

                        {{-- Employee IDs --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Employee IDs
                            </label>
                            <textarea
                                name="employee_ids"
                                rows="3"
                                class="form-control form-control-lg rounded-3"
                                placeholder="NIF0126087, NIF0126088, NIF0126089"
                                required
                            >{{ old('employee_ids', $employeeIds ?? '') }}</textarea>
                            <small class="text-muted">
                                Paste comma-separated employee IDs.
                            </small>
                        </div>

                        <div class="row g-4">
                            {{-- Branch --}}
                            <div class="col-6">
                                <label class="form-label fw-semibold">Branch</label>
                                <select name="branch_id" class="form-select modern-select" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Department --}}
                            <div class="col-6">
                                <label class="form-label fw-semibold">Department</label>
                                <select name="department_id" class="form-select modern-select" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">
                                            {{ $department->team_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Designation --}}
                            <div class="col-12 mt-5">
                                <label class="form-label fw-semibold">Designation</label>
                                <select name="designation_id" class="form-select modern-select" required>
                                    <option value="">Select Designation</option>
                                    @foreach($designations as $designation)
                                        <option value="{{ $designation->id }}">
                                            {{ $designation->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Divider --}}
                        <hr class="my-4">

                        {{-- Actions --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('employees.index') }}" class="btn btn-light">
                                ← Back
                            </a>

                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                Update Employees
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
