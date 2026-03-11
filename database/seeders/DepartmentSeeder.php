<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $departments = [
            // --- جهة إدارية (لا يسجل فيها طلاب) ---
            [
                'name_ar' => 'عمادة القبول والتسجيل',
                'name_en' => 'Admissions and Registration Office',
                'code'    => 'AR'
            ],

            // --- الأقسام الأكاديمية (التي يسجل فيها الطلاب) ---
            [
                'name_ar' => 'قسم علوم الحاسب',
                'name_en' => 'Computer Science Department',
                'code'    => 'CS'
            ],
            [
                'name_ar' => 'قسم نظم المعلومات',
                'name_en' => 'Information Systems Department',
                'code'    => 'IS'
            ],
            [
                'name_ar' => 'قسم هندسة الحاسب',
                'name_en' => 'Computer Engineering Department',
                'code'    => 'CEN'
            ],
            [
                'name_ar' => 'قسم الأمن السيبراني',
                'name_en' => 'Cybersecurity Department',
                'code'    => 'CYS'
            ],
            [
                'name_ar' => 'قسم اللغات والترجمة',
                'name_en' => 'Languages and Translation Department',
                'code'    => 'LT'
            ],
            [
                'name_ar' => 'قسم المناهج وطرق التدريس',
                'name_en' => 'Curriculum and Instruction Department',
                'code'    => 'EDU'
            ],

            // --- الأقسام المساندة (توفر مواد عامة فقط) ---
            [
                'name_ar' => 'قسم الدراسات الإسلامية',
                'name_en' => 'Islamic Studies Department',
                'code'    => 'ISL' // المالك لمواد IC
            ],
            [
                'name_ar' => 'قسم اللغة العربية',
                'name_en' => 'Arabic Language Department',
                'code'    => 'ARB' // المالك لمواد ARAB
            ],
            [
                'name_ar' => 'قسم الرياضيات',
                'name_en' => 'Mathematics Department',
                'code'    => 'MATH' // المالك لمواد MATH
            ],
            [
                'name_ar' => 'قسم اللغة الإنجليزية',
                'name_en' => 'English Language Center',
                'code'    => 'ENG' // المالك لمواد ENG
            ]
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['code' => $dept['code']], $dept);
        }
    }
}
