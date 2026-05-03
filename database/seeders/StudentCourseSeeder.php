<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $students = Student::all();
        $courses = Course::all();

        foreach ($students as $student) {
            $majorCourses = Course::where('department_id', $student->department_id)
                ->where('level_type', 'تخصص')
                ->get();

            $generalCourses = Course::where('level_type', 'عام')->get();

            $selectedCourses = $majorCourses->random(min(2, $majorCourses->count()))
                ->merge($generalCourses->random(min(2, $generalCourses->count())));

            foreach ($selectedCourses as $course) {
                $student->courses()->attach($course->id, [
                    'current_grade' => rand(60, 100),
                    'absences_count' => rand(0, 20),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
