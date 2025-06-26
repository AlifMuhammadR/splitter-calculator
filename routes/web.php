<?php

use App\Http\Controllers\LabController;
use App\Http\Controllers\LabGroupController;
use App\Http\Controllers\LossCalculatorController;
use App\Http\Controllers\RestoreController;
use App\Http\Controllers\TopologyController;
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

// Redirect root to home page
Route::get('/', function () {
    return redirect()->route('home.index');
});

// Resource routes for Home, Lab, and LabGroup
Route::resource('/home', \App\Http\Controllers\HomeController::class)->name('index', 'home');
Route::resource('/lab', LabController::class)->name('index', 'lab');
Route::resource('/lab-group', LabGroupController::class)->name('index', 'lab-group');

// Lab specific routes
Route::prefix('lab')->group(function () {
    Route::get('/folder/{id?}', [LabController::class, 'ajaxFolder']);
    Route::get('/preview/{id}', [LabController::class, 'getJsonPreview']);
    Route::get('/{lab}/topologi', [LabController::class, 'topologi'])->name('lab.canvas');
    Route::post('/{id}/update-json', [LabController::class, 'updateJson']);
    Route::get('/{id}/json', [LabController::class, 'getJsonExport']);
    Route::post('/import', [LabController::class, 'importLab']);
});

// LabGroup specific routes
Route::prefix('lab-group')->group(function () {
    Route::post('/{id}/rename', [LabGroupController::class, 'rename']);
    Route::get('/{id}/check-contents', [LabGroupController::class, 'checkContents']);
    Route::delete('/{id}', [LabGroupController::class, 'destroy']);
});

// Restore and Delete (DB Only) routes
Route::post('/restore/{type}/{id}', [RestoreController::class, 'restore']);
Route::delete('/delete-only-db/{type}/{id}', [RestoreController::class, 'deleteOnlyDb']);

// Topology routes
Route::prefix('topologi')->group(function () {
    Route::post('/save/{id}', [TopologyController::class, 'save']);
    Route::get('/load/{id}', [TopologyController::class, 'load']);
});
