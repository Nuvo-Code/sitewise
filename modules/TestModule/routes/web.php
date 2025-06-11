<?php

use Illuminate\Support\Facades\Route;
use Modules\TestModule\Controllers\TestModuleController;

/*
|--------------------------------------------------------------------------
| TestModule Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the TestModule module. These
| routes are loaded by the TestModuleServiceProvider within a group which
| contains the "web" middleware group and is prefixed with "testmodule".
|
*/

Route::get('/', [TestModuleController::class, 'index'])->name('index');
Route::get('/about', [TestModuleController::class, 'about'])->name('about');