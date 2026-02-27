<?php

namespace App\Http\Controllers;

use App\Models\AdvisingNote;
use Illuminate\Http\Request;

class AdvisingNoteController extends Controller
{
    //
    // app/Http/Controllers/AdvisingNoteController.php

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
