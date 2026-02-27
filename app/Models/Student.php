<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name_ar',
        'name_en',
        'major',
        'gpa',
        'total_credits',
        'status',
        'advisor_id'
    ];



    public function notes()
    {
        return $this->hasMany(AdvisingNote::class);
    }
}
