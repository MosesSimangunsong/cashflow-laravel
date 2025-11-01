<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

Route::group(['prefix' => 'app', 'middleware' => 'check.auth'], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('app.home');

    // [MODIFIED]
    // 1. Mengganti URL dari 'todos/{todo_id}' menjadi 'cashflow/{cashflow_id}'
    // 2. Mengganti method dari 'todoDetail' menjadi 'cashflowDetail'
    // 3. Mengganti nama rute dari 'app.todos.detail' menjadi 'app.cashflows.detail'
    Route::get('/cashflow/{cashflow_id}', [HomeController::class, 'cashflowDetail'])->name('app.cashflows.detail');
});

Route::get('/', function () {
    return redirect()->route('app.home');
});