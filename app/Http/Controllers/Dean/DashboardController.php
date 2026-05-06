<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\AdvisingNote;
use App\Models\Department;
use App\Models\RiskFlag;
use App\Models\Student;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // إحصائيات على مستوى الكلية كاملة
        $totalStudents  = Student::count();
        $atRiskStudents = Student::where('academic_status', 'Warning')->count();
        $totalAdvisors  = User::role('advisor')->count();
        $totalFlags     = RiskFlag::where('is_resolved', false)->count();

        // توزيع الطلاب والمعدلات لكل قسم
        $departments = Department::withCount([
            'students',
            'students as at_risk_count' => fn($q) => $q->where('academic_status', 'Warning'),
        ])
        ->with(['students' => fn($q) => $q->select('department_id', 'gpa')])
        ->get()
        ->map(function ($dept) {
            $dept->avg_gpa = round($dept->students->avg('gpa') ?? 0, 2);
            return $dept;
        });

        // مؤشرات الخطر مجمّعة لكل قسم
        $flagsByDept = RiskFlag::where('is_resolved', false)
            ->with('student:id,department_id')
            ->get()
            ->groupBy(fn($f) => $f->student?->department_id);

        // نشاط الإرشاد (عدد الملاحظات لكل قسم)
        $notesByDept = AdvisingNote::with('student:id,department_id')
            ->get()
            ->groupBy(fn($n) => $n->student?->department_id);

        // توزيع المعدل الكلي (GPA buckets)
        $gpaBuckets = [
            '4.5 - 5.0' => Student::whereBetween('gpa', [4.5, 5.0])->count(),
            '3.5 - 4.4' => Student::whereBetween('gpa', [3.5, 4.49])->count(),
            '2.5 - 3.4' => Student::whereBetween('gpa', [2.5, 3.49])->count(),
            '2.0 - 2.4' => Student::whereBetween('gpa', [2.0, 2.49])->count(),
            'أقل من 2.0' => Student::where('gpa', '<', 2.0)->count(),
        ];

        return view('dean.dashboard', compact(
            'totalStudents',
            'atRiskStudents',
            'totalAdvisors',
            'totalFlags',
            'departments',
            'flagsByDept',
            'notesByDept',
            'gpaBuckets'
        ));
    }
}
