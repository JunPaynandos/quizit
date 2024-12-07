@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Quiz for {{ $subject->subject_name }}</h1>

    <form action="{{ route('quiz.store', $subject->id) }}" method="POST">
        @csrf
        <!-- Number of Questions Input -->
        <div class="mb-3">
            <label for="num_questions" class="form-label">Number of Questions</label>
            <input type="number" name="num_questions" id="num_questions" class="form-control" required>
        </div>

        <!-- Question Type Input -->
        <div class="mb-3">
            <label for="question_type" class="form-label">Question Type</label>
            <select name="question_type" id="question_type" class="form-control" required>
                <option value="multiple_choice">Multiple Choice</option>
                <option value="true_false">True/False</option>
                <option value="fill_in_blank">Fill in the Blank</option>
            </select>
        </div>

        <!-- Dynamic Question Inputs -->
        <div id="question_fields"></div>

        <button type="submit" class="btn btn-primary">Save Quiz</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        // Handle number of questions input
        $('#num_questions').on('input', function () {
            var numQuestions = $(this).val();
            var questionType = $('#question_type').val();
            generateQuestionFields(numQuestions, questionType);
        });

        // Handle question type change
        $('#question_type').on('change', function () {
            var numQuestions = $('#num_questions').val();
            var questionType = $(this).val();
            generateQuestionFields(numQuestions, questionType);
        });

        // Function to generate question fields dynamically
        function generateQuestionFields(numQuestions, questionType) {
            var questionFieldsHtml = '';
            
            for (var i = 1; i <= numQuestions; i++) {
                questionFieldsHtml += `<div class="question-group mb-3">
                    <label for="question_${i}" class="form-label">Question ${i} Text</label>
                    <input type="text" name="questions[${i}][text]" id="question_${i}" class="form-control" placeholder="Enter the question text" required>
                `;

                if (questionType == 'multiple_choice') {
                    questionFieldsHtml += `
                        <div>
                            <label class="form-label">Choices for Question ${i}</label><br>
                            <input type="radio" name="questions[${i}][answer]" value="a"> A: <input type="text" name="questions[${i}][choice_a]" class="form-control" placeholder="Choice A" required><br>
                            <input type="radio" name="questions[${i}][answer]" value="b"> B: <input type="text" name="questions[${i}][choice_b]" class="form-control" placeholder="Choice B" required><br>
                            <input type="radio" name="questions[${i}][answer]" value="c"> C: <input type="text" name="questions[${i}][choice_c]" class="form-control" placeholder="Choice C" required><br>
                            <input type="radio" name="questions[${i}][answer]" value="d"> D: <input type="text" name="questions[${i}][choice_d]" class="form-control" placeholder="Choice D" required>
                        </div>
                    `;
                } else if (questionType == 'true_false') {
                    questionFieldsHtml += `
                        <div>
                            <label class="form-label">Answer for Question ${i}</label><br>
                            <input type="radio" name="questions[${i}][answer]" value="true"> True
                            <input type="radio" name="questions[${i}][answer]" value="false"> False
                        </div>
                    `;
                } else if (questionType == 'fill_in_blank') {
                    questionFieldsHtml += `
                        <div>
                            <label class="form-label">Correct Answer for Question ${i}</label>
                            <input type="text" name="questions[${i}][answer]" class="form-control" placeholder="Correct answer" required>
                        </div>
                    `;
                }

                questionFieldsHtml += '</div>';
            }

            $('#question_fields').html(questionFieldsHtml);
        }
    });
</script>

@endsection