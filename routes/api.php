<?php

use App\Http\Controllers\PetController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('/user')->group(function () {
    Route::get('/login', [UserController::class, 'login']);

    Route::get('/{username}', [UserController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/logout', [UserController::class, 'logout']);

        Route::post('/', [UserController::class, 'store']);
        Route::post('/createWithList', [UserController::class, 'batchStore']);
        Route::post('/createWithArray', [UserController::class, 'batchStore']);


        Route::delete('/{username}', [UserController::class, 'destroy']);
    });
    Route::put('/{username}', [UserController::class, 'update']);
});

Route::prefix("/pet")->whereNumber('pet')->group(function () {
    Route::get('/', [PetController::class, 'index']);
    Route::post('/', [PetController::class, 'store']);
    Route::put('/', [PetController::class, 'update']);
    Route::get('/{pet}', [PetController::class, 'show']);

    Route::post('/{pet}', [PetController::class, 'updateFormData']);
    Route::get('/findByTags', [PetController::class, 'findByTags']);
    Route::get('/findByStatus', [PetController::class, 'findByStatus']);
    Route::post('/{pet}/uploadImage', [PetController::class, 'storeImage']);
    Route::delete('/{pet}', [PetController::class, 'destroy']);
});

Route::prefix('/store')->group(function () {
    Route::get('/inventory', [StoreController::class, 'inventory']);

    Route::prefix('/order')->whereNumber('order')->group(function () {
        Route::post('/', [StoreController::class, 'store']);
        Route::get('/{order}', [StoreController::class, 'show']);
        Route::delete('/{order}', [StoreController::class, 'destroy']);
    });
});
