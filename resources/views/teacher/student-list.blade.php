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
                    <a class="nav-link" href="{{ route('quiz.make', ['subjectId' => $subject->id]) }}" style="margin-right: 1rem;">Quiz</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('quiz.result', ['subjectId' => $subject->id]) }}" style="margin-right: 1rem;">Scores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('teacher.students', ['subjectId' => $subject->id]) }}" style="color: #33b482; border-bottom: 1px solid #33b482; margin-right: 1rem;">Students</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- List of Enrolled Students -->
    <div class="students">
        <h2 class="mt-4" style="font-size: 30px;">Enrolled Students</h2>
        @if($students->isEmpty())
            <p style="position: relative; top: 2rem;">No students enrolled in this subject.</p>
        @else
            <ul class="student-list">
                @foreach($students as $student)
                    <li class="student-count d-flex justify-content-between" style="margin-bottom: 10px;">
                        <span>{{ $loop->iteration }}. {{ $student->user->name }}</span>
                        <form action="{{ route('subject.removeStudent', ['subject' => $subject->id, 'student' => $student->user_id]) }}" method="POST" style="display: inline; margin-left: 10px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this student from the subject?')">Remove</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
    <br><br><br><br><br>
</div>
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

    .students {
        position: relative;
        margin-top: 5rem;
        left: 10rem;
        width: 66rem;
    }

    .student-list {
        position: relative;
        list-style-type: none;
        padding-left: 5rem;
        top: 15px;
        left: -5rem;
        line-height: 40px;
        width: 100%;
    }

    .student-count {
        padding: 8px 0;
        border-bottom: 1px solid #ddd;
        width: 100%;
    }

    .list-group {
        width: 100%;
        margin-top: 2rem;
    }

    .list-group-item {
        font-size: 18px;
    }

    #success-message {
        background: #00bf7d;
    }

    #error-message {
        background: #ca131a;
    }
</style>
@endsection
