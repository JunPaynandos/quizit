*student/subject.blade.php

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="subject-image-container">
        <div>
            <p>Welcome to class!</p>
            <h1>subject name</h1>
        </div>
    </div>

    <div class="form-container">
        <!-- List of uploaded files (if any) -->
        <div>
            <h3>Files</h3>
            <ul class="file-list">
                    <li class="file-item">No files uploaded yet.</li>
                        <li class="file-item">
		//list of uploaded files
                        </li>
            </ul>
        </div>

        <!-- Display List of Quizzes for the Subject -->
        <div class="mt-4 quiz">
            <h3>Available Quizzes</h3>
                <p>No quizzes available for this subject.</p>
                <ul class="quiz-list">
                    <li class="quiz-item">
                            <div class="quiz-name">
                            </div>
                            <div class="quiz-button">
                                //quiz
                            </div>
                            <!-- If quiz is not taken, display the link to take the quiz -->
                            <div class="quiz-name">
                            </div>

                            <div class="quiz-button">
                                <p>You have not taken this quiz yet.</p>
                            </div>
                    </li>
                </ul>
        </div>
    </div>
</div>



