<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
});

Route::group([
    'middleware' => ['auth:api'],
    'prefix' => 'cities'
], function ($router) {
    Route::get('/', [CityController::class, 'list'])->name('list');
    Route::get('/find', [CityController::class, 'find'])->name('find');
    Route::get('/{id}', [CityController::class, 'show'])->name('show');
    Route::post('/', [CityController::class, 'store'])->name('store');
    Route::put('/{id}', [CityController::class, 'update'])->name('update');
    Route::delete('/{id}', [CityController::class, 'destroy'])->name('destory');
});