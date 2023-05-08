<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use Tymon\JWTAuth\JWTAuth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Authentication Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

// Protected Routes
Route::middleware('auth.jwt')->group(function () {

    // User Routes
    Route::get('users',[UserController::class, 'index']);
    Route::get('users/{id}',[UserController::class, 'show']);
    Route::put('users/{id}',[UserController::class, 'update']);
    Route::delete('users/{id}',[UserController::class, 'destroy']);
    // Project Routes
    Route::get('projects',[ProjectController::class, 'index']);
    Route::get('projects/{id}',[ProjectController::class, 'show']);
    Route::post('projects',[ProjectController::class, 'store']);
    Route::put('projects/{id}',[ProjectController::class, 'update']);
    Route::delete('projects/{id}',[ProjectController::class, 'destroy']);
    // Task Routes
    Route::get('tasks',[TaskController::class, 'index']);
    Route::get('tasks/{id}', [TaskController::class, 'show'] );
    Route::post('tasks', [TaskController::class, 'store']);
    Route::put('tasks/{id}', [TaskController::class, 'update']);
    Route::delete('tasks/{id}', [TaskController::class, 'destroy']);
});


