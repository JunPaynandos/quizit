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
                    <a class="nav-link" href="/teacher/subject/{{ $subject->id }}" style="margin-right: 1rem;">Module</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('quiz.make', ['subjectId' => $subject->id]) }}"  style="color: #33b482; border-bottom: 1px solid #33b482; margin-right: 1rem;">Quiz</a>
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
        <div class="mt-4 quiz">
            <div class="quiz-header">
                <h3>List of Quizzes</h3>
                <a href="{{ route('quiz.create', ['subjectId' => $subject->id]) }}" class="btn btn-create-quiz">Create New Quiz</a>
            </div>
            <ul class="quiz-list">
                @if($subject->quizzes->isEmpty())
                    <li>No quizzes created yet.</li>
                @else
                    @foreach($subject->quizzes as $quiz)
                        <li class="quiz-item flex justify-between items-center">
                            <a href="{{ route('quiz.show', ['subjectId' => $subject->id, 'quizId' => $quiz->id]) }}" class="quiz-link">{{ $quiz->quiz_name }}</a>
                            
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $quiz->id }}">
                                Delete
                            </button>
                            
                            <div class="modal fade" id="deleteModal{{ $quiz->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $quiz->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel{{ $quiz->id }}">Confirm Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to delete this quiz: "{{ $quiz->quiz_name }}"?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('quiz.delete', ['subjectId' => $subject->id, 'quizId' => $quiz->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
    </div>
</div>
<br><br><br><br>
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

    .btn-create-quiz {
        padding: 10px;
        width: 12rem;
        background: #33b482;
        color: white;
    }

    .btn-create-quiz:hover {
        background: #259e6e;
        color: white;
    }

    .quiz {
        position: relative;
        width: 66rem;
    }

    .quiz-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 30px;
        margin-left: 5rem;
    }

    .quiz-list {
        position: relative;
        list-style-type: none;
        padding-left: 5rem;
        top: 30px;
        line-height: 40px;
        width: 100%;
    }

    .quiz-item {
        padding: 8px 0;
        border-bottom: 1px solid #ddd;
        width: 100%;
    }

    .quiz-link {
        color: #33b482;
        text-decoration: none;
        font-size: 18px;
    }

    .quiz-link:hover {
        text-decoration: underline;
    }

    .navbar-nav .nav-link {
        transition: color 0.3s ease, border-bottom 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
        color: #33b482 !important;
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: none;
    }

    .modal-dialog {
        position: relative;
        width: 23rem;
    }

    #success-message {
        background: #00bf7d;
    }

    #error-message {
        background: #ca131a;
    }
</style>
@endsection