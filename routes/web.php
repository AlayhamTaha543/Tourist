<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', function () {
    return view('admin.login');
})->name(('login'));
Route::get('/otpCode', function () {
    return view('admin.verifyCode');
})->name(('otpCode'));
