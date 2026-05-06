<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentCourseSeeder extends Seeder
{
    // درجة عشوائية ثابتة بناءً على المعدل
    private function gradeFromGpa(float $gpa): int
    {
        if ($gpa >= 3.75) return rand(90, 100);
        if ($gpa >= 3.0)  return rand(80, 89);
        if ($gpa >= 2.5)  return rand(70, 79);
        if ($gpa >= 2.0)  return rand(60, 69);
        return rand(40, 59); // متعثر
    }

    // غياب عشوائي ثابت: المتعثرون لديهم غيابات أعلى
    private function absencesFromGpa(float $gpa): int
    {
        if ($gpa >= 3.0) return rand(0, 2);
        if ($gpa >= 2.0) return rand(1, 4);
        return rand(3, 8); // متعثر
    }

    public function run(): void
    {
        // ══════════ تجميع المواد حسب النوع ══════════
        $generalMandatory = Course::where('level_type', 'عام')
            ->where('requirement_type', 'اجباري')
            ->get(); // 5 مواد، مجموعها 13 ساعة

        $generalElective = Course::where('level_type', 'عام')
            ->where('requirement_type', 'اختياري')
            ->get(); // 3 مواد

        // ══════════ توزيع المواد لكل طالب ══════════
        $students = Student::with('department')->get();

        foreach ($students as $student) {
            $deptId = $student->department_id;
            $gpa    = $student->gpa;

            // مواد التخصص الاجبارية لقسم الطالب
            $deptMandatory = Course::where('department_id', $deptId)
                ->where('level_type', 'تخصص')
                ->where('requirement_type', 'اجباري')
                ->get(); // 2 مواد × 3 ساعة = 6 ساعات

            // مواد التخصص الاختيارية لقسم الطالب
            $deptElective = Course::where('department_id', $deptId)
                ->where('level_type', 'تخصص')
                ->where('requirement_type', 'اختياري')
                ->get(); // 1 مادة × 3 ساعات

            // حساب المواد المختارة مع ضمان 15-24 ساعة
            // القاعدة: 3 عامة اجبارية (8cr) + 2 تخصص اجباري (6cr) = 14cr كحد أدنى
            // نضيف اختياريات للوصول لـ 15-24

            // اختر 3 من المواد العامة الاجبارية
            $selectedGenMandatory = $generalMandatory->shuffle()->take(3); // 8 ساعات

            // جميع مواد التخصص الاجبارية
            $selectedDeptMandatory = $deptMandatory; // 6 ساعات

            $currentCredits = $selectedGenMandatory->sum('credits') + $selectedDeptMandatory->sum('credits');
            // currentCredits = 8 + 6 = 14

            // اجمع كل الاختياريات المتاحة
            $availableElectives = $generalElective->merge($deptElective)->shuffle();

            $selectedElectives = collect();
            foreach ($availableElectives as $elective) {
                if ($currentCredits >= 15 && $currentCredits + $elective->credits > 24) break;
                if ($currentCredits >= 24) break;
                $selectedElectives->push($elective);
                $currentCredits += $elective->credits;
                if ($currentCredits >= 18) break; // هدف معقول
            }

            // ادمج جميع المواد المختارة
            $allCourses = $selectedGenMandatory
                ->merge($selectedDeptMandatory)
                ->merge($selectedElectives);

            // أضف المواد للطالب
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
