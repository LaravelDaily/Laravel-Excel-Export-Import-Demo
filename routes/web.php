<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

// Imports
Route::post('import/array', [ImportController::class, 'array'])->name('import.array');
Route::post('import/excel', [ImportController::class, 'excel'])->name('import.excel');
Route::post('import/spatie', [ImportController::class, 'spatie'])->name('import.spatie');
Route::post('import/fast-excel', [ImportController::class, 'fastExcel'])->name('import.fast-excel');

// Exports
Route::get('export/array', [ExportController::class, 'array'])->name('export.array');
Route::get('export/excel', [ExportController::class, 'excel'])->name('export.excel');
Route::get('export/spatie', [ExportController::class, 'spatie'])->name('export.spatie');
Route::get('export/fast-excel', [ExportController::class, 'fastExcel'])->name('export.fast-excel');

Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');

Route::middleware('auth')->group(function () {
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__ . '/auth.php';
