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
        // جلب المعرفات للأقسام الأكاديمية
        $csDept = Department::where('code', 'CS')->first()->id;
        $isDept = Department::where('code', 'IS')->first()->id;
        $cenDept = Department::where('code', 'CEN')->first()->id;
        $cysDept = Department::where('code', 'CYS')->first()->id;
        $ltDept = Department::where('code', 'LT')->first()->id;
        $eduDept = Department::where('code', 'EDU')->first()->id;

        // جلب المعرفات للأقسام المساندة (التي توفر المواد العامة)
        $islDept = Department::where('code', 'ISL')->first()->id;
        $arbDept = Department::where('code', 'ARB')->first()->id;
        $mathDept = Department::where('code', 'MATH')->first()->id;
        $engDept = Department::where('code', 'ENG')->first()->id;

        $courses = [
            // --- قسم الدراسات الإسلامية (ISL) - توفر مواد عامة للكل ---
            ['code' => 'IC101', 'name' => 'الثقافة الإسلامية 1', 'credits' => 2, 'department_id' => $islDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'IC102', 'name' => 'الثقافة الإسلامية 2', 'credits' => 2, 'department_id' => $islDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'IC103', 'name' => 'الثقافة الإسلامية 3', 'credits' => 2, 'department_id' => $islDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'IC104', 'name' => 'الثقافة الإسلامية 4', 'credits' => 2, 'department_id' => $islDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'ISL201', 'name' => 'النظام الاقتصادي في الإسلام', 'credits' => 2, 'department_id' => $islDept, 'level_type' => 'عام', 'requirement_type' => 'اختياري'],

            // --- قسم اللغة العربية (ARB) - توفر مواد عامة للكل ---
            ['code' => 'ARAB101', 'name' => 'المهارات اللغوية', 'credits' => 2, 'department_id' => $arbDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'ARAB103', 'name' => 'التحرير العربي', 'credits' => 2, 'department_id' => $arbDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'ARAB201', 'name' => 'الأدب العربي الحديث', 'credits' => 2, 'department_id' => $arbDept, 'level_type' => 'عام', 'requirement_type' => 'اختياري'],
            ['code' => 'ARAB202', 'name' => 'البلاغة العربية', 'credits' => 2, 'department_id' => $arbDept, 'level_type' => 'عام', 'requirement_type' => 'اختياري'],
            ['code' => 'ARAB301', 'name' => 'النحو الواضح', 'credits' => 2, 'department_id' => $arbDept, 'level_type' => 'عام', 'requirement_type' => 'اختياري'],

            // --- قسم الرياضيات (MATH) - توفر مواد مساندة ---
            ['code' => 'MATH101', 'name' => 'حساب التفاضل والتكامل 1', 'credits' => 3, 'department_id' => $mathDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'MATH102', 'name' => 'حساب التفاضل والتكامل 2', 'credits' => 3, 'department_id' => $mathDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'MATH105', 'name' => 'الجبر الخطي', 'credits' => 3, 'department_id' => $mathDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'MATH201', 'name' => 'الإحصاء والاحتمالات', 'credits' => 3, 'department_id' => $mathDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'MATH202', 'name' => 'الرياضيات المتقطعة', 'credits' => 3, 'department_id' => $mathDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],

            // --- قسم اللغة الإنجليزية (ENG) - توفر مهارات اللغة ---
            ['code' => 'ENG101', 'name' => 'اللغة الإنجليزية 1', 'credits' => 3, 'department_id' => $engDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'ENG102', 'name' => 'اللغة الإنجليزية 2', 'credits' => 3, 'department_id' => $engDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'ENG201', 'name' => 'الكتابة التقنية', 'credits' => 2, 'department_id' => $engDept, 'level_type' => 'عام', 'requirement_type' => 'اجباري'],
            ['code' => 'ENG202', 'name' => 'مهارات المحادثة', 'credits' => 2, 'department_id' => $engDept, 'level_type' => 'عام', 'requirement_type' => 'اختياري'],
            ['code' => 'ENG301', 'name' => 'الإنجليزية للأغراض الأكاديمية', 'credits' => 3, 'department_id' => $engDept, 'level_type' => 'عام', 'requirement_type' => 'اختياري'],

            // --- قسم علوم الحاسب (CS) - مواد تخصص ---
            ['code' => 'CS111', 'name' => 'برمجة الحاسب 1', 'credits' => 4, 'department_id' => $csDept, 'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CS112', 'name' => 'برمجة الحاسب 2', 'credits' => 4, 'department_id' => $csDept, 'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CS221', 'name' => 'تراكيب البيانات', 'credits' => 3, 'department_id' => $csDept, 'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CS331', 'name' => 'تصميم الخوارزميات', 'credits' => 3, 'department_id' => $csDept, 'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CS441', 'name' => 'الذكاء الاصطناعي', 'credits' => 3, 'department_id' => $csDept, 'level_type' => 'تخصص', 'requirement_type' => 'اختياري'],

            // --- قسم الأمن السيبراني (CYS) - مواد تخصص ---
            ['code' => 'CYS201', 'name' => 'مقدمة في الأمن السيبراني', 'credits' => 3, 'department_id' => $cysDept, 'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CYS301', 'name' => 'التشفير وأمن المعلومات', 'credits' => 3, 'department_id' => $cysDept, 'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CYS320', 'name' => 'أمن الشبكات', 'credits' => 3, 'department_id' => $cysDept, 'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CYS401', 'name' => 'الاختراق الأخلاقي', 'credits' => 3, 'department_id' => $cysDept, 'level_type' => 'تخصص', 'requirement_type' => 'اجباري'],
            ['code' => 'CYS410', 'name' => 'التحقيق الجنائي الرقمي', 'credits' => 3, 'department_id' => $cysDept, 'level_type' => 'تخصص', 'requirement_type' => 'اختياري'],

            // أضف بنفس الطريقة بقية الأقسام (IS, CEN, LT, EDU) لتكتمل الـ 50 مادة
        ];


        foreach ($courses as $course) {
            Course::updateOrCreate(['code' => $course['code']], $course);
        }
    }
}
