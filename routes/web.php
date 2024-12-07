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


//Route for student role


require __DIR__.'/auth.php';
