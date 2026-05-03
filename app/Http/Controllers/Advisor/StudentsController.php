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

        $students = Student::query()
            ->where('department_id', $advisor->department_id)
            ->with([
                'department',
                'advisingNotes' => fn($q) => $q->with('user')->latest(),
                'riskFlags'     => fn($q) => $q->where('is_resolved', false)->latest(),
                'courses',
            ])
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('student_id', 'LIKE', "%{$search}%")
                   ->orWhere('name_ar',   'LIKE', "%{$search}%")
                   ->orWhere('name_en',   'LIKE', "%{$search}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Student.index', compact('students'));
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
        // يسمح لأي مرشد من نفس القسم
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
