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
        
        /* Style for the file input */
        .file-input {
            display: none;
        }
        
        /* Style for the drag and drop area */
        .drag-drop-area {
            border: 2px dashed #aaa;
            padding: 20px;
            border-radius: 10px;
            cursor: pointer;
        }
        
        /* Style for the upload button */
        .upload-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        /* Style for the loader */
        #loader {
            display: none;
            margin-top: 20px;
        }
        
        /* Style for the message */
        #message {
            display: none;
            margin-top: 20px;
        }
        
        
</style>
    <x-filters.filter-box>
     


    </x-filters.filter-box>

@endsection

@section('content')
    
    <!--<form class="" method="POST" action="{{ url('salary_sheet_store') }}">-->
    <div class="form-container mt-5" id="uploadForm" data-upload-url="">
        <h2>Upload File</h2>
        <div id="" class="text-warning mt-2 mb-2">Column Of The Sheet Must Be In This Alignment Employee Id , CTC Annual , Bank Name , Account Number . </div>
        <div class="drag-drop-area" id="dragDropArea">
            <p>Click to select files ( Only CSV And XLSX)</p>
            <input type="file" class="file-input" name="file" required id="fileInput" accept=".csv, .xlsx">
        </div>
        <p id="fileNameDisplay"></p>
        <input type="checkbox" id="hasHeadingCheckbox">
        <label for="hasHeadingCheckbox" >File contains heading</label>
        <button class="upload-btn mt-3 ml-2" id="uploadBtnn" type="submit">Upload</button>
        <div id="loader" class="text-info">Updating Data! Please Wait...</div>
        <div id="message"></div>
    </div>
    <!--</form>-->
<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
<script>
       document.addEventListener('DOMContentLoaded', function () {
        const uploadForm = document.getElementById('uploadForm');
        const dragDropArea = document.getElementById('dragDropArea');
        const fileInput = document.getElementById('fileInput');
        const uploadBtn = document.getElementById('uploadBtnn');
        const loader = document.getElementById('loader');
        const message = document.getElementById('message');
    
        // Handle click on the drag and drop area to trigger file selection
        dragDropArea.addEventListener('click', function () {
            fileInput.click();
        });
    
        // Handle file input change
        fileInput.addEventListener('change', function () {
            const files = fileInput.files;
        });
    
        // Handle form submission
        uploadBtn.addEventListener('click', function () {
            const files = fileInput.files;
            const hasHeading = document.getElementById('hasHeadingCheckbox').checked;
            
            if (files.length === 0) {
                showMessage('No file selected!');
                return;
            }
            uploadFile(files[0],hasHeading);
        });
    
        // Upload file using AJAX
        function uploadFile(file,hasHeading) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('hasHeading', hasHeading);
    
    
            const xhr = new XMLHttpRequest();
            xhr.open('POST', "{{ url('salary_sheet_store') }}", true);
    
            // Show loader
            loader.style.display = 'block';
    
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Hide loader
                    loader.style.display = 'none';
                    // Show success message
                    $('#message').addClass('text-success');
                    console.log(xhr.response);
                    showMessage('Data uploaded successfully');
                    // Reset file input
                    fileInput.value = '';
                } else {
                    // Hide loader
                    loader.style.display = 'none';
                    $('#message').addClass('text-danger');
                    // Show error message
                    showMessage('Error uploading file!');
                }
            };
    
            xhr.onerror = function () {
                // Hide loader
                loader.style.display = 'none';
                // Show error message
                console.log(xhr.response);
                showMessage('Error uploading file!');
            };
    
            xhr.send(formData);
        }
    
        // Show message
        function showMessage(msg) {
            message.innerHTML = msg;
            message.style.display = 'block';
            setTimeout(function () {
                message.style.display = 'none';
            }, 5000); // Hide message after 5 seconds
        }
    });
    
    // Handle file input change
    fileInput.addEventListener('change', function () {
        const files = fileInput.files;
        if (files.length > 0) {
            const fileName = files[0].name;
            fileNameDisplay.textContent = fileName; // Update file name display
        }
    });
    

</script>

@endsection
