<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    public function teacher()
     {
         return $this->belongsTo(User::class, 'teacher_id');
     }

     public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
