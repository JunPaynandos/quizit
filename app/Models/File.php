<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['file_name', 'file_path', 'subject_id'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
