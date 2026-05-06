<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\AdvisingNote;
use App\Models\RiskFlag;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $deptId = auth()->user()->department_id;

        // جميع المرشدين في القسم مع عدد طلابهم
        $advisors = User::role('advisor')
            ->where('department_id', $deptId)
            ->withCount('students')
            ->get();

        // إحصائيات الطلاب في القسم
        $totalStudents    = Student::where('department_id', $deptId)->count();
        $atRiskStudents   = Student::where('department_id', $deptId)
            ->where('academic_status', 'Warning')
            ->count();
        $regularStudents  = $totalStudents - $atRiskStudents;

        // مؤشرات الخطر النشطة في القسم
        $activeFlags = RiskFlag::where('is_resolved', false)
            ->whereHas('student', fn($q) => $q->where('department_id', $deptId))
            ->selectRaw('type, severity, count(*) as total')
            ->groupBy('type', 'severity')
            ->get();

        $totalActiveFlags = RiskFlag::where('is_resolved', false)
            ->whereHas('student', fn($q) => $q->where('department_id', $deptId))
            ->count();

        // آخر الملاحظات الإرشادية في القسم
        $recentNotes = AdvisingNote::with(['student', 'user'])
            ->whereHas('student', fn($q) => $q->where('department_id', $deptId))
            ->latest()
            ->take(10)
            ->get();

        // الطلاب المتعثرون في القسم
        $atRiskList = Student::where('department_id', $deptId)
            ->where('academic_status', 'Warning')
            ->with(['advisor', 'riskFlags' => fn($q) => $q->where('is_resolved', false)])
            ->orderBy('gpa')
            ->get();

        return view('chair.dashboard', compact(
            'advisors',
            'totalStudents',
            'atRiskStudents',
            'regularStudents',
            'activeFlags',
            'totalActiveFlags',
            'recentNotes',
            'atRiskList'
        ));
    }
}
