<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // جلب معرفات الأقسام لتسهيل الربط
        $csDept = Department::where('code', 'CS')->first()->id;
        $cysDept = Department::where('code', 'CYS')->first()->id;
        $ltDept = Department::where('code', 'LT')->first()->id;

        $courses = [
            // مواد قسم علوم الحاسب - تخصص
            [
                'code' => 'CS111',
                'name' => 'أساسيات البرمجة',
                'credits' => 4,
                'department_id' => $csDept,
                'level_type' => 'تخصص',
                'requirement_type' => 'اجباري'
            ],
            [
                'code' => 'CS212',
                'name' => 'هياكل البيانات',
                'credits' => 3,
                'department_id' => $csDept,
                'level_type' => 'تخصص',
                'requirement_type' => 'اجباري'
            ],
            [
                'code' => 'CS490',
                'name' => 'ذكاء اصطناعي (اختياري)',
                'credits' => 3,
                'department_id' => $csDept,
                'level_type' => 'تخصص',
                'requirement_type' => 'اختياري'
            ],

            // مواد الأمن السيبراني
            [
                'code' => 'CYS201',
                'name' => 'مقدمة في التشفير',
                'credits' => 3,
                'department_id' => $cysDept,
                'level_type' => 'تخصص',
                'requirement_type' => 'اجباري'
            ],

            // مواد عامة (تتبع أقسامها ولكن تصنيفها "عام")
            [
                'code' => 'IC101',
                'name' => 'الثقافة الإسلامية 1',
                'credits' => 2,
                'department_id' => $csDept,
                'level_type' => 'عام',
                'requirement_type' => 'اجباري'
            ],
            [
                'code' => 'ENGL101',
                'name' => 'اللغة الإنجليزية 1',
                'credits' => 3,
                'department_id' => $ltDept,
                'level_type' => 'عام',
                'requirement_type' => 'اجباري'
            ],
            [
                'code' => 'ARAB101',
                'name' => 'التحرير العربي',
                'credits' => 2,
                'department_id' => $ltDept,
                'level_type' => 'عام',
                'requirement_type' => 'اجباري'
            ],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(['code' => $course['code']], $course);
        }
    }
}
