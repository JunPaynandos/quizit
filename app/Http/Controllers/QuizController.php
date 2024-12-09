<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Subject;
use App\Models\QuizResult;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function create($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);
        return view('quiz.create', compact('subject'));
    }

    public function make($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);
        return view('teacher.make-quiz', compact('subject'));
    }

    public function store(Request $request, $subjectId)
    {
        \Log::info('Quiz submission data:', $request->all());
    
        // Validate quiz data
        $request->validate([
            'quiz_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'num_questions' => 'required|integer|min:1',
        ]);
    
        // Create the quiz
        $quiz = Quiz::create([
            'quiz_name' => $request->quiz_name,
            'description' => $request->description,
            'subject_id' => $subjectId,
        ]);
    
        for ($i = 0; $i < $request->num_questions; $i++) {
            $questionText = $request->input("questions.$i.text");
            if (empty($questionText)) {
                return redirect()->back()->withErrors(['questions.' . $i . '.text' => 'Question text cannot be empty.']);
            }
    
            $question = Question::create([
                'question_text' => $questionText,
                'question_type' => $request->input("questions.$i.type"),
                'quiz_id' => $quiz->id,
            ]);
    
            $answerData = [];
    
            $questionType = $request->input("questions.$i.type");
            
            if ($questionType == 'multiple_choice') {
                foreach (['a', 'b', 'c', 'd'] as $choice) {
                    $choiceText = $request->input("questions.$i.choice_$choice");
                    if (empty($choiceText)) {
                        return redirect()->back()->withErrors(['questions.' . $i . '.choice_' . $choice => 'Choice cannot be empty.']);
                    }
                    // Get the selected answer from the dropdown
                    $selectedAnswer = $request->input("questions.$i.selected");
                    
                    // Determine if this choice is the correct answer based on the selected value
                    $isCorrect = ($selectedAnswer === $choice) ? 1 : 0;

                    $answerData[] = [
                        'answer_text' => $choiceText,
                        'is_correct' => $isCorrect,
                    ];
                }
            } elseif ($questionType == 'true_false') {

                $correctAnswer = $request->input("questions.$i.answer");
                $answerData[] = [
                    'answer_text' => 'True',
                    'is_correct' => $correctAnswer == 'true' ? 1 : 0,
                ];
                $answerData[] = [
                    'answer_text' => 'False',
                    'is_correct' => $correctAnswer == 'false' ? 1 : 0,
                ];
            } elseif ($questionType == 'fill_in_the_blanks') {
                $guess = $request->input("questions.$i.guess");
                
                $answerText = strtolower(trim($guess));

                $answerData[] = [
                    'answer_text' => $request->input("questions.$i.guess"),
                    'is_correct' => 1,
                ];
            }
    
            $question->answers()->createMany($answerData);
        }
    
        return redirect()->route('quiz.make', ['subjectId' => $subjectId])->with('success', 'Quiz created successfully.');
    }

    public function show($subjectId, $quizId)
    {
        $subject = Subject::findOrFail($subjectId);
        $quiz = Quiz::findOrFail($quizId); 
        return view('quiz.show', compact('subject', 'quiz'));  
    }

    public function takeQuiz($subjectId, $quizId)
    {
        $quiz = Quiz::with('questions.answers')->where('subject_id', $subjectId)->findOrFail($quizId);

        return view('quiz.take', compact('quiz'));
    } 
    
    public function submitQuiz(Request $request, $subjectId, $quizId)
    {
        $quiz = Quiz::with('questions.answers')->where('id', $quizId)->where('subject_id', $subjectId)->firstOrFail();
        $correctAnswersCount = 0;
        $answersData = [];
    
        // Loop through each question and check the user's answer
        foreach ($quiz->questions as $question) {
            $userAnswer = $request->input('answers.' . $question->id);
    
            // validation for missing answers
            if (is_null($userAnswer) || $userAnswer === '') {
                return redirect()->back()->withErrors(['answers.' . $question->id => 'Please provide an answer for question ' . ($question->id) . '.']);
            }
    
            $correctAnswer = null;
    
            if ($question->question_type == 'multiple_choice') {
                $correctAnswer = $question->answers->where('is_correct', 1)->first();
            }
            elseif ($question->question_type == 'true_false') {
                $correctAnswer = $question->answers->where('is_correct', 1)->first();
            }
            elseif ($question->question_type == 'fill_in_the_blanks') {
                $correctAnswer = $question->answers->where('is_correct', 1)->first();
                if (!$correctAnswer) {
                    $correctAnswer = $question->correct_answer;
                }
            }
    
            // for debugging
            if ($correctAnswer) {
                $correctAnswerText = is_object($correctAnswer) ? $correctAnswer->answer_text : $correctAnswer;
                \Log::info('Correct answer for Question ID ' . $question->id . ': ' . $correctAnswerText);
            } else {
                \Log::info('No correct answer found for Question ID ' . $question->id);
            }
    
            // Compare the answers for each question type
            if ($question->question_type == 'multiple_choice' && $correctAnswer) {
                \Log::info("User answer: '$userAnswer', Correct answer: '" . $correctAnswer->answer_text . "'");
                if (strtolower(trim($userAnswer)) == strtolower(trim($correctAnswer->answer_text))) {
                    $correctAnswersCount++; // Increment count for correct answer
                }
            }
            elseif ($question->question_type == 'true_false' && $correctAnswer) {
                \Log::info("User answer: '$userAnswer', Correct answer: '" . $correctAnswer->answer_text . "'");
                if (strtolower(trim($userAnswer)) == strtolower(trim($correctAnswer->answer_text))) {
                    $correctAnswersCount++;
                }
            }
            elseif ($question->question_type == 'fill_in_the_blanks') {
                \Log::info("User answer: '$userAnswer', Correct answer: '$correctAnswer'");
                $correctAnswerText = is_object($correctAnswer) ? $correctAnswer->answer_text : $correctAnswer;
                if (strtolower(trim($userAnswer)) == strtolower(trim($correctAnswerText))) {
                    $correctAnswersCount++;
                }
            }
    
            // Store the selected answer's text in the database
            $answersData[] = [
                'question_id' => $question->id,
                'user_answer' => $userAnswer,  // Store the actual answer text
            ];
        }
    
        // Store the result in the quiz_results table
        $quizResult = QuizResult::create([
            'user_id' => auth()->user()->id,
            'quiz_id' => $quizId,
            'score' => $correctAnswersCount,
        ]);
    
        // Store user's answers in the user_answers table
        foreach ($answersData as $answer) {
            DB::table('user_answers')->insert([
                'quiz_result_id' => $quizResult->id,
                'question_id' => $answer['question_id'],
                'user_answer' => $answer['user_answer'],
            ]);
        }
    
        \Log::info('User answers:', $request->input('answers'));

        return redirect()->route('student.subject', ['id' => $subjectId])
                ->with('success', 'Submitted Successfully. Check out your score!')
                ->with('quizId', $quizId);
    }
    
    public function updateQuestion(Request $request, $quizId, $questionId)
    {
        $question = Question::findOrFail($questionId);
    
        $question->update([
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
        ]);
    
        foreach ($request->answers as $answerId => $answerText) {
            $answer = Answer::find($answerId);
            if ($answer) {
                $answer->update(['answer_text' => $answerText]);
                $isCorrect = in_array($answerId, $request->correct_answers);
                $answer->update(['is_correct' => $isCorrect]);
            }
        }
    
        return redirect()->route('quiz.show', ['subjectId' => $question->quiz->subject_id, 'quizId' => $quizId])
                         ->with('success', 'Question updated successfully!');
    }   
    
    public function deleteQuestion($quizId, $questionId)
    {
        $question = Question::findOrFail($questionId);

        $question->answers()->delete();

        $question->delete();

        return redirect()->route('quiz.show', ['subjectId' => $question->quiz->subject_id, 'quizId' => $quizId])
                        ->with('success', 'Question and its answers deleted successfully!');
    }

    //score of student in quiz
    public function showQuizResult($subjectId, $quizId)
    {
        $subject = Subject::findOrFail($subjectId);
        $quiz = Quiz::findOrFail($quizId);
    
        $quizResult = QuizResult::where('quiz_id', $quizId)
                                ->where('user_id', auth()->user()->id)
                                ->first();
    
        if (!$quizResult) {
            return redirect()->route('subject.quiz', ['subjectId' => $subject->id])
                             ->with('error', 'You have not taken this quiz yet.');
        }
    
        $score = $quizResult->score;
        $totalQuestions = $quiz->questions->count();
    
        $userAnswers = UserAnswer::where('quiz_result_id', $quizResult->id)
                                 ->get();
    
        $questionResults = [];
        foreach ($quiz->questions as $question) {
            $userAnswer = $userAnswers->where('question_id', $question->id)->first();
    
            $correctAnswer = $question->answers()->where('is_correct', 1)->first();
    
            $questionResults[] = [
                'question_text' => $question->question_text,
                'correct_answer' => $correctAnswer ? $correctAnswer->answer_text : 'No correct answer',
                'user_answer' => $userAnswer ? $userAnswer->user_answer : 'No answer',
            ];
        }
    
        return view('quiz.student-score', compact('subject', 'quiz', 'score', 'totalQuestions', 'questionResults'));
    }        

    public function showResults($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);

        $quizResults = QuizResult::whereHas('quiz', function($query) use ($subjectId) {
            $query->where('subject_id', $subjectId);
        })->with('user', 'quiz')->get();

        return view('quiz.result', compact('subject', 'quizResults'));
    }

    //view the quiz for student
    public function viewQuiz($subjectId, $quizId)
    {
        $quiz = Quiz::with('questions.answers')->findOrFail($quizId);
        $subject = Subject::findOrFail($subjectId);

        return view('student.view-quiz', compact('quiz', 'subject'));
    }

    public function showScore($subjectId, $quizId)
    {
        $subject = Subject::findOrFail($subjectId);
        $quiz = Quiz::findOrFail($quizId);

        // Scores in descending order
        $results = QuizResult::where('quiz_id', $quizId)
            ->orderBy('score', 'desc')
            ->get();

        return view('quiz.score', compact('quiz', 'results'));  // Pass sorted results to the view
    }

    public function deleteQuiz($subjectId, $quizId)
    {
        $quiz = Quiz::where('id', $quizId)->where('subject_id', $subjectId)->first();

        if ($quiz) {
            $quiz->delete();

            return redirect()->route('quiz.make', ['subjectId' => $subjectId])->with('success', 'Quiz deleted successfully!');
        }

        return redirect()->route('quiz.make', ['subjectId' => $subjectId])->with('error', 'Quiz not found!');
    }
}
