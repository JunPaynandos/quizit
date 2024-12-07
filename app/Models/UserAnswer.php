<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAnswer extends Model
{
    use HasFactory;

    protected $table = 'user_answers';

    protected $fillable = ['quiz_result_id', 'question_id', 'user_answer'];

    //inverse relationship of QuizResult
    public function quizResult()
    {
        return $this->belongsTo(QuizResult::class);
    }

    //inverse relationship with Question
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
