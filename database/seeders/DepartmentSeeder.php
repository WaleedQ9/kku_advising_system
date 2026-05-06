<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name_ar' => 'قسم علوم الحاسب',       'name_en' => 'Computer Science Department',       'code' => 'CS'],
            ['name_ar' => 'قسم الأمن السيبراني',    'name_en' => 'Cybersecurity Department',          'code' => 'CYS'],
            ['name_ar' => 'قسم نظم المعلومات',      'name_en' => 'Information Systems Department',    'code' => 'IS'],
            ['name_ar' => 'قسم هندسة الحاسب',       'name_en' => 'Computer Engineering Department',   'code' => 'CEN'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['code' => $dept['code']], $dept);
        }
    }
}
