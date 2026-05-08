<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $cs  = Department::where('code', 'CS')->first();
        $cys = Department::where('code', 'CYS')->first();
        $is  = Department::where('code', 'IS')->first();
        $cen = Department::where('code', 'CEN')->first();

        $advisorCs  = User::role('advisor')->where('department_id', $cs->id)->first();
        $advisorCys = User::role('advisor')->where('department_id', $cys->id)->first();
        $advisorIs  = User::role('advisor')->where('department_id', $is->id)->first();
        $advisorCen = User::role('advisor')->where('department_id', $cen->id)->first();

        $students = [


            ['student_id' => '441000001', 'name_ar' => 'محمد عبدالله الغامدي',  'name_en' => 'Mohammed Abdullah Al-Ghamdi',  'gpa' => 3.85, 'total_credits' => 90,  'dept' => $cs,  'advisor' => $advisorCs],
            ['student_id' => '441000002', 'name_ar' => 'فيصل سعد القحطاني',     'name_en' => 'Faisal Saad Al-Qahtani',       'gpa' => 3.50, 'total_credits' => 72,  'dept' => $cs,  'advisor' => $advisorCs],
            ['student_id' => '441000003', 'name_ar' => 'عمر خالد الزهراني',     'name_en' => 'Omar Khalid Al-Zahrani',       'gpa' => 2.95, 'total_credits' => 54,  'dept' => $cs,  'advisor' => $advisorCs],
            ['student_id' => '441000004', 'name_ar' => 'يوسف ناصر العتيبي',     'name_en' => 'Yousef Nasser Al-Otaibi',      'gpa' => 2.40, 'total_credits' => 36,  'dept' => $cs,  'advisor' => $advisorCs],
            ['student_id' => '441000005', 'name_ar' => 'تركي إبراهيم الشهري',   'name_en' => 'Turki Ibrahim Al-Shehri',      'gpa' => 3.10, 'total_credits' => 54,  'dept' => $cs,  'advisor' => $advisorCs],
            ['student_id' => '441000006', 'name_ar' => 'سلطان مشعل الدوسري',    'name_en' => 'Sultan Meshal Al-Dosari',      'gpa' => 1.75, 'total_credits' => 18,  'dept' => $cs,  'advisor' => $advisorCs],
            ['student_id' => '441000007', 'name_ar' => 'بندر عواض المطيري',     'name_en' => 'Bandar Awad Al-Mutairi',       'gpa' => 1.60, 'total_credits' => 36,  'dept' => $cs,  'advisor' => $advisorCs],
            ['student_id' => '441000008', 'name_ar' => 'حسن علي السبيعي',       'name_en' => 'Hassan Ali Al-Subaie',         'gpa' => 2.80, 'total_credits' => 54,  'dept' => $cs,  'advisor' => $advisorCs],
            ['student_id' => '441000009', 'name_ar' => 'وليد عبدالرحمن الحربي', 'name_en' => 'Waleed AbdulRahman Al-Harbi', 'gpa' => 3.20, 'total_credits' => 72,  'dept' => $cs,  'advisor' => $advisorCs],
            ['student_id' => '441000010', 'name_ar' => 'أحمد سعود البقمي',      'name_en' => 'Ahmed Saud Al-Baqami',        'gpa' => 1.90, 'total_credits' => 18,  'dept' => $cs,  'advisor' => $advisorCs],


            ['student_id' => '441000011', 'name_ar' => 'ريم سلمان العمري',      'name_en' => 'Reem Salman Al-Omari',         'gpa' => 4.20, 'total_credits' => 90,  'dept' => $cys, 'advisor' => $advisorCys],
            ['student_id' => '441000012', 'name_ar' => 'نورا فهد آل سعود',      'name_en' => 'Noura Fahad Al-Saud',          'gpa' => 3.75, 'total_credits' => 72,  'dept' => $cys, 'advisor' => $advisorCys],
            ['student_id' => '441000013', 'name_ar' => 'هند محمد الراشد',       'name_en' => 'Hind Mohammed Al-Rashed',      'gpa' => 2.60, 'total_credits' => 54,  'dept' => $cys, 'advisor' => $advisorCys],
            ['student_id' => '441000014', 'name_ar' => 'دانا عبدالله الخالدي',  'name_en' => 'Dana Abdullah Al-Khalidi',     'gpa' => 1.85, 'total_credits' => 18,  'dept' => $cys, 'advisor' => $advisorCys],
            ['student_id' => '441000015', 'name_ar' => 'لمى عمر الجهني',        'name_en' => 'Lama Omar Al-Johani',          'gpa' => 3.40, 'total_credits' => 54,  'dept' => $cys, 'advisor' => $advisorCys],
            ['student_id' => '441000016', 'name_ar' => 'سارة يوسف المالكي',     'name_en' => 'Sara Yousef Al-Malki',         'gpa' => 2.15, 'total_credits' => 36,  'dept' => $cys, 'advisor' => $advisorCys],
            ['student_id' => '441000017', 'name_ar' => 'منيرة حسن القرني',      'name_en' => 'Munira Hassan Al-Qarni',       'gpa' => 1.50, 'total_credits' => 18,  'dept' => $cys, 'advisor' => $advisorCys],
            ['student_id' => '441000018', 'name_ar' => 'أميرة خالد الشمري',     'name_en' => 'Amira Khalid Al-Shammari',     'gpa' => 3.90, 'total_credits' => 72,  'dept' => $cys, 'advisor' => $advisorCys],
            ['student_id' => '441000019', 'name_ar' => 'وفاء ناصر الغامدي',     'name_en' => 'Wafa Nasser Al-Ghamdi',        'gpa' => 2.70, 'total_credits' => 54,  'dept' => $cys, 'advisor' => $advisorCys],
            ['student_id' => '441000020', 'name_ar' => 'بسمة سعد العتيبي',      'name_en' => 'Basma Saad Al-Otaibi',         'gpa' => 1.95, 'total_credits' => 36,  'dept' => $cys, 'advisor' => $advisorCys],


            ['student_id' => '441000021', 'name_ar' => 'راشد سالم القحطاني',    'name_en' => 'Rashed Salem Al-Qahtani',      'gpa' => 3.60, 'total_credits' => 72,  'dept' => $is,  'advisor' => $advisorIs],
            ['student_id' => '441000022', 'name_ar' => 'ماجد فهد الزهراني',     'name_en' => 'Majed Fahd Al-Zahrani',        'gpa' => 2.30, 'total_credits' => 36,  'dept' => $is,  'advisor' => $advisorIs],
            ['student_id' => '441000023', 'name_ar' => 'عبدالعزيز تركي الشهري', 'name_en' => 'Abdulaziz Turki Al-Shehri',    'gpa' => 1.70, 'total_credits' => 18,  'dept' => $is,  'advisor' => $advisorIs],
            ['student_id' => '441000024', 'name_ar' => 'نايف علي الدوسري',      'name_en' => 'Naif Ali Al-Dosari',           'gpa' => 3.10, 'total_credits' => 54,  'dept' => $is,  'advisor' => $advisorIs],
            ['student_id' => '441000025', 'name_ar' => 'فواز سلمان العنزي',     'name_en' => 'Fawaz Salman Al-Anazi',        'gpa' => 2.75, 'total_credits' => 54,  'dept' => $is,  'advisor' => $advisorIs],
            ['student_id' => '441000026', 'name_ar' => 'حمد عبدالله المطيري',   'name_en' => 'Hamad Abdullah Al-Mutairi',    'gpa' => 4.00, 'total_credits' => 90,  'dept' => $is,  'advisor' => $advisorIs],
            ['student_id' => '441000027', 'name_ar' => 'جاسم محمد الحربي',      'name_en' => 'Jasem Mohammed Al-Harbi',      'gpa' => 1.80, 'total_credits' => 18,  'dept' => $is,  'advisor' => $advisorIs],
            ['student_id' => '441000028', 'name_ar' => 'طارق ناصر السبيعي',     'name_en' => 'Tarek Nasser Al-Subaie',       'gpa' => 3.45, 'total_credits' => 72,  'dept' => $is,  'advisor' => $advisorIs],
            ['student_id' => '441000029', 'name_ar' => 'ضياء خالد البقمي',      'name_en' => 'Diaa Khalid Al-Baqami',        'gpa' => 2.55, 'total_credits' => 36,  'dept' => $is,  'advisor' => $advisorIs],
            ['student_id' => '441000030', 'name_ar' => 'وائل إبراهيم الغامدي',  'name_en' => 'Wael Ibrahim Al-Ghamdi',       'gpa' => 1.65, 'total_credits' => 18,  'dept' => $is,  'advisor' => $advisorIs],


            ['student_id' => '441000031', 'name_ar' => 'سلمى أحمد العمري',      'name_en' => 'Salma Ahmed Al-Omari',         'gpa' => 3.95, 'total_credits' => 90,  'dept' => $cen, 'advisor' => $advisorCen],
            ['student_id' => '441000032', 'name_ar' => 'غدير محمد الراشد',      'name_en' => 'Ghadir Mohammed Al-Rashed',    'gpa' => 2.90, 'total_credits' => 54,  'dept' => $cen, 'advisor' => $advisorCen],
            ['student_id' => '441000033', 'name_ar' => 'شيخة فهد الخالدي',      'name_en' => 'Sheikha Fahd Al-Khalidi',      'gpa' => 1.55, 'total_credits' => 18,  'dept' => $cen, 'advisor' => $advisorCen],
            ['student_id' => '441000034', 'name_ar' => 'نجود عمر الجهني',       'name_en' => 'Nujood Omar Al-Johani',        'gpa' => 3.30, 'total_credits' => 54,  'dept' => $cen, 'advisor' => $advisorCen],
            ['student_id' => '441000035', 'name_ar' => 'عبير يوسف المالكي',     'name_en' => 'Abeer Yousef Al-Malki',        'gpa' => 2.20, 'total_credits' => 36,  'dept' => $cen, 'advisor' => $advisorCen],
            ['student_id' => '441000036', 'name_ar' => 'تهاني حسن القرني',      'name_en' => 'Tahani Hassan Al-Qarni',       'gpa' => 3.70, 'total_credits' => 72,  'dept' => $cen, 'advisor' => $advisorCen],
            ['student_id' => '441000037', 'name_ar' => 'ميسون خالد الشمري',     'name_en' => 'Maysoon Khalid Al-Shammari',   'gpa' => 1.40, 'total_credits' => 18,  'dept' => $cen, 'advisor' => $advisorCen],
            ['student_id' => '441000038', 'name_ar' => 'رنا ناصر الغامدي',      'name_en' => 'Rana Nasser Al-Ghamdi',        'gpa' => 2.50, 'total_credits' => 36,  'dept' => $cen, 'advisor' => $advisorCen],
            ['student_id' => '441000039', 'name_ar' => 'هيفاء سعد العتيبي',     'name_en' => 'Haifa Saad Al-Otaibi',         'gpa' => 3.15, 'total_credits' => 54,  'dept' => $cen, 'advisor' => $advisorCen],
            ['student_id' => '441000040', 'name_ar' => 'لولوة سلمان الزهراني',  'name_en' => 'Lulwa Salman Al-Zahrani',      'gpa' => 1.85, 'total_credits' => 18,  'dept' => $cen, 'advisor' => $advisorCen],
        ];

        foreach ($students as $data) {
            $academicStatus = $data['gpa'] < 2.0 ? 'Warning' : 'Regular';
            $status         = $data['gpa'] < 2.0 ? 'متعثر' : 'منتظم';

            Student::updateOrCreate(
                ['student_id' => $data['student_id']],
                [
                    'name_ar'         => $data['name_ar'],
                    'name_en'         => $data['name_en'],
                    'major'           => $data['dept']->name_ar,
                    'department_id'   => $data['dept']->id,
                    'advisor_id'      => $data['advisor']->id,
                    'gpa'             => $data['gpa'],
                    'total_credits'   => $data['total_credits'],
                    'status'          => $status,
                    'academic_status' => $academicStatus,
                ]
            );
        }
    }
}
