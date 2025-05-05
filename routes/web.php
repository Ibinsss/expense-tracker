<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseBreakdownController;

// Redirect root to /dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Dashboard page (shows greeting + recent expenses)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Grouped routes that require authentication
Route::middleware(['auth'])->group(function () {

    // Expense CRUD routes
    Route::resource('expenses', ExpenseController::class);

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Monthly expense grouping
    Route::get('/expenses/month/{month}', [ExpenseController::class, 'showMonthlyBreakdown'])
        ->name('expenses.breakdown');

    // Monthly breakdown with chart view
    Route::get('/expenses/breakdown/{month}', [ExpenseBreakdownController::class, 'show'])
        ->name('expenses.breakdown');

    // Email breakdown POST route
    Route::post('/expenses/breakdown/{month}/email', [ExpenseBreakdownController::class, 'email'])
        ->name('expenses.breakdown.email');
});

// Auth scaffolding (login, register, etc.)
require __DIR__.'/auth.php';
