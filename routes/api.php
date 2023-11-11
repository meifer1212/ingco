<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return response()->json([
//         'user' => $request->user()
//     ]);
// });


// Route::post('/login', [AuthController::class, 'loginAPI']);
// Route::post('/register', [AuthController::class, 'registerAPI']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/tasks', [TaskController::class, 'getTasksAPI']); //
    Route::get('/tags', [TaskController::class, 'getTagsAPI']); //
    Route::get('/users', [TaskController::class, 'getUsersAPI']); //
    Route::post('/task', [TaskController::class, 'createTaskAPI']); //
    Route::middleware(['check.task.ownership'])->group(function () {
        Route::put('/task/{task}', [TaskController::class, 'updateTaskAPI']); //
        Route::delete('/task/{task}', [TaskController::class, 'deleteTaskAPI']); //
    });
    Route::post('/logout', [AuthController::class, 'logoutAPI']);
});
