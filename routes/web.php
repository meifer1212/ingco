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
    return redirect()->route('tasks.index');
});


// crear un grupo de rutas con middleware auth
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    Route::get('/tasks', [App\Http\Controllers\TaskController::class, 'index'])->middleware('auth')->name('tasks.index');

    Route::get('/tasks/create', [App\Http\Controllers\TaskController::class, 'createView'])->middleware('auth')->name('tasks.create');

    Route::post('/tasks/create', [App\Http\Controllers\TaskController::class, 'create'])->middleware('auth')->name('tasks.create');

    Route::get('/tasks/{task}', [App\Http\Controllers\TaskController::class, 'show'])->middleware('auth')->name('tasks.show');

    Route::middleware(['check.task.ownership'])->group(function () {
        Route::get('/tasks/{task}/update', [App\Http\Controllers\TaskController::class, 'update'])->middleware('auth')->name('tasks.update');
        Route::put('/tasks/{task}', [App\Http\Controllers\TaskController::class, 'edit'])->middleware('auth')->name('tasks.edit');
        Route::get('/tasks/{task}/destroy', [App\Http\Controllers\TaskController::class, 'destroy'])->middleware('auth')->name('tasks.destroy');
        Route::delete('/tasks/{task}', [App\Http\Controllers\TaskController::class, 'delete'])->middleware('auth')->name('tasks.delete');
    });
});

Route::middleware(['noauth'])->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->middleware('noauth')->name('login');
    Route::get('/register', function () {
        return view('auth.register');
    })->middleware('noauth')->name('register');
});

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
