<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/doc-to-pdf')->group(function () {
    Route::get('/', function () {
        return view('files');
    });
    Route::post('/upload-file', [DocumentController::class, 'upload'])->name('upload');

    Route::post('/convert', [DocumentController::class, 'convert'])->name('convert');
});




