<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\BookController;
use App\Http\Controllers\Web\BookLoanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Book routes (all require authentication)
    Route::resource('books', BookController::class);

    // Book loan routes (all require authentication)
    Route::resource('loans', BookLoanController::class);
    Route::get('/loans/user/{user_id}', [BookLoanController::class, 'userLoans'])->name('loans.user');
});

require __DIR__.'/auth.php';
