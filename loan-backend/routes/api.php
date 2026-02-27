<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('loans')->group(function () {
    Route::post('/analyze', [LoanController::class, 'analyze']);
    Route::get('/applications', [LoanController::class, 'index']);
    Route::get('/applications/{id}', [LoanController::class, 'show']);
    Route::get('/clients/{clientId}/applications', [LoanController::class, 'clientApplications']);
    Route::get('/statistics', [LoanController::class, 'statistics']);
});
