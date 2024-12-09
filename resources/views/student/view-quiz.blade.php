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
                    <a class="nav-link" href="{{ route('student.subject', ['id' => $subject->id]) }}" style="margin-right: 1rem;">Module</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" style="color: #33b482; border-bottom: 1px solid #33b482; margin-right: 1rem;" href="{{ route('subject.quiz', ['subjectId' => $subject->id]) }}">Quiz</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="form-container">       
        <!-- Display List of Quizzes for the Subject -->
        <div class="mt-4 quiz">
            <h3 style="font-size: 30px; position: relative; left: 5rem;">Available Quizzes</h3>
            @if($subject->quizzes->isEmpty())
                <p>No quizzes available for this subject.</p>
            @else
                <ul class="quiz-list" style="position: relative;">
                    @foreach($subject->quizzes as $quiz)
                    <li class="quiz-item" style="position: relative;">
                        @php
                            // Check if the user has already taken the quiz
                            $quizTaken = $quiz->results->where('user_id', auth()->user()->id)->first();
                        @endphp

                        @if($quizTaken)
                            <!-- If quiz is taken, display the result link and hide the quiz link -->
                            <div class="quiz-name">
                                <span>{{ $quiz->quiz_name }}</span> 
                            </div>
                            
                            <div class="quiz-button">
                                <a href="{{ route('quiz.student-score', ['subjectId' => $subject->id, 'quizId' => $quiz->id]) }}" class="btn btn-result">View Your Score</a>
                            </div>
                        @else
                            <!-- If quiz is not taken, display the link to take the quiz -->
                            <div class="quiz-name">
                                <a href="{{ route('quiz.take', ['subjectId' => $subject->id, 'quizId' => $quiz->id]) }}" class="quiz-link">
                                    {{ $quiz->quiz_name }}
                                </a>
                            </div>

                            <div class="quiz-button">
                                <p>You have not taken this quiz yet.</p>
                            </div>
                        @endif
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
<br><br><br><br>
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

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        color: white;
    }

    .btn-result {
        background-color: #057d54;
        color: white;
        position: relative;
        top: -5px;
    }

    .btn-result:hover {
        background-color: #046942;
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