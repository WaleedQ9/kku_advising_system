<?php

use App\Http\Controllers\Advisor\AdvisingNoteController;
use App\Http\Controllers\Advisor\DropActionController;
use App\Http\Controllers\Advisor\RiskFlagController;
use App\Http\Controllers\Advisor\StudentsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Registrar\DashboardController;
use App\Http\Controllers\Registrar\StudentManagementController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('home') : redirect()->route('login');
});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
});

Auth::routes();

// ─── Home (shared) ────────────────────────────────────────────────────────────
Route::controller(HomeController::class)->group(function () {
    Route::get('/home', 'index')->name('home');
});

// ─── Advisor ──────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:advisor'])->group(function () {

    // Students
    Route::controller(StudentsController::class)->group(function () {
        Route::get('/students',          'index')->name('students.index');
        Route::get('/students/{student}','show')->name('students.show');
    });

    // Advising Notes
    Route::post('/notes', [AdvisingNoteController::class, 'store'])->name('notes.store');
    Route::post('/notes/{note}/follow-up-done', [AdvisingNoteController::class, 'markFollowUpDone'])->name('notes.followup.done');

    // Drop Actions
    Route::controller(DropActionController::class)->group(function () {
        Route::get('/students/{student}/drop/{course}/check', 'check')->name('drop.check');
        Route::post('/students/{student}/drop',               'store')->name('drop.store');
    });

    // Risk Flags
    Route::controller(RiskFlagController::class)->group(function () {
        Route::get('/students/{student}/flags',  'index')->name('flags.index');
        Route::post('/flags/{riskFlag}/resolve', 'resolve')->name('flags.resolve');
        Route::post('/flags/scan',               'scan')->name('flags.scan');
    });
});

// ─── Registrar ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:registrar'])
    ->prefix('registrar')
    ->name('registrar.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/students',                             [StudentManagementController::class, 'index'])->name('students.index');
        Route::get('/students/{student}/enroll',            [StudentManagementController::class, 'createEnrollment'])->name('students.enroll');
        Route::post('/students/{student}/enroll',           [StudentManagementController::class, 'storeEnrollment'])->name('students.store_enroll');
    });
