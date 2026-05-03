<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentManagementController extends Controller
{
    //autentication middleware
    public function __construct()
    {
        $this->middleware('auth');
    }



    public function index(Request $request)
    {
        $stats = [
            'total_students' => \App\Models\Student::count(),
            'new_students'   => \App\Models\Student::where('total_credits', 0)->count(),
            'at_risk'        => \App\Models\Student::where('gpa', '<', 2.0)->where('total_credits', '>', 0)->count(),
        ];

        $departmentsStats = \App\Models\Department::withCount('students')
            ->get()
            ->map(function ($dept) {
                return [
                    'name' => $dept->name_ar,
                    'code' => $dept->code,
                    'count' => $dept->students_count,
                    'new' => \App\Models\Student::where('department_id', $dept->id)->where('total_credits', 0)->count(),
                    'avg_gpa' => number_format(\App\Models\Student::where('department_id', $dept->id)->avg('gpa'), 2),
                ];
            });

        $query = \App\Models\Student::query();
        $query = Student::query();
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('student_id', 'like', '%' . $request->search . '%')
                    ->orWhere('name_ar', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('new_students')) {
            $query->where('total_credits', 0);
        }
        $students = $query->with('department')
            ->latest()
            ->paginate(10)
            ->withQueryString();


        $departments = \App\Models\Department::all();
        return view('registrar.students.index', compact('students', 'departments', 'stats', 'departmentsStats'));
    }


    public function createEnrollment(Student $student)
    {

        $registeredCourses = $student->courses()->with('department')->get();


        $registeredCourseIds = $registeredCourses->pluck('id')->toArray();
        $availableCourses = Course::where(function ($q) use ($student) {
            $q->where('department_id', $student->department_id)
                ->orWhere('level_type', 'عام');
        })
            ->whereNotIn('id', $registeredCourseIds)
            ->get();


        return view('registrar.students.enroll', compact('student', 'availableCourses', 'registeredCourses'));
    }


    public function storeEnrollment(Request $request, Student $student)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::find($request->course_id);

        if ($student->courses()->where('course_id', $request->course_id)->exists()) {
            return back()->with('error', 'هذا الطالب مسجل في المادة مسبقاً');
        }

        $student->courses()->attach($request->course_id, [
            'current_grade' => 0,
            'absences_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student->increment('total_credits', $course->credits);

        return back()->with('success', "تم تسجيل مادة ({$course->name}) بنجاح وتحديث ساعات الطالب.");
    }
}
