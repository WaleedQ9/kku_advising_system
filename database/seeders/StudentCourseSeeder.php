<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentCourseSeeder extends Seeder
{
    private function gradeFromGpa(float $gpa): int
    {
        if ($gpa >= 3.75) return rand(90, 100);
        if ($gpa >= 3.0)  return rand(80, 89);
        if ($gpa >= 2.5)  return rand(70, 79);
        if ($gpa >= 2.0)  return rand(60, 69);
        return rand(40, 59); // متعثر
    }


    private function absencesFromGpa(float $gpa): int
    {
        if ($gpa >= 2.0) return 0;
        if ($gpa >= 1.7) return 2;
        return 4;
    }

    public function run(): void
    {

        $generalMandatory = Course::where('level_type', 'عام')
            ->where('requirement_type', 'اجباري')
            ->get();

        $generalElective = Course::where('level_type', 'عام')
            ->where('requirement_type', 'اختياري')
            ->get();



        $students = Student::with('department')->get();

        foreach ($students as $student) {
            $deptId = $student->department_id;
            $gpa    = $student->gpa;



            $deptMandatory = Course::where('department_id', $deptId)
                ->where('level_type', 'تخصص')
                ->where('requirement_type', 'اجباري')
                ->get();



            $deptElective = Course::where('department_id', $deptId)
                ->where('level_type', 'تخصص')
                ->where('requirement_type', 'اختياري')
                ->get(); // 1 مادة × 3 ساعات


            $selectedGenMandatory = $generalMandatory->shuffle()->take(3); // 8 ساعات

            $selectedDeptMandatory = $deptMandatory; // 6 ساعات

            $currentCredits = $selectedGenMandatory->sum('credits') + $selectedDeptMandatory->sum('credits');

            $availableElectives = $generalElective->merge($deptElective)->shuffle();

            $selectedElectives = collect();
            foreach ($availableElectives as $elective) {
                if ($currentCredits >= 15 && $currentCredits + $elective->credits > 24) break;
                if ($currentCredits >= 24) break;
                $selectedElectives->push($elective);
                $currentCredits += $elective->credits;
                if ($currentCredits >= 18) break;
            }

            $allCourses = $selectedGenMandatory
                ->merge($selectedDeptMandatory)
                ->merge($selectedElectives);

            foreach ($allCourses as $course) {
                $student->courses()->syncWithoutDetaching([
                    $course->id => [
                        'current_grade'  => $this->gradeFromGpa($gpa),
                        'absences_count' => $this->absencesFromGpa($gpa),
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]
                ]);
            }
        }
    }
}
