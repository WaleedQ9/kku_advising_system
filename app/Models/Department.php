<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //
    protected $fillable = [

        "name_ar",
        "name_en",
        "code"
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function users()
    { // المرشدين
        return $this->hasMany(User::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
