<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportCenterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\PDFController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

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
    return redirect()->route('login');
});



Route::get('/dashboard', [DashBoardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('departments', DepartmentController::class)->name('departments', 'departments');
    Route::resource('users', UserController::class)->name('users', 'users');
    Route::resource('reports', ReportController::class)->name('reports', 'reports');
    Route::resource('centers', ReportCenterController::class)->name('centers', 'centers');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // routes/web.php
    Route::post('/centers/run', [DashBoardController::class, 'run'])->name('centers.run');
    Route::post('/centers/search', [DashBoardController::class, 'search'])->name('centers.search');
    Route::get('generate-pdf', [PDFController::class, 'generatePDF'])->name('pdf');
    Route::get('pdf/{id}', [PDFController::class, 'generatePDFDetails'])->name('pdf.details');
});

require __DIR__.'/auth.php';
