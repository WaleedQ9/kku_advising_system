
<?php

use App\Http\Controllers\Advisor\AdvisingNoteController;
use App\Http\Controllers\Advisor\StudentsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Registrar\DashboardController;
use App\Http\Controllers\Registrar\StudentManagementController;
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


route::controller(HomeController::class)->group(function () {
    Route::get('/home', 'index')->name('home');
});
route::controller(StudentsController::class)->group(function () {
    Route::get('/students', 'index')->name('students.index');
    Route::get('/students/{student}', 'show')->name('students.show');
});


route::controller(AdvisingNoteController::class)->group(function () {
    Route::post('/notes', 'store')->name('notes.store');
});



//



Route::middleware(['auth', 'role:registrar'])
    ->prefix('registrar')
    ->name('registrar.')
    ->group(function () {

        // إدارة الطلاب
        Route::get('/students', [StudentManagementController::class, 'index'])->name('students.index');

        // تسجيل المواد
        Route::get('/students/{student}/enroll', [StudentManagementController::class, 'createEnrollment'])->name('students.enroll');
        Route::post('/students/{student}/enroll', [StudentManagementController::class, 'storeEnrollment'])->name('students.store_enroll');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
