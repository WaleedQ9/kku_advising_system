<?php

namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search  = $request->input('search');
        $advisor = auth()->user();

        $status   = $request->input('status');
        $followup = $request->boolean('followup');

        $deptQuery = Student::where('department_id', $advisor->department_id);
        $deptStats = [
            'total'        => (clone $deptQuery)->count(),
            'regular'      => (clone $deptQuery)->where('status', 'منتظم')->count(),
            'atRisk'       => (clone $deptQuery)->where('status', 'متعثر')->count(),
            'graduated'    => (clone $deptQuery)->where('status', 'خريج')->count(),
            'avgGpa'       => round((clone $deptQuery)->avg('gpa') ?? 0, 2),
            'flaggedCount' => (clone $deptQuery)->whereHas('riskFlags', fn($q) => $q->where('is_resolved', false))->count(),
            'followUpCount' => (clone $deptQuery)->whereHas('advisingNotes', fn($q) => $q->where('follow_up_required', true))->count(),
        ];

        $students = Student::query()
            ->where('department_id', $advisor->department_id)
            ->with([
                'department',
                'advisingNotes' => fn($q) => $q->with('user')->latest(),
                'riskFlags'     => fn($q) => $q->where('is_resolved', false)->latest(),
                'courses'       => fn($q) => $q->withPivot('current_grade', 'absences_count'),
                'dropActions',
            ])
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('student_id', 'LIKE', "%{$search}%")
                    ->orWhere('name_ar',   'LIKE', "%{$search}%")
                    ->orWhere('name_en',   'LIKE', "%{$search}%");
            }))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($followup, fn($q) => $q->whereHas('advisingNotes', fn($q2) => $q2->where('follow_up_required', true)))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('Student.index', compact('students', 'deptStats'));
    }

    public function print(Student $student)
    {
        if (auth()->user()->department_id !== $student->department_id) {
            abort(403);
        }

        $student->load([
            'department',
            'advisor',
            'courses'       => fn($q) => $q->withPivot('current_grade', 'absences_count'),
            'advisingNotes' => fn($q) => $q->with('user')->latest(),
            'riskFlags',
            'dropActions'   => fn($q) => $q->with('course')->latest(),
        ]);

        return view('Student.print', compact('student'));
    }

    public function show(Student $student)
    {
        if (auth()->user()->department_id !== $student->department_id) {
            abort(403, 'ليس لديك صلاحية الوصول لبيانات هذا الطالب');
        }

        $student->load([
            'department',
            'advisor',
            'courses'       => fn($q) => $q->withPivot('current_grade', 'absences_count'),
            'advisingNotes' => fn($q) => $q->with('user')->latest(),
            'riskFlags',
            'dropActions'   => fn($q) => $q->with('course')->latest(),
        ]);

        $notes = $student->advisingNotes;

        return view('Student.show', compact('student', 'notes'));
    }
}
