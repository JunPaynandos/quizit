<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizResult extends Model
{
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
    ];

    /**
     * Get the quiz associated with the result.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the user who took the quiz.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
