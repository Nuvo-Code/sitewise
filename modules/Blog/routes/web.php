<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Controllers\BlogController;

/*
|--------------------------------------------------------------------------
| Blog Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the Blog module. These
| routes are loaded by the BlogServiceProvider within a group which
| contains the "web" middleware group and is prefixed with "blog".
|
*/

Route::get('/', [BlogController::class, 'index'])->name('index');
Route::get('/post/{id}', [BlogController::class, 'show'])->name('show');
Route::get('/about', [BlogController::class, 'about'])->name('about');
