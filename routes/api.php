<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BookLoanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware(['auth:sanctum'])->group(function () {
    // Book routes
    Route::apiResource('books', BookController::class);

    // Book loan routes
    Route::get('loans', [BookLoanController::class, 'index']);
    Route::post('loans', [BookLoanController::class, 'store']);
    Route::get('loans/{id}', [BookLoanController::class, 'show']);
    Route::put('loans/{id}/return', [BookLoanController::class, 'update']);
    Route::get('loans/user/{userId}', [BookLoanController::class, 'userLoans']);
});
