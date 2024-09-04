<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/username_verify', [AuthController::class, 'username_verify']);
Route::post('/phone_number_verify', [AuthController::class, 'phone_number_verify']);
Route::post('/send_verification_sms', [AuthController::class, 'send_verification_sms']);
Route::post('/verify_code', [AuthController::class, 'verify_code']);
Route::middleware('auth:sanctum')->group(function (){
    Route::get('/actualise', [AuthController::class,'actualise']);
    Route::post('/update_photo', [UserController::class,'update_photo']);
    Route::get('/delete_photo', [UserController::class,'delete_photo']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
