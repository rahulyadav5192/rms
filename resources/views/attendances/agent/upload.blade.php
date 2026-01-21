@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('content')

<!-- Agent Attendance Upload Form -->
@if(in_array(auth()->user()->username, ['NIFALPHA','NIF1122045']))
<div class="row">

<div class="col-6 mt-4">
    <div class="dashboard-card">
        <h4 class="text-center text-primary mb-4">Upload CSA Attendance Data (Only For Swiggy/Blinkit)</h4>
        <p class="text-center mb-4">
            <a href="{{ asset('sample_csa - Raw.csv') }}" class="btn btn-sm btn-outline-primary" download>
                📥 Download Sample CSV
            </a>
        </p>

        <div class="upload-area" id="agentUploadArea">
            <form id="agentUploadForm" enctype="multipart/form-data">
                @csrf
                <div class="form-group text-center">
                    <label for="attendance_file" class="upload-label">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                        <p class="text-muted mt-2">Drag and drop your CSV file here or <span class="text-primary" id="agentBrowseLink">click to browse</span></p>
                        <input type="file" class="form-control-file d-none" id="agent_attendance_file" name="attendance_file" accept=".csv" autocomplete="off">
                    </label>
                    <small class="text-info">Only .csv files are allowed</small>
                </div>
                <button type="submit" class="btn btn-primary mt-3 w-100">Upload</button>
            </form>
            <div id="agentUploadProgress" class="progress mt-3" style="display: none;">
                <div id="agentProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
            <div id="agentUploadFeedback" class="mt-2" style="display: none;"></div>
            <div id="agentInsertSummary" class="mt-2" style="display: none;"></div>
            <div id="agentErrorList" class="mt-2" style="display: none;"></div>
            <div id="agentDebugInfo" class="mt-2" style="display: none;"></div>
        </div>
    </div>
</div>
@endif
@if(in_array(auth()->user()->username, ['NIFALPHA','NIF0924304','NIF0223001','NIF0525298','NIF0721007','NIF1122045','NIF1019001','NIF0123042','NIF0617001','NIF1020003','NIF1019001','NIF0913001','NIF0416001','NIF0123029','NIF1015002','NIF0123030','NIF0114001','NIF0821014']))
<!-- Non-CSA Attendance Upload Form -->
<div class="col-6 mt-4">
    <div class="dashboard-card">
        <h4 class="text-center text-primary mb-4">Upload Non-CSA Attendance Data</h4>
        <p class="text-center mb-4">
            <a href="{{ asset('sample.csv') }}" class="btn btn-sm btn-outline-primary" download>
                📥 Download Sample CSV
            </a>
        </p>

        
        <div class="upload-area" id="nonCsaUploadArea">
            <form id="nonCsaUploadForm" enctype="multipart/form-data">
                @csrf
                <div class="form-group text-center">
                    <label for="non_csa_attendance_file" class="upload-label">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                        <p class="text-muted mt-2">Drag and drop your CSV file here or <span class="text-primary" id="nonCsaBrowseLink">click to browse</span></p>
                        <input type="file" class="form-control-file d-none" id="non_csa_attendance_file" name="attendance_file" accept=".csv" autocomplete="off">
                    </label>
                    <small class="text-info">Only .csv files are allowed</small>
                </div>
                <button type="submit" class="btn btn-primary mt-3 w-100">Upload</button>
            </form>
            <div id="nonCsaUploadProgress" class="progress mt-3" style="display: none;">
                <div id="nonCsaProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
            <div id="nonCsaUploadFeedback" class="mt-2" style="display: none;"></div>
            <div id="nonCsaInsertSummary" class="mt-2" style="display: none;"></div>
            <div id="nonCsaErrorList" class="mt-2" style="display: none;"></div>
            <div id="nonCsaDebugInfo" class="mt-2" style="display: none;"></div>
        </div>
    </div>
</div>

</div>
@endif
<style>
    .dashboard-card {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        transition: all 0.3s ease;
    }

    .dashboard-card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .upload-area {
        border: 2px dashed #ced4da;
        border-radius: 10px;
        padding: 40px;
        text-align: center;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .upload-area.dragover {
        background: #e9ecef;
        border-color: #007bff;
    }

    .upload-label {
        cursor: pointer;
    }

    .upload-label:hover #browseLink, .upload-label:hover #nonCsaBrowseLink, .upload-label:hover #agentBrowseLink {
        color: #0056b3;
    }

    .progress {
        height: 20px;
    }

    .progress-bar {
        background-color: #007bff;
        transition: width 0.3s ease;
    }

    #agentUploadFeedback, #agentInsertSummary, #agentErrorList, #agentDebugInfo,
    #nonCsaUploadFeedback, #nonCsaInsertSummary, #nonCsaErrorList, #nonCsaDebugInfo {
        min-height: 20px;
    }

    .error-item {
        color: #dc3545;
        margin-bottom: 5px;
    }

    .success-item {
        color: #28a745;
        margin-bottom: 5px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Agent Attendance Upload Logic
        const agentUploadArea = $('#agentUploadArea');
        const agentFileInput = $('#agent_attendance_file');
        const agentBrowseLink = $('#agentBrowseLink');
        const agentUploadFeedback = $('#agentUploadFeedback');
        const agentUploadProgress = $('#agentUploadProgress');
        const agentProgressBar = $('#agentProgressBar');
        const agentInsertSummary = $('#agentInsertSummary');
        const agentErrorList = $('#agentErrorList');
        const agentDebugInfo = $('#agentDebugInfo');
        const agentUploadForm = $('#agentUploadForm');

        agentUploadArea.on('dragover', function (e) {
            e.preventDefault();
            agentUploadArea.addClass('dragover');
        });

        agentUploadArea.on('dragleave', function (e) {
            e.preventDefault();
            agentUploadArea.removeClass('dragover');
        });

        agentUploadArea.on('drop', function (e) {
            e.preventDefault();
            agentUploadArea.removeClass('dragover');
            agentFileInput[0].files = e.originalEvent.dataTransfer.files;
            updateAgentFeedback(`Selected file: ${agentFileInput[0].files[0].name}`);
        });

        agentBrowseLink.one('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            agentFileInput.click();
        });

        agentFileInput.on('change', function (e) {
            e.stopPropagation();
            if (agentFileInput[0].files.length > 0) {
                updateAgentFeedback(`Selected file: ${agentFileInput[0].files[0].name}`);
            }
        });

        agentUploadForm.on('submit', function (e) {
            e.preventDefault();
            if (!agentFileInput[0].files || agentFileInput[0].files.length === 0) {
                updateAgentFeedback('Please select a CSV file.', 'error');
                return;
            }

            agentUploadProgress.show();
            agentProgressBar.css('width', '0%').text('0%');
            agentUploadFeedback.text('Uploading...').show();
            agentErrorList.hide().empty();
            agentInsertSummary.hide().empty();
            agentDebugInfo.hide().empty();

            const formData = new FormData(this);

            $.ajax({
                url: '{{ route('upload.attendance') }}', // Adjust route as needed
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                xhr: function () {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            agentProgressBar.css('width', percent + '%').text(percent + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function (response) {
                    console.log('Agent Response:', response);
                    if (response.error) {
                        updateAgentFeedback(response.error, 'error');
                    } else {
                        agentProgressBar.css('width', '100%').text('100%');
                        updateAgentFeedback('Upload completed!').removeClass('text-danger').addClass('text-success');
                        if (response.inserted) {
                            agentInsertSummary.html(`<p class="success-item">Total rows inserted: ${response.inserted}</p>`).show();
                        }
                        if (response.errors && response.errors.length) {
                            let errorHtml = '<p class="error-item">Errors encountered:</p>';
                            response.errors.forEach(error => {
                                errorHtml += `<p class="error-item">Line ${error.line}: Employee ID ${error.employeeId} - ${error.message || 'Not found'}</p>`;
                            });
                            agentErrorList.html(errorHtml).show();
                        }
                        agentDebugInfo.html(`<p>Total rows processed: ${response.total}</p>`).show();
                    }
                },
                error: function (xhr) {
                    const response = xhr.responseJSON || { error: 'An unexpected error occurred.' };
                    updateAgentFeedback(response.error, 'error');
                    agentProgressBar.css('width', '0%').text('0%');
                    console.log('Agent Error Response:', response);
                },
                complete: function () {
                    agentUploadProgress.hide();
                }
            });
        });

        function updateAgentFeedback(message, type = 'info') {
            agentUploadFeedback.text(message).show();
            if (type === 'error') {
                agentUploadFeedback.addClass('text-danger').removeClass('text-success');
            } else {
                agentUploadFeedback.removeClass('text-danger').addClass('text-success');
            }
        }
        // Non-CSA Attendance Upload Logic
        const nonCsaUploadArea = $('#nonCsaUploadArea');
        const nonCsaFileInput = $('#non_csa_attendance_file');
        const nonCsaBrowseLink = $('#nonCsaBrowseLink');
        const nonCsaUploadFeedback = $('#nonCsaUploadFeedback');
        const nonCsaUploadProgress = $('#nonCsaUploadProgress');
        const nonCsaProgressBar = $('#nonCsaProgressBar');
        const nonCsaInsertSummary = $('#nonCsaInsertSummary');
        const nonCsaErrorList = $('#nonCsaErrorList');
        const nonCsaDebugInfo = $('#nonCsaDebugInfo');
        const nonCsaUploadForm = $('#nonCsaUploadForm');

        nonCsaUploadArea.on('dragover', function (e) {
            e.preventDefault();
            nonCsaUploadArea.addClass('dragover');
        });

        nonCsaUploadArea.on('dragleave', function (e) {
            e.preventDefault();
            nonCsaUploadArea.removeClass('dragover');
        });

        nonCsaUploadArea.on('drop', function (e) {
            e.preventDefault();
            nonCsaUploadArea.removeClass('dragover');
            nonCsaFileInput[0].files = e.originalEvent.dataTransfer.files;
            updateNonCsaFeedback(`Selected file: ${nonCsaFileInput[0].files[0].name}`);
        });

        nonCsaBrowseLink.one('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            nonCsaFileInput.click();
        });

        nonCsaFileInput.on('change', function (e) {
            e.stopPropagation();
            if (nonCsaFileInput[0].files.length > 0) {
                updateNonCsaFeedback(`Selected file: ${nonCsaFileInput[0].files[0].name}`);
            }
        });

        nonCsaUploadForm.on('submit', function (e) {
            e.preventDefault();
            if (!nonCsaFileInput[0].files || nonCsaFileInput[0].files.length === 0) {
                updateNonCsaFeedback('Please select a CSV file.', 'error');
                return;
            }

            nonCsaUploadProgress.show();
            nonCsaProgressBar.css('width', '0%').text('0%');
            nonCsaUploadFeedback.text('Uploading...').show();
            nonCsaErrorList.hide().empty();
            nonCsaInsertSummary.hide().empty();
            nonCsaDebugInfo.hide().empty();

            const formData = new FormData(this);

            $.ajax({
                url: '{{ route('upload.non.csa.attendance') }}', // Adjust route as needed
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                xhr: function () {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            nonCsaProgressBar.css('width', percent + '%').text(percent + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function (response) {
                    console.log('Non-CSA Response:', response);
                    if (response.error) {
                        updateNonCsaFeedback(response.error, 'error');
                    } else {
                        nonCsaProgressBar.css('width', '100%').text('100%');
                        updateNonCsaFeedback('Upload completed!').removeClass('text-danger').addClass('text-success');
                        // Always show summary counts (even if 0)
                        const inserted = (response.inserted !== undefined && response.inserted !== null) ? response.inserted : 0;
                        const updated = (response.updated !== undefined && response.updated !== null) ? response.updated : 0;
                        const total = (response.total !== undefined && response.total !== null) ? response.total : 0;

                        nonCsaInsertSummary.html(
                            `<p class="success-item">Inserted: ${inserted}</p>` +
                            `<p class="success-item">Updated: ${updated}</p>` +
                            `<p class="success-item">Rows processed: ${total}</p>`
                        ).show();

                        if (response.errors && response.errors.length) {
                            let errorHtml = '<p class="error-item">Errors encountered:</p>';
                            response.errors.forEach(error => {
                                const row = error.row || error.line || 'N/A';
                                const emp = error.employeeId || 'N/A';
                                const field = error.field ? ` (${error.field})` : '';
                                const val = (error.value !== undefined && error.value !== null) ? ` [value: ${error.value}]` : '';
                                errorHtml += `<p class="error-item">Row ${row}: Employee ID ${emp}${field} - ${error.message || 'Error'}${val}</p>`;
                            });
                            nonCsaErrorList.html(errorHtml).show();
                        }
                        nonCsaDebugInfo.hide().empty();
                    }
                },
                error: function (xhr) {
                    // Sometimes PHP warnings/deprecations are printed before JSON, so responseJSON is null.
                    // Try extracting JSON from the raw responseText.
                    let response = xhr.responseJSON;

                    if (!response && xhr.responseText) {
                        try {
                            const text = xhr.responseText;
                            const jsonStart = text.indexOf('{');
                            const jsonEnd = text.lastIndexOf('}');
                            if (jsonStart !== -1 && jsonEnd !== -1 && jsonEnd > jsonStart) {
                                response = JSON.parse(text.substring(jsonStart, jsonEnd + 1));
                            }
                        } catch (e) {
                            // ignore parsing errors
                        }
                    }

                    response = response || { error: 'An unexpected error occurred.' };

                    updateNonCsaFeedback(response.error || 'An unexpected error occurred.', 'error');
                    nonCsaProgressBar.css('width', '0%').text('0%');
                    console.log('Non-CSA Error Response:', response);

                    // Even on HTTP error (e.g., PHP warnings before JSON), backend may still return counts
                    if (response.inserted !== undefined || response.updated !== undefined || response.total !== undefined) {
                        const inserted = (response.inserted !== undefined && response.inserted !== null) ? response.inserted : 0;
                        const updated = (response.updated !== undefined && response.updated !== null) ? response.updated : 0;
                        const total = (response.total !== undefined && response.total !== null) ? response.total : 0;

                        nonCsaInsertSummary.html(
                            `<p class="success-item">Inserted: ${inserted}</p>` +
                            `<p class="success-item">Updated: ${updated}</p>` +
                            `<p class="success-item">Rows processed: ${total}</p>`
                        ).show();
                    }

                    // If backend sent row-level errors, show them even on HTTP 4xx/5xx
                    if (response.errors && response.errors.length) {
                        let errorHtml = '<p class="error-item">Errors encountered:</p>';
                        response.errors.forEach(error => {
                            const row = error.row || error.line || 'N/A';
                            const emp = error.employeeId || 'N/A';
                            const field = error.field ? ` (${error.field})` : '';
                            const val = (error.value !== undefined && error.value !== null) ? ` [value: ${error.value}]` : '';
                            errorHtml += `<p class="error-item">Row ${row}: Employee ID ${emp}${field} - ${error.message || 'Error'}${val}</p>`;
                        });
                        nonCsaErrorList.html(errorHtml).show();
                    }
                },
                complete: function () {
                    nonCsaUploadProgress.hide();
                }
            });
        });

        function updateNonCsaFeedback(message, type = 'info') {
            nonCsaUploadFeedback.text(message).show();
            if (type === 'error') {
                nonCsaUploadFeedback.addClass('text-danger').removeClass('text-success');
            } else {
                nonCsaUploadFeedback.removeClass('text-danger').addClass('text-success');
            }
        }
    });
</script>
@endsection