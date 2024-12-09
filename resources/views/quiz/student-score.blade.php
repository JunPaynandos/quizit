@extends('layouts.app')

@section('content')
<div class="container">

    <img src="{{ asset('images/qlf.png') }}" alt="Logo" class="logo" style="width: 8rem; height: 4rem;">
    <a href="{{ route('subject.quiz', ['subjectId' => $subject->id]) }}" class="class-link" style="position: relative; top: -2.5rem;">
        &lt;Quiz
    </a>
    <br><br>
    <!-- Quiz Results Section -->
    <div class="quiz-results">
        <div class="score-summary">
            <p style ="font-size: 30px; position: relative; bottom: 5px;"><strong>{{ $quiz->quiz_name }}</strong></p>
            <p><strong>Your Score:</strong> {{ $score }} / {{ $totalQuestions }}</p>
        </div>

        <!-- Display Questions and Answers -->
        <div class="question-list">
            @foreach ($questionResults as $result)
                <div class="question-item">
                    <p style="position: relative; top: 5px; bottom: 5px;"><strong>Question:</strong> {{ $result['question_text'] }}</p>
                    <p style="position: relative; top: 10px; bottom: 5px;">
                        <strong>Correct Answer:</strong>
                        <span style="color: #259e6e;">{{ $result['correct_answer'] }}</span>
                    </p>
                    <p  style="position: relative; top: 10px; bottom: 5px;">
                        <strong>Your Answer:</strong>
                        @if ($result['user_answer'] === $result['correct_answer'])
                            <span style="color: #259e6e;">{{ $result['user_answer'] }}</span>
                        @else
                            <span style="color: #bb2d3b;">{{ $result['user_answer'] }}</span>
                        @endif
                    </p>
                    <hr>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

<style>
    hr {
        position: relative;
        top: 20px;
    }
    .container {
        padding: 0;
        margin: 0;
    }

    .quiz-results {
        margin: 2rem;
    }

    .score-summary {
        margin-bottom: 1rem;
    }

    .question-list {
        margin-top: 2rem;
    }

    .question-item {
        margin-bottom: 2rem;
    }

    .btn-click {
        background-color: #259e6e;
        color: white;
    }

    .btn-click:hover {
        background-color: #259e6e;
        color: white;
    }

    img {
        display: flex;
        align-items: center;
        justify-self: center;
    }

    .class-link {
        color: gray;
        text-decoration: none;
        transition: color 0.3s ease, border-bottom 0.3s ease;
    }

    .class-link:hover {
        color: #259e6e; 
    }
</style>