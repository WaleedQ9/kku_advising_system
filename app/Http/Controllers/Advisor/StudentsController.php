<?php

namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $search = $request->input('search');
        $advisor = auth()->user();
        $students = Student::query()
            ->where('department_id', $advisor->department_id)

            ->where('advisor_id', $advisor->id)

            ->with('department')

            // 4. البحث: البحث بالرقم الجامعي أو الاسم
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('student_id', 'LIKE', "%{$search}%")
                        ->orWhere('name_ar', 'LIKE', "%{$search}%")
                        ->orWhere('name_en', 'LIKE', "%{$search}%");
                });
            })

            ->latest()
            ->paginate(10)
            ->withQueryString();



        return view('Student.index', compact('students'));
    }

    public function show(Student $student)
    {
        if (auth()->user()->department_id !== $student->department_id) {
            abort(403, 'ليس لديك صلاحية الوصول لبيانات هذا الطالب');
        }

        $student->load([
            'department',
            'advisor',
            'courses' => function ($query) {
                $query->withPivot('current_grade', 'absences_count');
            }
        ]);

        $notes = $student->advisingNotes()->with('user')->latest()->get();

        return view('student.show', compact('student', 'notes'));
    }
}
