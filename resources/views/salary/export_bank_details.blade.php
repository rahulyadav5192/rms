@extends('layouts.app')


@section('filter-section')

@endsection

@section('content')


<!-- Filter Form with 3D Card Style (Compact) -->
<form action="{{ route('exportBankDetails') }}" id="filterForm" method="GET">
    <div class="card custom-card">
        <h4 class="card-title mb-3 text-center">Filter Bank Details</h4>
        <div class="row">
            <!-- Branch Filter -->
            <div class="col-md-3 mb-2">
                <label class="form-label" for="branch">Branch</label>
                <select class="form-select custom-select" name="branch" id="branch">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                            {{ ucfirst($branch->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Department Filter -->
            <div class="col-md-3 mb-2">
                <label class="form-label" for="department">Department</label>
                <select class="form-select custom-select" name="department" id="department">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                            {{ ucfirst($department->team_name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Designation Filter -->
            <div class="col-md-3 mb-2">
                <label class="form-label" for="designation">Designation</label>
                <select class="form-select custom-select" name="designation" id="designation">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($designations as $designation)
                        <option value="{{ $designation->id }}" {{ request('designation') == $designation->id ? 'selected' : '' }}>
                            {{ ucfirst($designation->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3 mb-2">
                <label class="form-label" for="status">Employee Status</label>
                <select class="form-select custom-select" name="status" id="status">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="deactive" {{ request('status') == 'deactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>


            <!-- Search -->
            <div class="col-md-3 mb-2">
                <label class="form-label" for="search">Search</label>
                <input type="text" class="form-control custom-input" name="search" value="{{ request('search') }}" id="search" placeholder="Name or ID">
            </div>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <!-- Submit and Export Buttons -->
            <button type="submit" class="btn btn-primary custom-btn">Filter</button>
            <button type="submit" name="export" value="csv" class="btn btn-secondary custom-btn">Export CSV</button>
        </div>
    </div>
</form>
<div class="card m-3" style="width: 18rem;">
  
  <div class="card-body">
    <h5 class="card-title">Total</h5>
    <p class="card-text">{{ $bankDetails->total() }}</p>
  </div>
</div>
<!-- Data Table -->
@if($bankDetails->isNotEmpty())
    <div class="table-responsive" style="    margin-top: 52px;">
        <table class="table table-bordered table-striped table-hover shadow-sm">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Bank Name</th>
                    <th>Account Holder Name</th>
                    <th>Account Number</th>
                    <th>IFSC Code</th>
                    <th>Branch</th>
                    <th>Account Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bankDetails as $detail)
                    <tr>
                        <td>{{ $detail->employee_id }}</td>
                        <td>{{ $detail->employee_name }}</td>
                        <td>{{ $detail->department_name }}</td>
                        <td>{{ $detail->designation_name }}</td>
                        <td>{{ $detail->bank_name }}</td>
                        <td>{{ $detail->acc_holder_name }}</td>
                        <td>{{ $detail->account_number }}</td>
                        <td>{{ $detail->ifsc_code }}</td>
                        <td>{{ $detail->branch_name }}</td>
                        <td>{{ $detail->account_type }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Pagination Links -->
        <div class="pagination-container d-flex justify-content-center">
            {{ $bankDetails->links() }}
        </div>
    </div>
@else
    <p class="text-muted">No records found.</p>
@endif

<!-- Custom CSS for Compact Filter Section -->
<style>
    .custom-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-top: 20px;
    }

    .custom-card h4 {
        color: #333;
        font-size: 1.25rem;
    }

    .custom-select, .custom-input {
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 8px;
        border: 1px solid #ccc;
        font-size: 0.9rem;
    }

    .custom-select:focus, .custom-input:focus {
        border-color: #5c6bc0;
        outline: none;
    }

    .custom-btn {
        padding: 8px 16px;
        border-radius: 5px;
        font-size: 0.9rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .custom-btn:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    /* Adjusting column width and margin for smaller filter form */
    .form-select, .custom-input {
        height: 38px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .custom-card {
            padding: 15px;
        }

        .col-md-3 {
            margin-bottom: 1rem;
        }

        .custom-btn {
            width: 45%;
            margin-top: 10px;
        }
    }
</style>








@endsection