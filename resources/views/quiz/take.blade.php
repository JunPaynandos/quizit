@extends('layouts.app')

@section('content')
<div class="container">
    <img src="{{ asset('images/ql.png') }}" alt="Logo" class="logo" style="width: 8rem; height: 4rem;">
    <a href="{{ route('quiz.view', ['subjectId' => $quiz->subject_id, 'quizId' => $quiz->id]) }}" class="class-link" style="position: relative; top: -2.5rem;">
        &lt;Quiz
    </a>
    <br><br>

    <h1 style="font-size: 30px;">{{ $quiz->quiz_name }}</h1>
    <!-- <p>{{ $quiz->description }}</p> -->

    <form action="{{ route('quiz.submit', ['subjectId' => $quiz->subject_id, 'quizId' => $quiz->id]) }}" method="POST">
        @csrf

        @foreach ($quiz->questions as $index => $question)
            <div class="question mb-4">
                <h4 class="question-text"><strong>{{ $index + 1 }}. {{ $question->question_text }}</strong></h4>

                @if($question->question_type == 'multiple_choice')
                    <div class="choices">
                        @foreach ($question->answers as $choice)
                            <div class="form-check">
                                <input class="form-check-input custom-radio" type="radio" name="answers[{{ $question->id }}]" value="{{ $choice->answer_text }}" id="choice{{ $choice->id }}" required>
                                <label class="form-check-label" for="choice{{ $choice->id }}">
                                    {{ $choice->answer_text }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                @elseif($question->question_type == 'true_false')
                    <div class="form-check">
                        <input class="form-check-input custom-radio" type="radio" name="answers[{{ $question->id }}]" value="True" id="true{{ $question->id }}" required>
                        <label class="form-check-label" for="true{{ $question->id }}">
                            True
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input custom-radio" type="radio" name="answers[{{ $question->id }}]" value="False" id="false{{ $question->id }}" required>
                        <label class="form-check-label" for="false{{ $question->id }}">
                            False
                        </label>
                    </div>

                @elseif($question->question_type == 'fill_in_the_blanks')
                    <div class="form-group">
                        <input type="text" name="answers[{{ $question->id }}]" class="form-control" placeholder="Your answer" required>
                    </div>
                @endif
            </div>
        @endforeach

        <button type="submit" class="btn btn-submit">Submit Answers</button>
    </form>
</div>

<style>
    img {
        display: flex;
        align-items: center;
        justify-self: center;
    }

    .class-link {
        color: gray;  /* Initial color */
        text-decoration: none;
        transition: color 0.3s ease, border-bottom 0.3s ease;
    }

    .class-link:hover {
        color: #259e6e;  /* Color on hover */
    }

    /* Custom radio button styling */
    .form-check-input.custom-radio {
        appearance: none; /* Remove default browser styling */
        -webkit-appearance: none;
        background-color: #fff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    /* When the radio button is selected */
    .form-check-input.custom-radio:checked {
        background-color: #33b482; /* Inner circle color */
        border-color: #33b482;
    }

    .form-check-input.custom-radio:checked::before {
        content: ''; 
        position: relative;
        top: 4px;
        left: 4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #fff; /* Inner dot color */
    }

    /* Add custom focus style for radio buttons */
    .form-check-input.custom-radio:focus {
        outline: none; /* Remove the default outline */
        box-shadow: 0 0 0 3px rgba(51, 180, 130, 0.5); /* Custom focus color */
    }

    .question {
        position: relative;
        top: 3rem;
    }

    .question-text {
        font-size: 19px;
    }

    .btn-submit {
        position: relative;
        top: 5rem;
    }
    
    .btn-submit {
        background: #33b482;
        color: white;
    }

    .btn-submit:hover {
        background: #259e6e;
        color: white;
    }

    .choices {
        position: relative;
        margin: 10px 0 10px 0;
    }

    .form-check {
        position: relative;
        margin: 10px 0 10px 0;
    }
</style>

@endsection