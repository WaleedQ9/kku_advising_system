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
        // 1. إحصائيات عامة للنظام
        $stats = [
            'total_students' => \App\Models\Student::count(),
            'new_students'   => \App\Models\Student::where('total_credits', 0)->count(),
            'at_risk'        => \App\Models\Student::where('gpa', '<', 2.0)->where('total_credits', '>', 0)->count(),
        ];

        // 2. إحصائيات تفصيلية لكل قسم
        $departmentsStats = \App\Models\Department::withCount('students')
            ->get()
            ->map(function ($dept) {
                return [
                    'name' => $dept->name_ar,
                    'code' => $dept->code,
                    'count' => $dept->students_count,
                    // حساب الطلاب الجدد في هذا القسم
                    'new' => \App\Models\Student::where('department_id', $dept->id)->where('total_credits', 0)->count(),
                    // حساب متوسط المعدل في القسم
                    'avg_gpa' => number_format(\App\Models\Student::where('department_id', $dept->id)->avg('gpa'), 2),
                ];
            });

        // 3. الاستعلام الأساسي للجدول (مع الفلاتر الحالية)
        $query = \App\Models\Student::query();
        $query = Student::query();
        // 1. البحث بالنص (اسم أو رقم)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('student_id', 'like', '%' . $request->search . '%')
                    ->orWhere('name_ar', 'like', '%' . $request->search . '%');
            });
        }

        // 2. الفلترة حسب القسم
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // 3. الفلترة حسب الحالة (منتظم، خريج، متعثر)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة الطلاب الجدد (الذين ساعاتهم تساوي 0)
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
        // جلب المواد التابعة لقسم الطالب أو المواد العامة
        // والتي لم يقم الطالب بتسجيلها مسبقاً
        $registeredCourseIds = $student->courses()->pluck('courses.id')->toArray();

        $availableCourses = Course::where(function ($q) use ($student) {
            $q->where('department_id', $student->department_id)
                ->orWhere('level_type', 'عام');
        })
            ->whereNotIn('id', $registeredCourseIds)
            ->get();

        return view('registrar.students.enroll', compact('student', 'availableCourses'));
    }


    public function storeEnrollment(Request $request, Student $student)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        // 1. جلب بيانات المادة قبل الحاقها لمعرفة عدد ساعاتها
        $course = Course::find($request->course_id);

        // 2. التحقق من أن الطالب لم يسجل المادة مسبقاً (حماية إضافية)
        if ($student->courses()->where('course_id', $request->course_id)->exists()) {
            return back()->with('error', 'هذا الطالب مسجل في المادة مسبقاً');
        }

        // 3. عملية الـ Attach في الجدول الوسيط
        $student->courses()->attach($request->course_id, [
            'current_grade' => 0,
            'absences_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. تحديث إجمالي الساعات في جدول الطالب
        // نزيد الساعات القديمة + ساعات المادة الجديدة
        $student->increment('total_credits', $course->credits);

        return redirect()->route('registrar.students.index')
            ->with('success', "تم تسجيل مادة ({$course->name}) بنجاح وتحديث ساعات الطالب.");
    }
}
