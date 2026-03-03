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


    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student')
            ->withPivot('current_grade', 'absences_count')
            ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }



    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }



    public function department()
    {
        return $this->belongsTo(Department::class);
    }


    public function advisingNotes()
    {
        return $this->hasMany(AdvisingNote::class);
    }
}
