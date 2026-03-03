<?php

namespace App\Http\Controllers\Advisor;

use App\Models\AdvisingNote;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdvisingNoteController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|string',
            'content' => 'required|string|min:10',
        ]);

        AdvisingNote::create([
            'student_id' => $request->student_id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'content' => $request->content,
        ]);

        return back()->with('success', 'تم حفظ الملاحظة الإرشادية بنجاح');
    }
}
