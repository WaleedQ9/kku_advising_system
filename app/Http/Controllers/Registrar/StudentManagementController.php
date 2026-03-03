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
        $query = Student::query();

        // البحث بالرقم الجامعي أو الاسم
        if ($request->has('search')) {
            $query->where('student_id', 'like', '%' . $request->search . '%')
                ->orWhere('name_ar', 'like', '%' . $request->search . '%');
        }

        $students = $query->with('department')->paginate(10);
        return view('registrar.students.index', compact('students'));
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
