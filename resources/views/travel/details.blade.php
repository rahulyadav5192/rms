@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="text-center mb-4">Travel Details & Invoice Upload</h3>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Travel Status Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-lg p-3">
                <h5 class="card-title">Manager Approval (Travel Request)</h5>
                <p>Status: 
                    @switch($travel->manager_status)
                        @case(0) <span class="badge badge-success">Approved</span> @break
                        @case(1) <span class="badge badge-warning">Pending</span> @break
                        @case(2) <span class="badge badge-danger">Rejected</span> @break
                        @case(3) <span class="badge badge-info">Need Changes</span> @break
                        @default <span class="badge badge-secondary">Unknown</span>
                    @endswitch
                </p>
                <p><strong>Manager Remark:</strong> {{ $travel->manager_remark ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-lg p-3">
                <h5 class="card-title">Accounts Team Approval (Travel Request)</h5>
                <p>Status: 
                    @switch($travel->approve_status)
                        @case(0) <span class="badge badge-success">Approved</span> @break
                        @case(1) <span class="badge badge-warning">Pending</span> @break
                        @case(2) <span class="badge badge-danger">Rejected</span> @break
                        @case(3) <span class="badge badge-info">Need Changes</span> @break
                        @default <span class="badge badge-secondary">Unknown</span>
                    @endswitch
                </p>
                <p><strong>Accounts Remark:</strong> {{ $travel->account_remark ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Invoice Approval Status Section -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-lg p-3">
                <h5 class="card-title">Manager Approval (All Invoices)</h5>
                <p>Status: 
                    @switch($travel->invoice_approve_manager)
                        @case(0) <span class="badge badge-success">Approved</span> @break
                        @case(1) <span class="badge badge-warning">Pending</span> @break
                        @case(2) <span class="badge badge-danger">Rejected</span> @break
                        @case(3) <span class="badge badge-info">Need Changes</span> @break
                        @default <span class="badge badge-secondary">Unknown</span>
                    @endswitch
                </p>
                <p><strong>Manager Remark:</strong> {{ $travel->i_m_remark ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-lg p-3">
                <h5 class="card-title">Accounts Team Approval (All Invoices)</h5>
                <p>Status: 
                    @switch($travel->invoice_acc_appr)
                        @case(0) <span class="badge badge-success">Approved</span> @break
                        @case(1) <span class="badge badge-warning">Pending</span> @break
                        @case(2) <span class="badge badge-danger">Rejected</span> @break
                        @case(3) <span class="badge badge-info">Need Changes</span> @break
                        @default <span class="badge badge-secondary">Unknown</span>
                    @endswitch
                </p>
                <p><strong>Accounts Remark:</strong> {{ $travel->i_ac_remark ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Invoice Upload Form -->
    <!-- Invoice Upload Form -->
    <div class="card shadow-lg p-3 mt-4">
        <h5 class="card-title">Upload Invoices</h5>
        <form action="{{ route('employee.uploadInvoices', $travel->id) }}" method="POST" enctype="multipart/form-data" id="invoiceForm">
            @csrf
            <div id="invoiceFields">
                <div class="invoice-entry mb-3 p-3 border rounded">
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="expense_type_0">Expense Type</label>
                            <select name="expense_type[]" class="form-control @error('expense_type.*') is-invalid @enderror" id="expense_type_0" required>
                                <option value="">Select Type</option>
                                <option value="local_travel">Local Travel</option>
                                <option value="hotel">Hotel</option>
                                <option value="food">Food</option>
                                <option value="client_engagement">Client Engagement</option>
                                <option value="miscellaneous">Miscellaneous</option>
                            </select>
                            @error('expense_type.*')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="narration_0">Narration</label>
                            <input type="text" name="narration[]" class="form-control @error('narration.*') is-invalid @enderror" id="narration_0" required>
                            @error('narration.*')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="amount_0">Exact Amount</label>
                            <input type="number" name="amount[]" step="0.01" class="form-control @error('amount.*') is-invalid @enderror" id="amount_0" required>
                            @error('amount.*')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="invoice_files_0">Upload File</label>
                            <input type="file" name="invoice_files[]" class="form-control @error('invoice_files.*') is-invalid @enderror" id="invoice_files_0" required>
                            @error('invoice_files.*')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-entry mt-2" style="display: none;">Remove</button>
                </div>
            </div>
            <button type="button" class="btn btn-primary mt-2" id="addMore">Add More</button>
            <button type="submit" class="btn btn-success mt-2">Upload</button>
        </form>
    </div>
    
    <script>
    let entryCount = 0;

    document.getElementById('addMore').addEventListener('click', function() {
        entryCount++;
        const template = document.querySelector('.invoice-entry');
        const clone = template.cloneNode(true);
        
        // Update IDs and names
        clone.querySelectorAll('select, input').forEach(input => {
            const oldId = input.id;
            input.id = oldId.replace('_0', `_${entryCount}`);
            input.name = input.name.replace('[]', `[${entryCount}]`);
            input.value = ''; // Clear values in cloned fields
            input.classList.remove('is-invalid'); // Clear validation errors
        });
        
        // Show remove button for additional entries
        const removeBtn = clone.querySelector('.remove-entry');
        removeBtn.style.display = 'block';
        removeBtn.addEventListener('click', function() {
            clone.remove();
        });

        document.getElementById('invoiceFields').appendChild(clone);
    });
</script>

    <!-- Uploaded Invoices Section -->
    <div class="card shadow-lg p-3 mt-4">
        <h5 class="card-title">Uploaded Invoices</h5>
        @if($invoices->isEmpty())
            <p class="text-muted">No invoices uploaded yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Narration</th>
                            <th>File</th>
                            <th>Upload Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->expense_type }}</td>
                                <td>{{ $invoice->amount }}</td>
                                <td>{{ $invoice->description }}</td>
                                <td>
                                    <a href="{{ asset('uploads/invoices/' . $invoice->file_path) }}" target="_blank">
                                        View File
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y') }}</td>
                                <td>
                                    <form action="{{ route('employee.deleteInvoice', [$travel->id, $invoice->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection