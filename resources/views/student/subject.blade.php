@extends('layouts.app')

@if (session('success'))
    <div id="success-message" class="fixed top-7 right-7 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg" style="top: 5rem; right: 3.7rem; z-index: 100;">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(function() {
            var message = document.getElementById('success-message');
            if (message) {
                message.style.transition = "opacity 0.5s ease";
                message.style.opacity = 0;

                setTimeout(function() {
                    message.style.display = 'none';
                }, 500);
            }
        }, 3000);
    </script>
@endif

@if (session('error'))
    <div id="error-message" class="fixed top-7 right-7 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg" style="top: 5rem; right: 3.7rem; z-index: 100;">
        {{ session('error') }}
    </div>
    <script>
        setTimeout(function() {
            var message = document.getElementById('error-message');
            if (message) {
                message.style.transition = "opacity 0.5s ease";
                message.style.opacity = 0;
                setTimeout(function() {
                    message.style.display = 'none';
                }, 500);
            }
        }, 3000);
    </script>
@endif

@section('content')
<div class="container">
    <div class="subject-image-container">
        <div class="text-overlay text-black">
            <a href="{{ route('student.class') }}" class="class-link">
                < Class
            </a>
            <p class="mb-2 description">Welcome to class!</p>
            <h1 style="font-size: 1.8rem;">{{ $subject->subject_name }}</h1>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container-fluid"  style="position: relative; padding-left: 10rem;">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#" style="color: #33b482; border-bottom: 1px solid #33b482; margin-right: 1rem;">Module</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" style="margin-right: 1rem;" href="{{ route('subject.quiz', ['subjectId' => $subject->id]) }}">Quiz</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="form-container">
        <!-- List of uploaded files-->
        <div class="mt-4 files">
            <h3 style="font-size: 30px; position: relative; left: 5rem;">Modules</h3>
            <ul class="file-list">
                @if($subject->files->isEmpty())
                    <li class="file-item">No files uploaded yet.</li>
                @else
                    @foreach($subject->files as $file)
                        <li class="file-item">
                            @if (in_array(pathinfo($file->file_name, PATHINFO_EXTENSION), ['pdf']))
                                <a href="{{ route('subject.viewFile', ['subjectId' => $subject->id, 'fileId' => $file->id]) }}" target="_blank" class="file-link">{{ $file->file_name }}</a>
                            @else
                                <a href="javascript:void(0)" class="confirm-download file-link" data-file-url="{{ route('subject.viewFile', ['subjectId' => $subject->id, 'fileId' => $file->id]) }}" data-file-name="{{ $file->file_name }}">{{ $file->file_name }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
        <br><br><br>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Download</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Confirm to download this file: <strong><span id="fileNameDisplay"></span></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDownloadBtn" class="btn btn-download">Download</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        var fileUrl = ""; // Variable to store the file URL for download
        var fileName = ""; // Variable to store the file name for display in the modal

        // Show the confirmation modal when a download link is clicked
        $('.confirm-download').on('click', function() {
            // Get the file URL and name from the data attributes
            fileUrl = $(this).data('file-url');
            fileName = $(this).data('file-name');

            // Display the filename in the modal
            $('#fileNameDisplay').text(fileName);

            // Show the confirmation modal
            $('#confirmationModal').modal('show');
        });

        // If the user confirms, proceed with the download
        $('#confirmDownloadBtn').on('click', function() {
            window.location.href = fileUrl; // Redirect to the file URL to download
            $('#confirmationModal').modal('hide');
        });
    });
</script>

<style>
    .container {
        padding: 0;
        margin: 0;
    }

    .form-container {
        margin: 5rem;
    }

    .subject-image-container {
        width: 98.9vw;
        height: 400px;
        background-image: url('{{ asset('images/booksbg2.png') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
    }

    .text-overlay {
        position: absolute;
        top: 70%;
        left: 18%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 20px;
        font-weight: 800;
        text-align: left;
        padding: 20px;
    }

    .description {
        font-size: 40px;
    }

    .quiz-list {
        list-style: none;
        padding-left: 0;
    }

    .quiz-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .quiz-link {
        color: #33b482;
        text-decoration: none;
        font-size: 18px;
    }

    .quiz-link:hover {
        text-decoration: underline;
    }

    .quiz-button {
        margin-left: auto;
    }

    .class-link {
        position: absolute;
        top: -11.5rem;
        color: gray;
        text-decoration: none;
        transition: color 0.3s ease, border-bottom 0.3s ease;
    }

    .class-link:hover {
        color: #259e6e;
    }

    .btn-result {
        background-color: #057d54;
        color: white;
    }

    .btn-result:hover {
        background-color: #046942;
        color: white;
    }

    .btn-download {
        background: #33b482;
        color: white;
    }

    .btn-upload:hover, .btn-download:hover {
        background: #259e6e;
        color: white;
    }

    .files {
        position: relative;
        width: 66rem;
    }

    .file-list {
        position: relative;
        list-style-type: none;
        padding-left: 5rem;
        top: 15px;
        line-height: 40px;
        width: 100%;
    }

    .file-item {
        padding: 8px 0;
        border-bottom: 1px solid #ddd;
        width: 100%;
    }

    .file-link {
        color: #33b482;
        text-decoration: none;
    }

    .file-link:hover {
        text-decoration: underline;
    }

    .quiz {
        position: relative;
        width: 66rem;
    }

    .quiz-list {
        position: relative;
        list-style-type: none;
        padding-left: 5rem;
        top: 70px;
        line-height: 40px;
        width: 100%;
    }

    .quiz-item {
        padding: 8px 0;
        border-bottom: 1px solid #ddd;
        width: 100%;
    }

    .modal-dialog {
        width: 23rem;
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: none;
    }

    #success-message {
        background: #00bf7d;
    }

    #error-message {
        background: #ca131a;
    }
</style>
@endsection