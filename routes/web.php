<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Subject;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SubjectQuizController;
use App\Http\Controllers\StudentQuizController;

Route::get('/', function () {
    return view('welcome');
});

// Email verification routes
Route::middleware(['auth'])->group(function () {
    // Route to show verification notice
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // Route for email verification
    Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        if ($user->markEmailAsVerified()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('verification.notice');
    })->middleware('signed')->name('verification.verify');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Define routes for student and teacher dashboards
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        // Redirect to teacher or student dashboard based on role
        if (Auth::user()->hasRole('teacher')) {
            return redirect()->route('teacher.class'); // Redirect to teacher dashboard
        } elseif (Auth::user()->hasRole('student')) {
            return redirect()->route('student.class'); // Redirect to student dashboard
        }

        return redirect()->route('verification.notice');
    })->name('dashboard');

    // Define named routes for student and teacher dashboards
    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    })->name('student.dashboard');

    Route::post('/validate-code', [SubjectController::class, 'validateCode'])->name('validate.code');
});

//Routes for teacher role
Route::middleware(['auth', 'verified', 'role:teacher'])->group(function () {
    Route::get('/teacher/class', [SubjectController::class, 'index'])->name('teacher.class');
    Route::post('/classes', [SubjectController::class, 'store'])->name('classes.store');
    Route::get('/teacher/subject/{id}', [SubjectController::class, 'show'])->name('teacher.subject');
    Route::get('/teacher/subject/{id}', [SubjectController::class, 'show'])->name('subject.show');
    Route::delete('/teacher/subject/{id}', [SubjectController::class, 'destroy']); 
    Route::post('/teacher/subject/{id}/upload', [SubjectController::class, 'uploadFile'])->name('subject.uploadFile');
    Route::delete('/subject/{subjectId}/file/{fileId}', [SubjectController::class, 'deleteFile'])->name('subject.deleteFile');
    Route::get('/subject/{subjectId}/quiz/create', [QuizController::class, 'create'])->name('quiz.create');
    Route::post('/subject/{subjectId}/quiz/store', [QuizController::class, 'store'])->name('quiz.store');
    Route::get('/subject/{subjectId}/quiz/{quizId}', [QuizController::class, 'show'])->name('quiz.show');
    Route::get('/quiz/{subjectId}/{quizId}', [QuizController::class, 'show'])->name('quiz.show');
    Route::get('/teacher/subject/{subjectId}/quiz/results', [QuizController::class, 'showResults'])->name('quiz.result'); 
    Route::get('/teacher/subject/{subjectId}/quiz/make', [QuizController::class, 'make'])->name('quiz.make');
    Route::post('/quiz/{quizId}/question/{questionId}/update', [QuizController::class, 'updateQuestion'])->name('question.update');
    Route::delete('/quiz/{quizId}/question/{questionId}/delete', [QuizController::class, 'deleteQuestion'])->name('question.delete');
    Route::get('/teacher/subject/{subjectId}/students', [SubjectController::class, 'showStudents'])->name('teacher.students');
    Route::get('teacher/subjects/{subject}/students', [SubjectController::class, 'studentList'])->name('subject.studentList');
    Route::delete('teacher/subjects/{subject}/students/{student}', [SubjectController::class, 'removeStudent'])->name('subject.removeStudent');
    Route::delete('/teacher/subject/{subjectId}/quiz/{quizId}/delete', [QuizController::class, 'deleteQuiz'])->name('quiz.delete');
});

//Route for student role
Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Route::get('/student/class', [SubjectController::class, 'studentIndex'])->name('student.class');
    Route::get('/student/subject/{id}', [SubjectController::class, 'showSubject'])->name('student.subject');
    Route::get('subject/{subjectId}/quiz/{quizId}/take', [QuizController::class, 'takeQuiz'])->name('quiz.take');
    Route::post('subject/{subjectId}/quiz/{quizId}/submit', [QuizController::class, 'submitQuiz'])->name('quiz.submit');
    Route::get('/student/subject/{id}', [SubjectController::class, 'showSubject'])->name('student.subject');
    Route::get('/subject/{subjectId}/quiz/{quizId}/result', [QuizController::class, 'showQuizResult'])->name('quiz.student-score');
    Route::get('/subject/{subjectId}/quiz/{quizId}', [QuizController::class, 'viewQuiz'])->name('quiz.view');
    Route::get('/subject/{subjectId}/quiz', function ($subjectId) {
        // Find the subject using the ID passed in the URL
        $subject = Subject::findOrFail($subjectId);
        
        // Return the view 'view-quiz' and pass the subject to the view
        return view('student/view-quiz', compact('subject'));
    })->name('subject.quiz');
});

Route::get('/subject/{subjectId}/file/{fileId}/download', [SubjectController::class, 'downloadFile'])->name('subject.downloadFile');
Route::get('/subject/{subjectId}/file/{fileId}/view', [SubjectController::class, 'viewFile'])->name('subject.viewFile');
Route::get('/quiz/{subjectId}/{quizId}/score', [QuizController::class, 'showScore'])->name('quiz.score');




require __DIR__.'/auth.php';
