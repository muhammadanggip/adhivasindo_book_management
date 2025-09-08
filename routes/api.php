<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BookLoanController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// API Authentication Routes
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token,
    ]);
});

Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully'
    ]);
})->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Protected API Routes
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
