<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class Students extends Controller
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
            // 1. الأمان: جلب طلاب القسم التابع له المرشد فقط
            ->where('department_id', $advisor->department_id)

            // 2. الفلترة: جلب الطلاب المسجلين تحت إشراف هذا المرشد تحديداً
            ->where('advisor_id', $advisor->id)

            // 3. الأداء: جلب علاقة القسم مسبقاً لمنع استعلامات N+1
            ->with('department')

            // 4. البحث: البحث بالرقم الجامعي أو الاسم
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('student_id', 'LIKE', "%{$search}%")
                        ->orWhere('name_ar', 'LIKE', "%{$search}%")
                        ->orWhere('name_en', 'LIKE', "%{$search}%");
                });
            })

            // 5. الترتيب: الأحدث أولاً أو حسب المعدل
            ->latest()
            ->paginate(10)
            ->withQueryString();



        return view('Student.index', compact('students'));
    }

    public function show(Student $student)
    {
        // 1. الأمان: التأكد أن المرشد يتبع لنفس قسم الطالب
        if (auth()->user()->department_id !== $student->department_id) {
            abort(403, 'ليس لديك صلاحية الوصول لبيانات هذا الطالب');
        }

        // 2. جلب العلاقات المترابطة (Eager Loading)
        // نجلب القسم، المرشد، والمواد مع بيانات الجدول الوسيط (الدرجات والغياب)
        $student->load([
            'department',
            'advisor',
            'courses' => function ($query) {
                $query->withPivot('current_grade', 'absences_count');
            }
        ]);

        // 3. جلب الملاحظات مرتبة بالأحدث
        $notes = $student->advisingNotes()->with('user')->latest()->get();

        return view('student.show', compact('student', 'notes'));
    }
}
