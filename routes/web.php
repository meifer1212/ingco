<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('tasks');
});

Route::get('/login', [App\Http\Controllers\AuthController::class, 'loginView'])->name('loginView');
Route::get('/register', [App\Http\Controllers\AuthController::class, 'registerView'])->middleware('noauth')->name('registerView');

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');

Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::get('/tasks', [App\Http\Controllers\TaskController::class, 'index'])->middleware('auth')->name('tasks');
Route::get('/tasks/create', [App\Http\Controllers\TaskController::class, 'create'])->middleware('auth')->name('tasks.create');
Route::post('/tasks', [App\Http\Controllers\TaskController::class, 'store'])->middleware('auth')->name('tasks.store');
Route::get('/tasks/{task}', [App\Http\Controllers\TaskController::class, 'show'])->middleware('auth')->name('tasks.show');
Route::put('/tasks/{task}', [App\Http\Controllers\TaskController::class, 'update'])->middleware('auth')->name('tasks.update');
Route::delete('/tasks/{task}', [App\Http\Controllers\TaskController::class, 'destroy'])->middleware('auth')->name('tasks.destroy');
