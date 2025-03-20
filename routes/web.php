<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\InsertController;

Route::get('/home/admin/insert', [InsertController::class, 'showForm'])->name('admin.insert.form');
Route::post('/home/admin/insert', [InsertController::class, 'insertTransaction'])->name('admin.insert.transaction');


// Rute untuk registrasi dan login
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('register', [RegisterController::class, 'register'])->name('register');
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('login', [LoginController::class, 'login'])->name('login');

// Rute untuk verifikasi email
Route::get('email/verify/{id}/{hash}', [VerifyEmail::class, 'verify'])->name('verification.verify');
Route::post('email/resend', [VerifyEmail::class, 'resend'])->name('verification.resend');

// Halaman setelah login dan verifikasi
Route::get('/home', function () {
    return 'Selamat datang di rumah mu!';
})->middleware(['auth', 'verified'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
