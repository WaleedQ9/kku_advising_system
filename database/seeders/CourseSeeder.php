<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $cs  = Department::where('code', 'CS')->first()->id;
        $cys = Department::where('code', 'CYS')->first()->id;
        $is  = Department::where('code', 'IS')->first()->id;
        $cen = Department::where('code', 'CEN')->first()->id;

        $gen = $cs;

        $courses = [
            // ══════════  اجبارية (5) ══════════
            ['code' => 'GEN101', 'name' => 'الثقافة الإسلامية',            'credits' => 2, 'department_id' => $gen, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'GEN102', 'name' => 'المهارات اللغوية العربية',      'credits' => 2, 'department_id' => $gen, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'GEN103', 'name' => 'اللغة الإنجليزية 1',           'credits' => 3, 'department_id' => $gen, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'GEN104', 'name' => 'حساب التفاضل والتكامل',        'credits' => 3, 'department_id' => $gen, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'GEN105', 'name' => 'الإحصاء والاحتمالات',          'credits' => 3, 'department_id' => $gen, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],

            // ══════════  اختيارية (3) ══════════
            ['code' => 'GEN201', 'name' => 'اللغة الإنجليزية 2',           'credits' => 3, 'department_id' => $gen, 'level_type' => 'عام', 'requirement_type' => 'اختياري'],
            ['code' => 'GEN202', 'name' => 'ريادة الأعمال والابتكار',      'credits' => 2, 'department_id' => $gen, 'level_type' => 'عام', 'requirement_type' => 'اختياري'],
            ['code' => 'GEN203', 'name' => 'مهارات التواصل والعرض',        'credits' => 2, 'department_id' => $gen, 'level_type' => 'عام', 'requirement_type' => 'اختياري'],

            // ══════════  — تخصص (3) ══════════
            ['code' => 'CS111',  'name' => 'برمجة الحاسب 1',               'credits' => 4, 'department_id' => $cs,  'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CS221',  'name' => 'تراكيب البيانات والخوارزميات', 'credits' => 3, 'department_id' => $cs,  'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CS331',  'name' => 'نظم التشغيل',                  'credits' => 3, 'department_id' => $cs,  'level_type' => 'تخصص', 'requirement_type' => 'اختياري'],

            // ══════════  — تخصص (3) ══════════
            ['code' => 'CYS201', 'name' => 'مقدمة في الأمن السيبراني',     'credits' => 3, 'department_id' => $cys, 'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CYS301', 'name' => 'التشفير وأمن المعلومات',       'credits' => 3, 'department_id' => $cys, 'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CYS401', 'name' => 'الاختراق الأخلاقي',            'credits' => 3, 'department_id' => $cys, 'level_type' => 'تخصص', 'requirement_type' => 'اختياري'],

            // ══════════  — تخصص (3) ══════════
            ['code' => 'IS201',  'name' => 'مقدمة في نظم المعلومات',       'credits' => 3, 'department_id' => $is,  'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'IS301',  'name' => 'قواعد البيانات',               'credits' => 3, 'department_id' => $is,  'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'IS401',  'name' => 'تحليل الأنظمة وتصميمها',      'credits' => 3, 'department_id' => $is,  'level_type' => 'تخصص', 'requirement_type' => 'اختياري'],

            // ══════════  — تخصص (3) ══════════
            ['code' => 'CEN201', 'name' => 'الدوائر المنطقية الرقمية',     'credits' => 3, 'department_id' => $cen, 'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CEN301', 'name' => 'معمارية الحاسب',               'credits' => 3, 'department_id' => $cen, 'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CEN401', 'name' => 'الأنظمة المدمجة',              'credits' => 3, 'department_id' => $cen, 'level_type' => 'تخصص', 'requirement_type' => 'اختياري'],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(['code' => $course['code']], $course);
        }
    }
}
