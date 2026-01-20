@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')

    <style>
        .search-status {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .search-status .btn-primary.rounded.f-14.p-2.mr-3 {
            margin-left: 20px;
        }
    </style>
    <form action="{{ url('account/employees') }}" id="myForm" method="GET">
        <x-filters.filter-box></x-filters.filter-box>
    </form>
@endsection

@section('content')
    <style>
        /* Base styles and resets */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f5f7fa;
            color: #2d3748;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Button styles */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .btn-accept {
            background: linear-gradient(135deg, #48bb78, #68d391);
            color: #fff;
        }

        .btn-accept:hover {
            background: linear-gradient(135deg, #68d391, #9ae6b4);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .btn-deny {
            background: linear-gradient(135deg, #f56565, #fc8181);
            color: #fff;
        }

        .btn-deny:hover {
            background: linear-gradient(135deg, #fc8181, #feb2b2);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        /* Status labels */
        .status-accepted {
            background: #e6fffa;
            color: #319795;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .status-rejected {
            background: #fff5f5;
            color: #e53e3e;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .status-pending {
            background: #fefcbf;
            color: #d97706;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        /* Leave container */
        .leave-container {
            background: #ffffff;
            padding: 40px;
            margin: 40px auto;
            max-width: 1200px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: box-shadow 0.3s ease;
        }

        .leave-container:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }

        .leave-container h3 {
            font-size: 26px;
            font-weight: 700;
            color: #1a202c;
            text-align: center;
            margin-bottom: 32px;
        }

        /* Table styles */
        .leave-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
        }

        .leave-table th {
            background: linear-gradient(135deg, #edf2f7, #e2e8f0);
            font-weight: 600;
            text-align: left;
            padding: 16px 24px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #4a5568;
        }

        .leave-table td {
            padding: 16px 24px;
            border-bottom: 1px solid #edf2f7;
            font-size: 14px;
            color: #2d3748;
            vertical-align: middle;
        }

        .leave-table tr:last-child td {
            border-bottom: none;
        }

        .leave-table tr:hover {
            background-color: #f7fafc;
            transition: background-color 0.2s ease;
        }

        .leave-table th:last-child,
        .leave-table td:last-child {
            text-align: center;
        }

        /* Responsive design */
        @media screen and (max-width: 768px) {
            .leave-container {
                padding: 24px;
                margin: 20px;
            }

            .leave-table th,
            .leave-table td {
                padding: 12px 16px;
                font-size: 13px;
            }

            .btn {
                padding: 8px 16px;
                font-size: 13px;
            }

            .leave-container h3 {
                font-size: 22px;
                margin-bottom: 24px;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        @media screen and (max-width: 576px) {
            .btn {
                padding: 6px 12px;
                font-size: 12px;
            }

            .status-accepted,
            .status-rejected,
            .status-pending {
                padding: 4px 12px;
                font-size: 12px;
            }
        }
    </style>

    <div class="leave-container">
        <h3>Employee Leave Requests</h3>
        <div class="table-responsive">
            <table class="table leave-table">
                <thead>
                    <tr>
                        <th>S. No</th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Leave Date</th>
                        <th>Reason</th>
                        <th>HR Status</th>
                        <th>Reject Reason</th>
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
                                @if($l->status == 'pending')
                                    <span class="status-pending">⏳ Pending</span>
                                @elseif($l->status == 'approved')
                                    <span class="status-accepted">✅ Approved</span>
                                @elseif($l->status == 'rejected')
                                    <span class="status-rejected">❌ Rejected</span>
                                @else
                                    <span class="status-pending">⏳ Unknown</span>
                                @endif
                            </td>
                            <td>
                                {{ Str::limit($l->reject_reason, 10) }}
                                @if(strlen($l->reject_reason) > 10)
                                    <a href="javascript:void(0)" 
                                       class="text-primary view-reason" 
                                       data-reason="{{ $l->reject_reason }}">
                                       View
                                    </a>
                                @endif
                            </td>


                            <td class="text-center">
                                @if($l->manager_status == 1)
                                    <div class="d-flex justify-content-center gap-3">
                                        <a href="{{ url('/manger_approve_leave') }}/{{ $l->leave_id }}" class="btn btn-accept">✅ Accept</a>
                                        <a href="{{ url('/manger_reject_leave') }}/{{ $l->leave_id }}" class="btn btn-deny">❌ Deny</a>
                                    </div>
                                @elseif($l->manager_status == 0)
                                    <span class="status-accepted">✅ Accepted</span>
                                @else
                                    <span class="status-rejected">❌ Rejected</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.view-reason').forEach(function (link) {
        link.addEventListener('click', function () {
            const reason = this.getAttribute('data-reason') || 'No reason provided';
            alert(reason);
        });
    });
});
</script>


@endsection

@push('scripts')
    <!-- Add any JavaScript if needed -->
@endpush