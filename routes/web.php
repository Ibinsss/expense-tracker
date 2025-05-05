<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseBreakdownController;

// Redirect root to /dashboard
Route::get('/', fn() => redirect('/dashboard'));

// Dashboard (only for verified users)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth','verified'])
    ->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Expense CRUD
    Route::resource('expenses', ExpenseController::class);

    // Profile
    Route::get('/profile',   [ProfileController::class,'edit'])   ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class,'update']) ->name('profile.update');
    Route::delete('/profile',[ProfileController::class,'destroy'])->name('profile.destroy');

    // Monthly breakdown pages
    Route::get( '/expenses/month/{month}',        [ExpenseController::class,          'showMonthlyBreakdown'])
         ->name('expenses.breakdown');
    Route::get( '/expenses/breakdown/{month}',    [ExpenseBreakdownController::class, 'show'])
         ->name('expenses.breakdown');
    Route::post('/expenses/breakdown/{month}/email',[ExpenseBreakdownController::class,'email'])
         ->name('expenses.breakdown.email');
});

// Standard auth routes (login/register/etc)
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/

// 1) Show “please verify” notice
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 2) Handle the email verification link
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth','signed'])->name('verification.verify');

// 3) Resend the verification email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth','throttle:6,1'])->name('verification.send');
