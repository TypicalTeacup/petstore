<?php

use App\Http\Controllers\PetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/pet');
});

Route::prefix('/pet')->whereNumber('pet')->group(function () {
    Route::view('/', 'pet.index');
    Route::view('/new', 'pet.create');
    Route::view('/{pet}', 'pet.details');
    Route::get('/{pet}/edit', [PetController::class, 'edit']);
});
