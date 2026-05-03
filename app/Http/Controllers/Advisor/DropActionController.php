<?php

namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\DropAction;
use App\Models\Student;
use Illuminate\Http\Request;

class DropActionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * عرض صفحة فحص أهلية الحذف قبل التنفيذ
     */
    public function check(Student $student, Course $course)
    {
        // تحقق أن المرشد مسؤول عن هذا الطالب
        if (auth()->id() !== $student->advisor_id) {
            abort(403);
        }

        $eligibility = $student->checkDropEligibility($course->id);

        return response()->json($eligibility);
    }

    /**
     * تنفيذ حذف المادة
     */
    public function store(Request $request, Student $student)
    {
        if (auth()->id() !== $student->advisor_id) {
            abort(403);
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'reason'    => 'required|string|min:10',
        ]);

        $course = Course::findOrFail($request->course_id);

        // تحقق أن الطالب مسجل فيها
        if (!$student->courses()->where('course_id', $course->id)->exists()) {
            return back()->with('error', 'الطالب غير مسجل في هذه المادة.');
        }

        $dropAction = DropAction::executeDrop($student, $course, auth()->user(), $request->reason);
        $dropAction->logTransaction();

        if ($dropAction->status === 'Completed') {
            return back()->with('success', "تم حذف مادة ({$course->name}) بنجاح.");
        }

        return back()->with('error', $dropAction->eligibility_check_result['reason'] ?? 'لا يمكن تنفيذ الحذف.');
    }
}
