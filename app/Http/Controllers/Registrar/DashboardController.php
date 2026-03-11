<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 1. إحصائيات عامة
        $stats = [
            'total_students' => Student::count(),
            'new_students'   => Student::where('total_credits', 0)->count(),
            'at_risk'        => Student::where('gpa', '<', 2.0)->where('total_credits', '>', 0)->count(),
        ];

        // 2. إحصائيات الأقسام مع الروابط الذكية
        $departmentsStats = Department::withCount('students')
            ->get()
            ->map(function ($dept) {
                return [
                    'id'      => $dept->id,
                    'name'    => $dept->name_ar,
                    'code'    => $dept->code,
                    'count'   => $dept->students_count,
                    'new'     => Student::where('department_id', $dept->id)->where('total_credits', 0)->count(),
                    'avg_gpa' => number_format(Student::where('department_id', $dept->id)->avg('gpa') ?? 0, 2),
                ];
            });

        return view('registrar.dashboard.index', compact('stats', 'departmentsStats'));
    }
}
