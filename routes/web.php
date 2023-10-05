<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportCenterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\PDFController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WordController;
use App\Http\Controllers\ReportDateController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\EmailController;

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
    Route::post('report/copy/{id}', [DashBoardController::class, 'copy'])->name('report.copy');
    Route::post('/users/send-email', [DashBoardController::class, 'sendEmail'])->name('centers.sendEmail');

    Route::get('generate-pdf', [PDFController::class, 'generatePDF'])->name('pdf');
    Route::get('delete', [DashBoardController::class, 'deleteDataWeek'])->name('delete.data');
    Route::get('pdf/{id}', [PDFController::class, 'generatePDFDetails'])->name('pdf.details');
    Route::get('pdf-dep/{id}', [PDFController::class, 'departmentPDF'])->name('pdf.department');

    Route::get('/generate-word', [WordController::class, 'generateWord'])->name('word');
    Route::get('word/{id}', [WordController::class, 'generateWordDetails'])->name('word.details');
    Route::get('word-dep/{id}', [WordController::class, 'departmentWord'])->name('word.department');
    Route::get('/generate-excel', [ExcelController::class, 'generateExcel'])->name('excel');
    Route::get('excel/{id}', [ExcelController::class, 'generateExcelDetails'])->name('excel.details');
    Route::get('excel-dep/{id}', [ExcelController::class, 'departmentExcel'])->name('excel.department');

    Route::get('/report-dates', [ReportDateController::class, 'index'])->name('report-dates.index');
    Route::post('/report-dates', [ReportDateController::class, 'store'])->name('report-dates.store');
    Route::post('/search-report', [ReportDateController::class, 'searchReport'])->name('search.report');

    Route::get('/show-units', [UnitController::class, 'showUnits'])->name('show.units');

    Route::resource('emails', EmailController::class);
});

require __DIR__.'/auth.php';
