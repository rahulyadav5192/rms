@extends('layouts.app')

@section('content')
<style>
    body {
    background-color: #f8f9fa;
}

.card {
    border: none;
    border-radius: 1rem;
}

.table th, .table td {
    vertical-align: middle;
}

.table th {
    text-align: center;
}

.btn-primary, .btn-info {
    transition: all 0.3s ease-in-out;
}

.btn-primary:hover, .btn-info:hover {
    transform: scale(1.05);
}

.table-responsive {
    overflow-x: auto;
    border-radius: 1rem;
    background-color: #fff;
}

.alert {
    border-radius: 0.5rem;
    font-size: 1rem;
}

</style>
<div class="container py-5">
    <!-- Success and Error Messages -->
    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-lg rounded-4 p-4">
        <h3 class="text-center mb-4">Edit Tracker</h3>
        
        <!-- Form to edit tracker details -->
        <form method="POST" action="{{ url('/account/skills/employee') }}/{{$tracker->ptid}}" enctype="multipart/form-data">
            @csrf
            <!-- Status and File Upload Fields in One Row -->
            <div class="row mb-4">
                <!-- File Upload Field -->
                <div class="col-md-6 mb-4">
                    <label for="file" class="form-label fw-bold text-secondary">Upload File</label>
                    <div class="input-group shadow-sm rounded-3">
                        <span class="input-group-text bg-light text-muted" id="file-addon">
                            <i class="bi bi-upload"></i>
                        </span>
                        <input type="file" class="form-control" id="file" name="file" aria-describedby="file-addon">
                    </div>
                    @if(count($files) > 0)
                        <small class="form-text text-muted mt-2 d-block">
                            Current file: 
                            <a href="{{ asset('uploads/' . $files[0]->file_name) }}" 
                               target="_blank" 
                               class="text-decoration-none text-primary fw-bold">
                               {{ $files[0]->file_name }}
                            </a>
                        </small>
                    @endif
                </div>

            
                <!-- Status Field -->
                <div class="col-lg-6 col-md-6">
                    <x-forms.select 
                        fieldId="status" 
                        :fieldLabel="__('Status')" 
                        fieldName="status" 
                        search="true" 
                        fieldRequired="true">
                        <option selected disabled>Choose a status...</option>
                        <option value="0" {{ $tracker->emp_status == 0 ? 'selected' : '' }}>Not Started</option>
                        <option value="1" {{ $tracker->emp_status == 1 ? 'selected' : '' }}>Working</option>
                        <option value="2" {{ $tracker->emp_status == 2 ? 'selected' : '' }}>Submitted</option>
                    </x-forms.select>
                </div>

            </div>
            
            <!-- Notes Field Below -->
            <div class="mb-4">
                <label for="notes" class="form-label fw-bold">Notes</label>
                <textarea class="form-control shadow-sm rounded-3" id="notes" name="notes" rows="3" required>{{ old('notes') }}</textarea>
            </div>


            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100 py-2 px-4 rounded-3 shadow-sm">Update Tracker</button>
        </form>
    </div>

    <!-- Section to Display All Uploaded Files by Employee -->
    <div class="card shadow-lg rounded-4 mt-5 p-4">
        <h4 class="text-center mb-4">Uploaded Files</h4>
        @if(count($files) > 0)
            <div class="table-responsive">
                <table class="table table-striped align-middle shadow-sm rounded-3">
                    <thead class="table-dark rounded-top">
                        <tr>
                            <th>#</th>
                            <th>Notes</th>
                            <th>File Name</th>
                            <th>Uploaded At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $index => $file)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $file->notes }}</td>
                                <td>{{ $file->file_name ? $file->file_name : 'No File Uploaded' }}</td>
                                <td>
                                    @if($file->created_at instanceof \Carbon\Carbon)
                                        {{ $file->created_at->format('d M Y, h:i A') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($file->created_at)->format('d M Y, h:i A') }}
                                    @endif
                                </td>
                                <td>
                                    @if($file->file_name)
                                        <a href="{{ asset('uploads/' . $file->file_name) }}" class="btn btn-sm btn-info shadow-sm" target="_blank">View</a>
                                        <a href="{{ asset('uploads/' . $file->file_name) }}" class="btn btn-sm btn-primary shadow-sm" download>Download</a>
                                    @else
                                        <span class="text-muted">No file available</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center text-muted">No files uploaded yet.</p>
        @endif
    </div>
</div>
@endsection
