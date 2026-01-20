@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
        <div class="card-header bg-gradient-success text-white text-center py-4">
            <h3 class="mb-0 text-dark">Travel Request Details</h3>
            <p class="text-dark mb-0">Employee: {{ $travel->employee_name }}</p>
        </div>
        <div class="card-body p-4">
            <!-- Travel Request Details -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Travel Request Approval</h5>
                            <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($travel->start_date)->format('d M Y') }}</p>
                            <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($travel->end_date)->format('d M Y') }}</p>
                            <p><strong>Destination:</strong> {{ $travel->destination }}</p>
                            <p><strong>Amount:</strong> ₹{{ number_format($travel->expenses, 2) }}</p>
                            <p><strong>Transport Mode:</strong> {{ ucfirst($travel->transport_mode) }}</p>
                            <p><strong>Status:</strong>
                                @switch($travel->manager_status)
                                    @case(0) <span class="badge badge-success">Approved</span> @break
                                    @case(1) <span class="badge badge-warning">Pending</span> @break
                                    @case(2) <span class="badge badge-danger">Rejected</span> @break
                                    @case(3) <span class="badge badge-info">Need Changes</span> @break
                                    @default <span class="badge badge-secondary">Unknown</span>
                                @endswitch
                            </p>
                            <p><strong>Remark:</strong> {{ $travel->manager_remark ?? 'N/A' }}</p>
                            <button class="btn btn-outline-primary btn-sm editRequest" 
                                    data-id="{{ $travel->id }}" 
                                    data-status="{{ $travel->manager_status }}" 
                                    data-remark="{{ $travel->manager_remark }}">
                                Edit Status
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Invoice Approval</h5>
                            <p><strong>Status:</strong>
                                @switch($travel->invoice_approve_manager)
                                    @case(0) <span class="badge badge-success">Approved</span> @break
                                    @case(1) <span class="badge badge-warning">Pending</span> @break
                                    @case(2) <span class="badge badge-danger">Rejected</span> @break
                                    @case(3) <span class="badge badge Biologically <span class="badge badge-info">Need Changes</span> @break
                                    @default <span class="badge badge-secondary">Unknown</span>
                                @endswitch
                            </p>
                            <p><strong>Remark:</strong> {{ $travel->i_m_remark ?? 'N/A' }}</p>
                            <button class="btn btn-outline-success btn-sm editInvoiceStatus" 
                                    data-id="{{ $travel->id }}" 
                                    data-status="{{ $travel->invoice_approve_manager }}" 
                                    data-remark="{{ $travel->i_m_remark }}">
                                Edit Invoice Status
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices List -->
            <h5 class="mt-4">Uploaded Invoices</h5>
            @if($invoices->isEmpty())
                <p class="text-muted">No invoices uploaded yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Preview</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Narration</th>
                                <th>File</th>
                                <th>Upload Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                            <tr>
                                <td>
                                    <div style="width: 50px; height: 50px; overflow: hidden; border: 1px solid #ddd; border-radius: 4px;">
                                        @if (in_array(strtolower(pathinfo($invoice->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                            <img src="{{ asset('uploads/invoices/' . $invoice->file_path) }}" 
                                                 alt="Preview" 
                                                 style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('images/pdf-placeholder.png') }}" 
                                                 alt="PDF" 
                                                 style="width: 100%; height: 100%; object-fit: contain; padding: 5px;">
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $invoice->expense_type }}</td>
                                <td>{{ $invoice->amount }}</td>
                                <td>{{ $invoice->description }}</td>
                                <td>
                                    <a href="{{ asset('uploads/invoices/' . $invoice->file_path) }}" target="_blank" class="btn btn-link btn-sm">View</a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Travel Request Modal -->
<div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Update Travel Request Status</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="requestId">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="managerStatus">
                        <option value="1">Pending</option>
                        <option value="0">Approve</option>
                        <option value="2">Reject</option>
                        <option value="3">Need Changes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Remark</label>
                    <input type="text" class="form-control" id="managerRemark">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateRequestStatus">Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Status Modal -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Update Invoice Approval Status</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="invoiceTravelId">
                <div class="form-group">
                    <label>Invoice Status</label>
                    <select class="form-control" id="invoiceManagerStatus">
                        <option value="1">Pending</option>
                        <option value="0">Approve</option>
                        <option value="2">Reject</option>
                        <option value="3">Need Changes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Remark</label>
                    <input type="text" class="form-control" id="invoiceManagerRemark">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="updateInvoiceStatus">Update</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Travel Request Modal
    document.querySelectorAll('.editRequest').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const status = this.getAttribute('data-status');
            const remark = this.getAttribute('data-remark');

            document.getElementById('requestId').value = id;
            document.getElementById('managerStatus').value = status;
            document.getElementById('managerRemark').value = remark || '';

            $('#editRequestModal').modal('show');
        });
    });

    document.getElementById('updateRequestStatus').addEventListener('click', function() {
        const id = document.getElementById('requestId').value;
        const status = document.getElementById('managerStatus').value;
        const remark = document.getElementById('managerRemark').value;

        if (!confirm("Are you sure you want to update this travel request?")) return;

        fetch(`/manager/travel-requests/${id}/update-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ manager_status: status, manager_remark: remark })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert("Error updating status.");
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Invoice Status Modal
    document.querySelectorAll('.editInvoiceStatus').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const status = this.getAttribute('data-status');
            const remark = this.getAttribute('data-remark');

            document.getElementById('invoiceTravelId').value = id;
            document.getElementById('invoiceManagerStatus').value = status;
            document.getElementById('invoiceManagerRemark').value = remark || '';

            $('#editInvoiceModal').modal('show');
        });
    });

    document.getElementById('updateInvoiceStatus').addEventListener('click', function() {
        const id = document.getElementById('invoiceTravelId').value;
        const status = document.getElementById('invoiceManagerStatus').value;
        const remark = document.getElementById('invoiceManagerRemark').value;

        if (!confirm("Are you sure you want to update the invoice approval status?")) return;

        fetch(`/manager/travel-requests/${id}/update-invoice-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ invoice_approve_manager: status, i_m_remark: remark })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert("Error updating invoice status.");
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>
@endsection