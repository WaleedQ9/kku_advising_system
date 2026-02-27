<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class Students extends Controller
{
    //
    public function index(Request $request)
    {

        $search = $request->input('search');

        $students = Student::where('advisor_id', auth()->id())
            ->when($search, function ($query) use ($search) {

                return $query->where(function ($q) use ($search) {
                    $q->where('student_id', 'LIKE', "%{$search}%")
                        ->orWhere('name_ar', 'LIKE', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();



        return view('Student.index', compact('students'));
    }

    public function show(Student $student)
    {
        $notes = $student->notes()->latest()->get();
        return view('student.show', compact('student', 'notes'));
    }
}
