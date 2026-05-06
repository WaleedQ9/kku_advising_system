<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('chair')) {
            return redirect()->route('chair.dashboard');
        }

        if ($user->hasRole('dean')) {
            return redirect()->route('dean.dashboard');
        }

        if ($user->hasRole('registrar')) {
            return redirect()->route('registrar.dashboard');
        }

        $students = Student::where('department_id', $user->department_id)->count();
        return view('home', compact('students'));
    }
}
