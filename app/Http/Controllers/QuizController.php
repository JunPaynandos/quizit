<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;

class QuizController extends Controller
{
    public function create($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);
        return view('quiz.create', compact('subject'));
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
    
        // Add questions dynamically
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
    
            // Add answers based on question type
            $answerData = [];
    
            $questionType = $request->input("questions.$i.type");
            
            if ($questionType == 'multiple_choice') {
                 // Handle multiple choice questions
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
                        'is_correct' => $isCorrect, // Correct choice set to 1, others to 0
                    ];
                }
            } elseif ($questionType == 'true_false') {
                // Handle true/false questions
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
                // Handle fill-in-the-blanks questions
                $guess = $request->input("questions.$i.guess");
                
                $answerText = strtolower(trim($guess));

                $answerData[] = [
                    'answer_text' => $request->input("questions.$i.guess"),
                    'is_correct' => 1, // Assuming this is the only correct answer
                ];
            }
    
            // Save the answers
            $question->answers()->createMany($answerData);
        }
    
        return redirect()->route('quiz.make', ['subjectId' => $subjectId])->with('success', 'Quiz created successfully.');
    }

    public function show($subjectId, $quizId)
    {
        $subject = Subject::findOrFail($subjectId);  // Get the subject by its ID
        $quiz = Quiz::findOrFail($quizId); 
        return view('quiz.show', compact('subject', 'quiz'));  
    }
}
