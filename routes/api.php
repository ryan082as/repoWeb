<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);
Route::apiResource('users', UserController::class);

Route::get('/users/{user}/collection', [UserController::class, 'getCollection']);
Route::post('/users/{user}/collection', [UserController::class, 'addToCollection']);
Route::delete('/users/{user}/collection/{media}', [UserController::class, 'removeFromCollection']);
