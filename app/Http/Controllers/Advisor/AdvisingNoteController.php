<?php

namespace App\Http\Controllers\Advisor;

use App\Models\AdvisingNote;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdvisingNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'        => 'required|exists:students,id',
            'note_type'         => 'required|in:Academic,Behavioral',
            'title'             => 'nullable|string|max:255',
            'content'           => 'required|string|min:10',
            'follow_up_required'=> 'nullable|boolean',
        ]);

        AdvisingNote::createNote([
            'student_id'        => $request->student_id,
            'user_id'           => auth()->id(),
            'title'             => $request->title,
            'note_type'         => $request->note_type,
            'type'              => $request->note_type, // توافق عكسي
            'content'           => $request->content,
            'follow_up_required'=> $request->boolean('follow_up_required'),
        ]);

        return back()->with('success', 'تم حفظ الملاحظة الإرشادية بنجاح');
    }

    public function markFollowUpDone(AdvisingNote $note)
    {
        if (auth()->user()->department_id !== $note->student->department_id) {
            abort(403);
        }

        // نطفئ follow_up_required على جميع ملاحظات هذا الطالب التي تحتاج متابعة
        AdvisingNote::where('student_id', $note->student_id)
            ->where('follow_up_required', true)
            ->update(['follow_up_required' => false]);

        return back()->with('success', 'تم تحديد المتابعة كمكتملة');
    }
}