@extends('layouts.app')

@section('content')
<div class="container my-5">

    <img src="{{ asset('images/ql.png') }}" alt="Logo" class="logo" style="width: 8rem; height: 4rem;">
    <a href="{{ route('quiz.make', ['subjectId' => $subject->id]) }}" class="class-link" style="position: relative; top: -2.5rem;">
        &lt;Quiz
    </a>
    <br><br>
    <form action="{{ route('quiz.store', $subject->id) }}" method="POST">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Quiz Name -->
        <div class="form-group mb-4">
            <label for="quiz_name">Quiz Name</label>
            <input type="text" name="quiz_name" id="quiz_name" class="form-control" required>
        </div>

        <!-- Quiz Description -->
        <div class="form-group mb-4">
            <label for="description">Description (Optional)</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>

        <!-- Number of Questions -->
        <div class="form-group mb-4">
            <label for="num_questions">Number of Questions</label>
            <input type="number" name="num_questions" id="num_questions" class="form-control" min="1" required>
        </div>

        <div id="questions-container">
            <!-- Questions will be dynamically added here based on num_questions -->
        </div>

        <button type="submit" class="btn btn-submit mt-4 p-2">Create Quiz</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#num_questions').on('input', function () {
            var numQuestions = parseInt($(this).val());
            var questionsContainer = $('#questions-container');
            questionsContainer.empty(); // Clear previous questions if any

            for (var i = 0; i < numQuestions; i++) {
                var questionDiv = $(` 
                    <div class="question mb-5 p-4 border rounded shadow-sm">
                        <h4>Question ${i + 1}</h4>
                        <div class="form-group mb-3">
                            <label for="questions[${i}][text]"></label>
                            <input type="text" name="questions[${i}][text]" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="questions[${i}][type]">Question Type</label>
                            <select name="questions[${i}][type]" class="form-control question-type" required>
                                <option value="">Select Type</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="fill_in_the_blanks">Fill in the Blanks</option>
                            </select>
                        </div>
                        <div class="form-group choices-container" style="display: none;">
                            <label>Choices (A-D)</label>
                            <input type="text" name="questions[${i}][choice_a]" class="form-control mb-2" placeholder="Choice A" required>
                            <input type="text" name="questions[${i}][choice_b]" class="form-control mb-2" placeholder="Choice B" required>
                            <input type="text" name="questions[${i}][choice_c]" class="form-control mb-2" placeholder="Choice C" required>
                            <input type="text" name="questions[${i}][choice_d]" class="form-control mb-2" placeholder="Choice D" required>
                        </div>
                        <div class="form-group answer-container" style="display: none;">
                            <label for="questions[${i}][selected]">Correct Answer</label>
                            <select name="questions[${i}][selected]" class="form-control">
                                <option value="a">A</option>
                                <option value="b">B</option>
                                <option value="c">C</option>
                                <option value="d">D</option>
                            </select>
                        </div>
                        <div class="form-group fill-in-the-blank-container" style="display: none;">
                            <label for="questions[${i}][guess]">Correct Answer</label>
                            <input type="text" name="questions[${i}][guess]" class="form-control" placeholder="Fill in the blank answer" required>
                        </div>
                        <div class="form-group true-false-container" style="display: none;">
                            <label for="questions[${i}][answer]">Correct Answer</label>
                            <select name="questions[${i}][answer]" class="form-control">
                                <option value="true">True</option>
                                <option value="false">False</option>
                            </select>
                        </div>
                    </div>
                `);
                questionsContainer.append(questionDiv);
            }
        });

        $('#questions-container').on('change', '.question-type', function () {
            var selectedType = $(this).val();
            var questionDiv = $(this).closest('.question');

            // Hide all answer containers initially
            questionDiv.find('.choices-container, .answer-container, .fill-in-the-blank-container, .true-false-container').hide();
            questionDiv.find('.choices-container input').removeAttr('required');
            questionDiv.find('.answer-container select').removeAttr('required');
            questionDiv.find('.fill-in-the-blank-container input').removeAttr('required');
            questionDiv.find('.true-false-container select').removeAttr('required');

            // Show relevant answer container based on selected type
            if (selectedType === 'multiple_choice') {
                questionDiv.find('.choices-container').show();
                questionDiv.find('.answer-container').show(); // Show correct answer dropdown for multiple choice
                questionDiv.find('.choices-container input').attr('required', 'required'); // Add required for choices
                questionDiv.find('.answer-container select').attr('required', 'required'); // Add required for answer
            } else if (selectedType === 'true_false') {
                questionDiv.find('.true-false-container').show();
                questionDiv.find('.true-false-container select').attr('required', 'required');
            } else if (selectedType === 'fill_in_the_blanks') {
                questionDiv.find('.fill-in-the-blank-container').show();
                questionDiv.find('.fill-in-the-blank-container input').attr('required', 'required');
            }
        });

        $('form').on('submit', function () {
            // Show all fields before submission to avoid focusable errors
            $('.choices-container, .answer-container, .fill-in-the-blank-container, .true-false-container').each(function() {
                if ($(this).is(":hidden")) {
                    $(this).find('input, select').removeAttr('required');
                }
            });
        });
    });
</script>
<style>
    .question {
        background: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.6);
    }

    .btn-submit {
        background: #33b482;
        color: white;
    }

    .btn-submit:hover {
        background: #259e6e;
        color: white;
    }

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
        /* text-decoration: underline; */
    }
</style>
@endsection