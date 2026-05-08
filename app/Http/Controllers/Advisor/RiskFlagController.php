<?php

namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;
use App\Models\RiskFlag;
use App\Models\Student;
use Illuminate\Http\Request;

class RiskFlagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     */
    public function index(Student $student)
    {
        if (auth()->user()->department_id !== $student->department_id) {
            abort(403);
        }

        $flags = $student->riskFlags()->latest()->get();

        return response()->json($flags);
    }

    /**
     */
    public function resolve(Request $request, RiskFlag $riskFlag)
    {
        $student = $riskFlag->student;
        if (auth()->user()->department_id !== $student->department_id) {
            abort(403);
        }

        $request->validate([
            'advisor_note' => 'nullable|string',
        ]);

        $riskFlag->resolve($request->advisor_note ?? '');

        return back()->with('success', 'تم حل التنبيه بنجاح.');
    }

    /**
     */
    public function scan()
    {
        $advisor  = auth()->user();
        $students = $advisor->students()->with('courses')->get();
        $generated = 0;

        foreach ($students as $student) {
            $before = $student->riskFlags()->where('is_resolved', false)->count();
            RiskFlag::triggerAlert($student);
            $after  = $student->riskFlags()->where('is_resolved', false)->count();
            $generated += max(0, $after - $before);
        }

        return back()->with('success', "تم الفحص. تم توليد {$generated} تنبيه جديد.");
    }
}
