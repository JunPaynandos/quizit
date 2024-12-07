<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['subject_name', 'teacher_id'];

     public function teacher()
     {
         return $this->belongsTo(User::class, 'teacher_id');
     }

     public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_subject', 'subject_id', 'student_id');
    }
}
