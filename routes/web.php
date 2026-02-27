
<?php

use App\Http\Controllers\AdvisingNoteController;
use App\Http\Controllers\Students;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    } else {
        return redirect()->route('login');
    }
});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

route::controller(Students::class)->group(function () {
    Route::get('/students', 'index')->name('students.index');
    Route::get('/students/{student}', 'show')->name('students.show');
});


route::controller(AdvisingNoteController::class)->group(function () {
    Route::post('/notes', 'store')->name('notes.store');
});
