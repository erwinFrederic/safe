<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\EmergencyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/username_verify', 'username_verify');
    Route::post('/phone_number_verify', 'phone_number_verify');
    Route::post('/email_verify', 'email_verify');
    Route::post('/send_verification_sms', 'send_verification_sms');
    Route::post('/verify_code', 'verify_code');
    Route::get('/actualise', 'actualise')->middleware('auth:sanctum');
    Route::get('/logout', 'logout')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::put('/update_informations', 'update_informations');
        Route::post('/update_photo', 'update_photo');
        Route::get('/delete_photo', 'delete_photo');
        Route::post('/send_verification_email', 'send_verification_email');
        Route::post('/verify_email_code', 'verify_email_code');
        Route::put('/update_email', 'update_email');
        Route::delete('/delete_email', 'delete_email');
        Route::put('/update_password', 'update_password');
        Route::put('/update_phone_number', 'update_number');
    });
    Route::controller(EmergencyController::class)->group(function(){
        Route::post('/register_member','register_member');
    });
    Route::controller(AdminController::class)->group(function(){
        Route::post('/create_emergency','create_emergency');
    });
    Route::controller(EmergencyContactController::class)->group(function(){
        Route::post('/create_contact','create_contact');
        Route::put('/update_contact/{id}','update_contact');
        Route::delete('/delete_contact/{id}','delete_contact');
    });
    Route::controller(VehicleController::class)->group(function (){
        Route::post('/create_vehicle','create_vehicle');
        Route::put('/update_vehicle/{id}','update_vehicle');
        Route::delete('/delete_vehicle/{id}','delete_vehicle');
    });
});

