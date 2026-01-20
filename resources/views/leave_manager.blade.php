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
    </style>
    <form action="{{ url('account/employees') }}" id="myForm" method="GET">
        <x-filters.filter-box>
        </x-filters.filter-box>
    </form>
@endsection

@section('content')

<style>
    a {
        text-decoration: none;
    }

    .accept, .deny {
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        transition: 0.3s ease;
        display: inline-block;
    }

    .accept {
        color: #fff;
        background: #44cc44;
        box-shadow: 0 4px 0 #2ea62e;
    }

    .accept:hover {
        background: #6fe76f;
        box-shadow: 0 4px 0 #7ed37e;
    }

    .deny {
        color: #fff;
        background: tomato;
        box-shadow: 0 4px 0 #cb4949;
    }

    .deny:hover {
        background: rgb(255, 147, 128);
        box-shadow: 0 4px 0 #ef8282;
    }

    .leave-table th {
        background-color: #f8f9fa;
        font-weight: 700;
        text-align: center;
        vertical-align: middle;
    }

    .leave-table td {
        vertical-align: middle;
    }

    .leave-container {
        background: #fff;
        padding: 30px;
        margin: 40px auto;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        max-width: 1100px;
    }

    @media screen and (max-width: 768px) {
        .accept, .deny {
            padding: 8px 12px;
            font-size: 14px;
        }

        .leave-container {
            padding: 20px;
        }
    }
</style>

<div class="leave-container">
    <h3 class="mb-4 text-center">Employee Leave Requests</h3>
    <div class="table-responsive">
        <table class="table table-bordered leave-table">
            <thead>
                <tr>
                    <th>S. No</th>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Leave Date</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leave as $key => $l)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $l->employee_id }}</td>
                        <td>{{ $l->user_name }}</td>
                        <td>{{ $l->leave_date }}</td>
                        <td style="white-space: pre-wrap; word-break: break-word;">{{ $l->reason }}</td>
                        <td class="text-center">
                            @if($l->manager_status == 1)
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ url('/manger_approve_leave') }}/{{ $l->leave_id }}" class="accept">✅ Accept</a>
                                    <a href="{{ url('/manger_reject_leave') }}/{{ $l->leave_id }}" class="deny">❌ Deny</a>
                                </div>
                            @elseif($l->manager_status == 0)
                                <span class="text-success fw-bold">✅ Accepted</span>
                            @else
                                <span class="text-danger fw-bold">❌ Rejected</span>
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
