<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisingNote extends Model
{
    //
    protected $fillable = ['student_id', 'user_id', 'type', 'content'];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
