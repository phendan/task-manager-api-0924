<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

Route::post('/task', [TaskController::class, 'store'])->middleware('auth:sanctum');
Route::patch('/task/{id}', [TaskController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/task/{id}', [TaskController::class, 'delete'])->middleware('auth:sanctum');
