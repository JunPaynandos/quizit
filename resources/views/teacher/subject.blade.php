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
            <a href="{{ route('teacher.class') }}" class="class-link">
                < Class
            </a>
            <p class="mb-2 description">Welcome to class!</p>
            <h1>Subject: {{ $subject->subject_name }}</h1>
            <p>Code: {{ $subject->code }}</p>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container-fluid"  style="position: relative; padding-left: 10rem;">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/teacher/subject/{{ $subject->id }}" style="color: #33b482; border-bottom: 1px solid #33b482; margin-right: 1rem;">Module</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" style="margin-right: 1rem;" href="{{ route('quiz.make', ['subjectId' => $subject->id]) }}">Quiz</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('quiz.result', ['subjectId' => $subject->id]) }}" style="margin-right: 1rem;">Scores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('teacher.students', ['subjectId' => $subject->id]) }}" style="margin-right: 1rem;">Students</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="form-container">
        <!-- List of uploaded files-->
        <div class="mt-4 files">
            <h3 style="font-size: 30px;  position: relative; left: 5rem; bottom: 2rem; display: flex; justify-content: space-between; align-items: center; width: 100%; padding-right: 2rem;">
                <span>Modules</span>
                <button type="button" class="btn btn-upload" data-bs-toggle="modal" data-bs-target="#uploadModal" style="position: relative; right: 3rem;">Upload</button>
            </h3>

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
                                <button type="button" class="btn btn-danger btn-sm delete-file-btn" data-file-id="{{ $file->id }}" data-file-name="{{ $file->file_name }}" data-subject-id="{{ $subject->id }}">Delete</button>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
        <br><br><br><br><br><br>
    </div>
</div>

<!-- Modal for File Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div id="upload-modal" class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- File Upload Form inside Modal -->
                <form action="{{ route('subject.uploadFile', $subject->id) }}" method="POST" enctype="multipart/form-data" class="upload-form">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">Choose a file to upload</label>
                        <input type="file" name="file" id="file" class="form-control file-input" required>
                    </div>
                    <button type="submit" class="btn btn-upload">Upload File</button>
                </form>
            </div>
        </div>
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
                <p>Are you sure you want to download this file: <strong><span id="fileNameDisplay"></span></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDownloadBtn" class="btn btn-download">Download</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal for Deleting a File -->
<div id="deleteModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this file: <strong><span id="deleteFileNameDisplay"></span></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteFileForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-delete">Delete</button>
                </form>
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

        // If delete button is clicked, show the modal and set the file details
        $('.delete-file-btn').on('click', function() {
            var fileName = $(this).data('file-name');
            var fileId = $(this).data('file-id');
            var subjectId = $(this).data('subject-id');

            $('#deleteFileNameDisplay').text(fileName);

            // Set the form action URL dynamically to delete the correct file
            var deleteUrl = "{{ route('subject.deleteFile', ['subjectId' => ':subjectId', 'fileId' => ':fileId']) }}";
            deleteUrl = deleteUrl.replace(':subjectId', subjectId).replace(':fileId', fileId);

            $('#deleteFileForm').attr('action', deleteUrl);

            // Show the delete confirmation modal
            $('#deleteModal').modal('show');
        });
    });
</script>

<style>
    .container {
        padding: 0;
        margin: 0;
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

    .form-container {
        margin: 5rem;
    }

    .form-label {
        position: relative;
        top: -20px;
        left: 5rem;
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

    .upload-form {
        position: relative;
        top: 2rem;
        margin-bottom: 2rem;
        font-size: 20px;
    }

    .btn-upload {
        margin-left: 5rem;
        padding: 10px;
        width: 12rem;
        background: #33b482;
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

    .btn-danger {
        position: absolute;
        right: 0;
    }

    .btn-delete {
        background: #e13647;
        color: white;
    }

    .btn-delete:hover {
        background: #bb2d3b;
        color: white;
    }

    .files {
        position: relative;
        top: 2rem;
        width: 66rem;
    }

    .file-input {
        border: 1px solid #ced4da;
        padding: 10px;
        border-radius: 0.375rem;
        font-size: 1rem;
        background-color: #f8f9fa;
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

    .quiz-btn {
        position: relative;
        top: 4rem;
    }

    .quiz {
        position: relative;
        top: 4rem;
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

    .navbar-nav .nav-link {
        transition: color 0.3s ease, border-bottom 0.3s ease;
    }
    
    .navbar-nav .nav-link:hover {
        color: #33b482 !important;
    }

    .modal-dialog {
        width: 23rem;
    }

    #upload-modal {
        width: 25rem;
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