<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;

Route::get('/', [CategoryController::class, 'index']);
Route::resource('books', BookController::class);
