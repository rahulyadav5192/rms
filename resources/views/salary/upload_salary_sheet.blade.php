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
    .form-container {
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        text-align: center;
        background-color: #f9f9f9;
    }
    .file-input {
        display: none;
    }
    .drag-drop-area {
        border: 2px dashed #aaa;
        padding: 20px;
        border-radius: 10px;
        cursor: pointer;
    }
    .upload-btn {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    #loader {
        display: none;
        margin-top: 20px;
    }
    #message {
        display: none;
        margin-top: 20px;
    }
    .error {
        color: red;
        font-size: 14px;
    }
</style>
@endsection

@section('content')
<div class="row" id="import_table">
    <div class="col-sm-12">
        <div class="add-client bg-white rounded">
            <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                @lang('app.importExcel') @lang('Salary')</h4>
            <div class="col-sm-12 pt-2">
                <div class="alert alert-warning" role="alert">
                    Only CSV File Can Be Uploaded. Unhide All Columns Before Uploading.....
                </div>
                @if(Session::has('mess'))
                <div class="alert alert-info" role="alert">
                    {{Session::get('mess')}}
                </div>
                @endif
            </div>
            <form id="" action="{{ url('account/upload_monthly_sheet') }}" method="POST" enctype="multipart/form-data" >
                {!! Form::token() !!}
                @csrf
                <div class="row py-20">
                    <div class="col-md-6">
                        <label for="import_file">@lang('File')</label>
                        <input type="file" name="import_file" id="import_file" required accept=".csv" class="form-control-file">
                        @if ($errors->has('import_file'))
                            <span class="error">{{ $errors->first('import_file') }}</span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <label for="month">@lang('app.month')</label>
                        <select id="month" name="month" required class="form-control">
                            <option value="">@lang('Select Month')</option>
                            @php
                                $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                            @endphp
                            @foreach ($months as $index => $month)
                                <option value="{{ $index + 1 }}" {{ (old('month') == ($index + 1)) ? 'selected' : '' }}>{{ $month }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('month'))
                            <span class="error">{{ $errors->first('month') }}</span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <label for="year">@lang('app.year')</label>
                        <select id="year" name="year" required class="form-control">
                            <option value="">@lang('Select Year')</option>
                            @for ($i = $year; $i >= $year - 10; $i--)
                                <option value="{{ $i }}" {{ (old('year') == $i) ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        @if ($errors->has('year'))
                            <span class="error">{{ $errors->first('year') }}</span>
                        @endif
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary m-3">@lang('Submit')</button>
                    <!--<a href="{{ route('employees.index') }}" class="btn btn-secondary">@lang('app.back')</a>-->
                </div>
            </form>
            
            <div id="uploadProgress" style="display: none;">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                </div>
                <div id="progressLabel">0%</div>
            </div>
            
            <div id="dataProcessProgress" style="display: none;">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                </div>
                <div id="dataProgressLabel">0%</div>
            </div>
        </div>
    </div>
</div>

<script>
 $(document).ready(function() {
    $('#uploadForm').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData($(this)[0]);

        var fileInput = document.getElementById('import_file');
        formData.append('import_file', fileInput.files[0]);
        formData.append('month', $('#month').val());
        formData.append('year', $('#year').val());
 
        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = (evt.loaded / evt.total) * 100;
                        $('#uploadProgress').show();
                        $('#uploadProgress .progress-bar').css('width', percentComplete + '%');
                        $('#progressLabel').text(percentComplete.toFixed(2) + '%');
                    }
                }, false);
                return xhr;
            },
            url: '{{ url('account/upload_monthly_sheet') }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
             timeout: 2400000,
            success: function(response) {
                // Handle success response
                console.log(response);
                // After file upload completes, trigger next function
                processData(response.file_id);
            },
            error: function(xhr, status, error) {
                // Handle error
                console.log(xhr.responseText);
            }
        });
    });

    function processData(file_id) {
        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = (evt.loaded / evt.total) * 100;
                        $('#dataProcessProgress').show();
                        $('#dataProcessProgress .progress-bar').css('width', percentComplete + '%');
                        $('#dataProgressLabel').text(percentComplete.toFixed(2) + '%');
                    }
                }, false);
                return xhr;
            },
            url: '{{ url('account/process_data') }}',
            type: 'POST',
            data: { file_id: file_id },
            success: function(response) {
                // Handle success response
                console.log(response);
                // Hide the progress bar after completion
                $('#dataProcessProgress').hide();
            },
            error: function(xhr, status, error) {
                // Handle error
                console.log(xhr.responseText);
            }
        });
    }
});

</script>
@endsection
