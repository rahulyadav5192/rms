@extends('layouts.app')

@section('content')
<div class="m-3">
    <!-- Success and Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h3 class="mb-4">Edit Tracker</h3>
            
            <!-- Form to edit tracker details -->
            <form method="POST" action="{{ url('/account/skills/hr') }}/{{$tracker->ptid}}" enctype="multipart/form-data" class="card p-4 shadow-sm rounded-3">
                @csrf

                <!-- Notes Field -->
                <div class="row mb-2">
                <!-- Notes Field -->
                <div class="col-12">
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-bold">Notes</label>
                        <textarea class="form-control shadow-sm rounded-3" id="notes" name="notes" rows="3" required>{{ old('notes', $tracker->notes) }}</textarea>
                    </div>
                </div>
            
                <!-- Status and File Upload Fields in One Row -->
                <div class="col-lg-6 col-md-6">
                    <!-- Status Field (Styled like Development Type) -->
                    <x-forms.select fieldId="status" :fieldLabel="__('Status')" fieldName="status" fieldRequired="true">
                        <option value="0" {{ $tracker->hr_status == 0 ? 'selected' : '' }}>Uploaded</option>
                        <option value="1" {{ $tracker->hr_status == 1 ? 'selected' : '' }}>Approved</option>
                        <option value="2" {{ $tracker->hr_status == 2 ? 'selected' : '' }}>Rejected</option>
                        <option value="3" {{ $tracker->hr_status == 3 ? 'selected' : '' }}>Need Improvement</option>
                    </x-forms.select>
                </div>
            
                <div class="col-lg-6 col-md-6">
                    <!-- File Upload Field -->
                    <!--<label for="file" class="form-label fw-bold">Upload File (Optional)</label>-->
                    <!--<input type="file" class="form-control shadow-sm rounded-3" id="file" name="file">-->
            
                    <!-- Display current file if it exists -->
                    @if(count($files) > 0 && $files[0]->file_name)
                        <small class="form-text text-muted mt-2">
                            Current file: 
                            <a href="{{ asset('uploads/' . $files[0]->file_name) }}" target="_blank" class="text-decoration-none text-primary">
                                {{ $files[0]->file_name }}
                            </a>
                        </small>
                    @else
                        <small class="form-text text-muted">No file uploaded.</small>
                    @endif
                </div>
            </div>


                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary shadow-sm rounded-3">Update Tracker</button>
            </form>

            <!-- Section to Display All Uploaded Files by Employee -->
            <div class="mt-5">
                <h4 class="mb-4">Uploaded Files</h4>
                @if(count($files) > 0)
                    <div class="table-responsive shadow-sm rounded-3">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Notes</th>
                                    <th>File Name</th>
                                    <th>Uploaded At</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($files as $index => $file)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $file->notes }}</td>
                                        <td>{{ $file->file_name ? $file->file_name : 'No File Uploaded' }}</td>
                                        <td>@if($file->created_at instanceof \Carbon\Carbon)
                                                {{ $file->created_at->format('d M Y, h:i A') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($file->created_at)->format('d M Y, h:i A') }}
                                            @endif</td>
                                        <td class="text-center">
                                            @if($file->file_name)
                                                <a href="{{ asset('uploads/' . $file->file_name) }}" 
                                                   class="btn btn-outline-info btn-sm d-inline-flex align-items-center justify-content-center me-2" 
                                                   title="View" target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ asset('uploads/' . $file->file_name) }}" 
                                                   class="btn btn-outline-primary btn-sm d-inline-flex align-items-center justify-content-center" 
                                                   title="Download" download>
                                                    <i class="bi bi-download"></i>
                                                </a>
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
                    <p class="text-muted">No files uploaded yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
