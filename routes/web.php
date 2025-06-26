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

<<<<<<< HEAD
Route::get('/lab/folder/{id?}', [LabController::class, 'ajaxFolder']);
Route::get('/lab/preview/{id}', [LabController::class, 'getJsonPreview']);
Route::post('/lab-group/{id}/rename', [LabGroupController::class, 'rename']);
Route::get('/lab/{lab}/topologi', [LabController::class, 'topologi'])->name('lab.canvas');
Route::get('/lab-group/{id}/check-contents', [LabGroupController::class, 'checkContents']);
Route::post('/lab/{id}/update-json', [LabController::class, 'updateJson']);
Route::delete('/lab-group/{id}', [LabGroupController::class, 'destroy']);
Route::get('/lab/folder/{id}', [LabController::class, 'ajaxFolder']);
Route::get('/lab/{id}/json', [LabController::class, 'getJsonExport']);
Route::post('/lab/import', [LabController::class, 'importLab']);
//

// Gabungan RESTORE
=======
// LabGroup specific routes
Route::prefix('lab-group')->group(function () {
    Route::post('/{id}/rename', [LabGroupController::class, 'rename']);
    Route::get('/{id}/check-contents', [LabGroupController::class, 'checkContents']);
    Route::delete('/{id}', [LabGroupController::class, 'destroy']);
});

// Restore and Delete (DB Only) routes
>>>>>>> c5b88f64b83a58d59a52d914602b49eae9113466
Route::post('/restore/{type}/{id}', [RestoreController::class, 'restore']);
Route::delete('/delete-only-db/{type}/{id}', [RestoreController::class, 'deleteOnlyDb']);

<<<<<<< HEAD

//
Route::post('/topologi/save/{id}', [TopologyController::class, 'save']);
Route::get('/topologi/load/{id}', [TopologyController::class, 'load']);
=======
// Topology routes
Route::prefix('topologi')->group(function () {
    Route::post('/save/{id}', [TopologyController::class, 'save']);
    Route::get('/load/{id}', [TopologyController::class, 'load']);
});
>>>>>>> c5b88f64b83a58d59a52d914602b49eae9113466
