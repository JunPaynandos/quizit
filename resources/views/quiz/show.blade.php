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
<style>
    .question {
        /* background: rgba(81, 253, 189, 0.1);  */
        border: 1px solid lightgray;
        border-radius: 10px;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.4);
        margin-bottom: 20px; /* Adding margin to separate questions */
        padding: 20px;
    }

    .btn-submit {
        background: #33b482;
        color: white;
    }

    .btn-submit:hover {
        background: #259e6e;
        color: white;
    }

    .btn-back {
        background-color: #41ca96;
        color: white;
        font-weight: bold;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 20px;
    }

    .btn-back:hover {
        background-color: #259e6e;
    }

    img {
        display: flex;
        align-items: center;
        justify-self: center;
    }

    .class-link {
        position: absolute;
        top: -15rem;
        left: 2rem;
        color: gray;
        text-decoration: none;
        transition: color 0.3s ease, border-bottom 0.3s ease;
    }

    .class-link:hover {
        color: #259e6e;
    }

    .question-header {
        font-size: 1.5rem;
        top: 4.5rem;
        margin-bottom: 1rem;
        color: #333;
    }

    .question-list {
        margin-top: 6rem;
        padding-left: 20px;
        list-style-type: none;
    }

    .question-item {
        margin-bottom: 15px;
    }

    .question-item strong {
        font-size: 1.2rem;
    }

    .question-label {
        font-size: 1.1rem;
        font-weight: bold;
        color: #41ca96;
    }

    .list-group-item {
        background: transparent; 
        border: none;
        padding: 10px;     
    }

    /* Custom styles for the Edit button */
    .edit-btn {
        background-color: #33b482;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 0.9rem;
        text-decoration: none;
        margin-top: 10px;
    }

    .edit-btn:hover {
        background-color: #259e6e;
    }

    /* Custom styles for the Delete button */
    .delete-btn {
        background-color: #f44336;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 0.9rem;
        text-decoration: none;
        margin-top: 10px;
    }

    .delete-btn:hover {
        background-color: #d32f2f;
    }

    /* Style for input fields */
    input.form-control {
        color: black;
    }

    /* Style for input fields when focused */
    input.form-control:focus {
        border-color: #33b482;
        box-shadow: 0 0 0 0.2rem rgba(51, 180, 130, 0.25); /* Focus box shadow */
    }

    input[type="checkbox"] {
        background=color: #33b482;
        accent-color: #33b482;
        margin-left: 10px;
    }

    input[type="checkbox"]:checked {
        background=color: #33b482;
        accent-color: #33b482;
    }

    /* Style for checkbox when focused */
    input[type="checkbox"]:focus {
        background=color: #33b482;
        box-shadow: 0 0 0 0.2rem rgba(51, 180, 130, 0.25); /* Focus box shadow for checkbox */
    }

    .btn-submit {
        background: #33b482;
        color: white;
    }

    .btn-submit:hover {
        background: #259e6e;
        color: white;
    }

    #success-message {
        background: #00bf7d;
    }

    #error-message {
        background: #ca131a;
    }
</style>

<!-- Main Content -->
<div class="container my-5">
    <img src="{{ asset('images/ql.png') }}" alt="Logo" class="logo" style="width: 8rem; height: 4rem;">
    
    <a href="{{ route('quiz.make', ['subjectId' => $quiz->subject_id]) }}" class="class-link" style="position: relative; top: -2.5rem;">
        &lt;Quiz
    </a>

    <h3 class="question-header" style="position: relative; left: 1.5rem;">{{ $quiz->quiz_name }}</h3>
    <ul class="question-list">
        @foreach ($quiz->questions as $index => $question)
            <li class="question-item">
                <div class="question">
                    <strong>Question {{ $index + 1 }}: {{ $question->question_text }}</strong>
                    <br>
                    <em>Type: {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</em>
                    <ul class="list-group mt-2">
                        @foreach ($question->answers as $answer)
                            <li class="list-group-item">
                                {{ $answer->answer_text }} 
                                @if($answer->is_correct) 
                                    <strong>(Correct)</strong> 
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <!-- Edit button triggers the modal -->
                    <button class="edit-btn" data-bs-toggle="modal" data-bs-target="#editQuestionModal" 
                        data-quiz-id="{{ $quiz->id }}" 
                        data-question-id="{{ $question->id }}" 
                        data-question-text="{{ $question->question_text }}" 
                        data-question-type="{{ $question->question_type }}" 
                        data-answers="{{ json_encode($question->answers) }}">
                        Edit Question
                    </button>

                    <!-- Delete button triggers the delete functionality -->
                    <form action="{{ route('question.delete', ['quizId' => $quiz->id, 'questionId' => $question->id]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this question? This action cannot be undone.')">Delete Question</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
</div>

<!-- Modal for editing the question -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-labelledby="editQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editQuestionModalLabel">Edit Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-question-form" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="question_text" class="form-label">Question Text</label>
                        <input type="text" class="form-control" id="question_text" name="question_text" required>
                    </div>

                    <!-- Replacing select with input to display the current question type -->
                    <div class="mb-3">
                        <label for="question_type" class="form-label">Question Type</label>
                        <input type="text" class="form-control" id="question_type" name="question_type" readonly required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Answers</label>
                        <div id="answers-container"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal JS to populate data dynamically -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var editQuestionModal = document.getElementById('editQuestionModal');
        
        // Handle modal showing and populating values
        editQuestionModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var questionId = button.getAttribute('data-question-id');
            var questionText = button.getAttribute('data-question-text');
            var questionType = button.getAttribute('data-question-type');
            var answers = JSON.parse(button.getAttribute('data-answers'));
            var quizId = button.getAttribute('data-quiz-id');  // Add quizId here

            var modal = editQuestionModal.querySelector('.modal-body');
            modal.querySelector('#question_text').value = questionText;

            // Set the question type as a readonly input (not changeable)
            modal.querySelector('#question_type').value = questionType;

            // Clear previous answers
            var answersContainer = modal.querySelector('#answers-container');
            answersContainer.innerHTML = '';

            // Populate answers and set correct answers
            answers.forEach(function (answer, index) {
                var isChecked = answer.is_correct ? 'checked' : '';
                answersContainer.innerHTML += `
                    <div class="mb-2">
                        <input type="text" class="form-control mb-2" name="answers[${answer.id}]" value="${answer.answer_text}" required>
                        <input type="checkbox" name="correct_answers[]" value="${answer.id}" ${isChecked}> Correct
                    </div>
                `;
            });

            // Prevent multiple checkboxes from being checked for the same question
            var checkboxes = answersContainer.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    // If any checkbox is checked, uncheck others
                    checkboxes.forEach(function (otherCheckbox) {
                        if (otherCheckbox !== checkbox) {
                            otherCheckbox.checked = false;
                        }
                    });
                });
            });

            // Update the form's action URL
            var form = document.getElementById('edit-question-form');
            form.action = '/quiz/' + quizId + '/question/' + questionId + '/update';  // Make sure the URL includes both quizId and questionId
        });
    });
</script>

@endsection