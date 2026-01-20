@extends('layouts.app')

@section('content')
<style>
    .section {
        background: #f9f9f9;
        margin-bottom: 40px;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .section h2 {
        font-size: 24px;
        margin-bottom: 15px;
        color: #333;
    }

    .video-container {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%;
        margin-bottom: 20px;
        border-radius: 8px;
        overflow: hidden;
    }

    .video-container video {
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        object-fit: cover;
    }

    .faq {
        background: #fff;
        padding: 15px;
        margin-top: 10px;
        border-left: 4px solid #007bff;
        border-radius: 6px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .faq h4 {
        margin: 0 0 5px;
        color: #007bff;
        font-size: 16px;
    }

    .faq p {
        margin: 0;
        color: #555;
        font-size: 15px;
    }

    @media (max-width: 768px) {
        .section {
            padding: 20px;
        }

        .section h2 {
            font-size: 20px;
        }
    }
</style>

<div class="container py-4">

    {{-- Document Upload Section --}}
    <div class="section">
        <h2>Document Upload</h2>
        <div class="video-container">
            <video controls src="{{ asset('/public/doc.mp4') }}"></video>
        </div>
        <div class="faq">
            <h4>How do I upload my documents?</h4>
            <p>First login, complete the joining form, upload your signature, accept all policies, then go to HR → Documents to upload files.</p>
        </div>
        <div class="faq">
            <h4>Where do I see uploaded documents?</h4>
            <p>Go to HR → Documents, and all uploaded files will be listed there.</p>
        </div>
        <div class="faq">
            <h4>Can I re-upload or delete a document?</h4>
            <p>You can add new documents. Editing or deleting might be restricted based on HR settings.</p>
        </div>
    </div>

    {{-- Attendance Dashboard Section --}}
    <div class="section">
        <h2>Attendance Dashboard</h2>
        <div class="video-container">
            <video controls src="{{ asset('/agent_dash.mp4') }}"></video>
        </div>
        <div class="faq">
            <h4>What does "Short Login" mean?</h4>
            <p>If your login hours are less than expected, it’s marked as short. Three such days in a row results in a half-day.</p>
        </div>
        <div class="faq">
            <h4>What is the login hour target?</h4>
            <p>Your dashboard shows your daily and monthly targets along with achieved hours.</p>
        </div>
        <div class="faq">
            <h4>Where can I track my attendance?</h4>
            <p>Open the Dashboard to view present days, target login, and achieved login stats.</p>
        </div>
    </div>

    {{-- Profile Picture Section --}}
    <div class="section">
        <h2>Change Profile Picture</h2>
        <div class="video-container">
            <video controls src="{{ asset('/profilechange.mp4') }}"></video>
        </div>
        <div class="faq">
            <h4>How to change my profile picture?</h4>
            <p>Click the “Edit” option in the top-right menu and upload a new image.</p>
        </div>
        <div class="faq">
            <h4>What formats are supported?</h4>
            <p>Use JPG, PNG, or WebP formats up to 2MB.</p>
        </div>
        <div class="faq">
            <h4>Why isn't my profile picture updating?</h4>
            <p>Try clearing browser cache or check if the file format and size are valid.</p>
        </div>
    </div>

    {{-- Attendance Regularization Section --}}
    <div class="section">
        <h2>Attendance Regularization</h2>
        <div class="video-container">
            <video controls src="{{ asset('/regular.mp4') }}"></video>
        </div>
        <div class="faq">
            <h4>How do I raise a regularization request?</h4>
            <p>Go to HR → Attendance Regularization, choose the date and reason, then submit the form.</p>
        </div>
        <div class="faq">
            <h4>What happens after I submit?</h4>
            <p>Your manager or HR will review and either approve or reject the request.</p>
        </div>
        <div class="faq">
            <h4>Can I cancel a submitted request?</h4>
            <p>You can cancel only if it’s not yet reviewed. Once approved or rejected, it's locked.</p>
        </div>
    </div>
</div>
@endsection
