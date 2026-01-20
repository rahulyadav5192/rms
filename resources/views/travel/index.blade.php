<!-- resources/views/travel-allowance.blade.php -->
@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@section('filter-section')
@php
$editEmployeePermission = user()->permission('edit_employees');
@endphp

<style>
    :root {
        --primary: #2c3e50;
        --secondary: #3498db;
        --accent: #e74c3c;
        --background: #f5f6fa;
        --card-bg: #ffffff;
    }

    body {
        font-family: 'Roboto', sans-serif;
        background: var(--background);
    }

    .container.card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin: 40px auto;
        max-width: 1200px;
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h3 {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .card-header::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(30deg);
    }

    .card-body {
        padding: 2rem;
    }

    .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        background: var(--primary);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1.5rem;
    }

    .modal-title {
        font-family: 'Playfair Display', serif;
    }

    .modal-body {
        padding: 2rem;
    }

    .form-group label {
        font-weight: 500;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
    }

    .btn-primary {
        background: var(--secondary);
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 25px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: var(--primary);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: #95a5a6;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 25px;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #7f8c8d;
        transform: translateY(-2px);
    }

    .table-responsive {
        margin-top: 2rem;
    }

    .table {
        background: var(--card-bg);
        border-radius: 10px;
        overflow: hidden;
    }

    .table thead {
        background: var(--primary);
        color: white;
    }

    .table th {
        font-family: 'Playfair Display', serif;
        padding: 1rem;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background: rgba(0, 0, 0, 0.02);
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        margin-right: 0.5rem;
    }
    
    /* Add to existing style block */
.btn-primary .spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
    margin-right: 8px;
    vertical-align: middle;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.btn-primary:disabled {
    opacity: 0.8;
    cursor: not-allowed;
}
</style>

<div class="container card pt-3">
    <div class="card-header">
        <h3>Travel Dashboard</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#travelModal">Add New</button>
    </div>
    
    <div class="card-body">
        <!-- Travel Allowance Table -->
        <div class="table-responsive">
            <table class="table table-striped" id="travelTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trip Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Destination</th>
                        <th>Approx Budget</th>
                        <th>Transport</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($travels as $travel)
                        <tr>
                            <td>{{ $travel->id }}</td>
                            <td>${travel.trip_type == 2 ? 'Two Way' : 'One Way'}</td>
                            <td>{{ $travel->start_date }}</td>
                            <td>{{ $travel->end_date }}</td>
                            <td>{{ $travel->destination }}</td>
                            <td>₹{{ number_format($travel->expenses, 2) }}</td>
                            <td>{{ ucfirst($travel->transport_mode) }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editTravel({{ $travel->id }})">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteTravel({{ $travel->id }})">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="travelModal" tabindex="-1" aria-labelledby="travelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="travelModalLabel">Add Travel Allowance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="travelForm" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="travelId">
            
                    <div class="form-group mb-3">
                        <label for="trip_type">Trip Type</label>
                        <select class="form-control" id="trip_type" name="trip_type" required>
                            <option value="">Select Trip Type</option>
                            <option value="1">One Way</option>
                            <option value="2">Two Way</option>
                        </select>
                    </div>

            
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6 form-group mb-3" id="endDateGroup" style="display: none;">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                    </div>
            
                    <div class="form-group mb-3">
                        <label for="destination">Travel Destination</label>
                        <input type="text" class="form-control" id="destination" name="destination" required>
                    </div>
            
                    <div class="form-group mb-3">
                        <label for="work_summary">Work Summary</label>
                        <textarea class="form-control" id="work_summary" name="work_summary" rows="3" required></textarea>
                    </div>
            
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="amount">Approx Budget (₹)</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="transport_mode">Transport Mode</label>
                            <select class="form-control" id="transport_mode" name="transport_mode" required>
                                <option value="">Select Transport</option>
                                <option value="flight">Flight</option>
                                <option value="train">Train</option>
                                <option value="bus">Bus</option>
                                <option value="car">Car</option>
                            </select>
                        </div>
                    </div>
            
                    <div class="form-group mb-3">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
            
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                        <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tripTypeSelect = document.getElementById('trip_type');
        const endDateGroup = document.getElementById('endDateGroup');
        const endDateInput = document.getElementById('end_date');

        tripTypeSelect.addEventListener('change', function () {
            if (this.value === '2') {
                endDateGroup.style.display = 'block';
                endDateInput.setAttribute('required', true);
            } else {
                endDateGroup.style.display = 'none';
                endDateInput.removeAttribute('required');
                endDateInput.value = '';
            }
        });
    });
</script>


<script>
    let travels = @json($travels);

    // Render table function
    function renderTable() {
        const tbody = document.querySelector('#travelTable tbody');
        if (!tbody) {
            console.error('Table body not found');
            return;
        }
        tbody.innerHTML = '';
        let serialNumber = 1; // Start serial number from 1
        travels.forEach(travel => {
            tbody.innerHTML += `
                <tr>
                    <td>${serialNumber}</td> <!-- Display serial number instead of travel.id -->
                    <td>${travel.trip_type == 1 ? 'Onw Way' : 'Two Way'}</td>
                    <td>${travel.start_date}</td>
                    <td>${travel.end_date}</td>
                    <td>${travel.destination}</td>
                    <td>₹${Number(travel.expenses).toFixed(2)}</td>
                    <td>${travel.transport_mode.charAt(0).toUpperCase() + travel.transport_mode.slice(1)}</td>
                    <td>
                        ${travel.approve_status == 2 ? '<span class="badge badge-warning">Pending</span>' 
                         : travel.approve_status == 1 ? '<span class="badge badge-danger">Rejected</span>' 
                         : '<span class="badge badge-success">Approved</span>'}
                    </td>
                    <td>
                        ${travel.approve_status == 2 ? `
                            <button class="btn btn-sm btn-primary" onclick="editTravel(${travel.id})">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteTravel(${travel.id})">Delete</button>
                        ` : travel.approve_status == 0 ? `
                            <a href="/travel/${travel.id}/details" class="btn btn-sm btn-info">View Details</a>
                        ` : ''}
                    </td>


                </tr>
            `;
            serialNumber++; // Increment for next row
        });
    }

    // Reset form and close modal
    function resetForm() {
        const form = document.getElementById('travelForm');
        const modalElement = document.getElementById('travelModal');
        form.reset();
        document.getElementById('travelId').value = '';
        document.getElementById('submitBtn').textContent = 'Save';
        document.getElementById('travelModalLabel').textContent = 'Add Travel Allowance';
        
        if (typeof bootstrap !== 'undefined' && modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.hide();
        } else {
            console.error('Bootstrap not loaded or modal element not found');
        }
    }

    // Edit travel function
    function editTravel(id) {
        fetch(`/travel-allowance/${id}/edit`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('trip_type').value = data.data.trip_type;
                document.getElementById('travelId').value = data.data.id;
                document.getElementById('start_date').value = data.data.start_date;
                document.getElementById('end_date').value = data.data.end_date;
                document.getElementById('destination').value = data.data.destination;
                document.getElementById('work_summary').value = data.data.work_summary;
                document.getElementById('amount').value = data.data.expenses;
                document.getElementById('transport_mode').value = data.data.transport_mode;
                document.getElementById('notes').value = data.data.notes;
                document.getElementById('submitBtn').textContent = 'Update';
                document.getElementById('travelModalLabel').textContent = 'Edit Travel Allowance';
                if (typeof bootstrap !== 'undefined') {
                    new bootstrap.Modal(document.getElementById('travelModal')).show();
                } else {
                    console.error('Bootstrap not loaded');
                }
            }
            const tripTypeSelect = document.getElementById('trip_type');
            tripTypeSelect.value = data.data.trip_type;
            tripTypeSelect.dispatchEvent(new Event('change'));

        })
        .catch(error => console.error('Error:', error));
    }

    // Delete travel function
    function deleteTravel(id) {
        if (confirm('Are you sure you want to delete this travel allowance?')) {
            fetch(`/travel-allowance/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    // Form submission handler with loading state
    document.getElementById('travelForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Get the submit button
        const submitBtn = document.getElementById('submitBtn');
        
        // Store original text
        const originalText = submitBtn.textContent;
        
        // Change button to loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="spinner"></span>
            <span>Loading...</span>
        `;
        
        const formData = new FormData(this);
        const id = document.getElementById('travelId').value;
        const url = id ? `/travel-allowance/${id}` : '/travel-allowance';
        const method = id ? 'PUT' : 'POST';
    
        // console.log([...formData.entries()]);

        // console.log(id);
        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            // Reset button state regardless of success or failure
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;

            if (data.success) {
                if (id) {
                    const index = travels.findIndex(t => t.id == id);
                    travels[index] = data.data;
                } else {
                    travels.push(data.data);
                }
                renderTable();
                resetForm(); // This will now close the modal
                alert(data.message);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            // Reset button on error
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            console.error('Error:', error);
        });
    });

    // Initial table render
    document.addEventListener('DOMContentLoaded', function() {
        renderTable();
    });
</script>
@endpush

@endsection