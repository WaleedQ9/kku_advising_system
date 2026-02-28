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
            // 1. جلب مواد القسم التابع له الطالب (مواد التخصص)
            $majorCourses = Course::where('department_id', $student->department_id)
                ->where('level_type', 'تخصص')
                ->get();

            // 2. جلب المواد العامة (التي تظهر لجميع الأقسام)
            $generalCourses = Course::where('level_type', 'عام')->get();

            // 3. اختيار مادتين تخصص ومادتين عامة لكل طالب بشكل عشوائي
            $selectedCourses = $majorCourses->random(min(2, $majorCourses->count()))
                ->merge($generalCourses->random(min(2, $generalCourses->count())));

            foreach ($selectedCourses as $course) {
                $student->courses()->attach($course->id, [
                    'current_grade' => rand(60, 100), // درجة من 100
                    'absences_count' => rand(0, 20),  // غيابات لاختبار الحرمان (DN)
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
