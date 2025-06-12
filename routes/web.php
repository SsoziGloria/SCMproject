<?php
use App\Http\Controllers\AuthController;

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('dashboard', function () {
    return view('layouts.app');
})->middleware('auth')->name('dashboard');

Route::get('/', function () {
    return redirect()->route('login');
});