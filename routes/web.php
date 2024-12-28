<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::controller(UserController::class)->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::delete('users/delete/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::put('users/update', [UserController::class, 'update'])->name('users.update');
});
