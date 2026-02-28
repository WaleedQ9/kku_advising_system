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
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['code' => $dept['code']], $dept);
        }
    }
}
