<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;

class StudentQuizController extends Controller
{
    public function take($subjectId, $quizId)
    {
        $quiz = Quiz::with('questions.answers')->where('subject_id', $subjectId)->findOrFail($quizId);
        return view('quiz.take', compact('quiz'));
    }

    public function submit(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        $score = 0;
        foreach ($quiz->questions as $question) {
            $answer = Answer::find($request->input("question_{$question->id}"));
            if ($answer && $answer->is_correct) {
                $score++;
            }
        }

        return view('quiz.result', compact('quiz', 'score'));
    }
}
