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

Route::get('/', function () {
    return redirect()->route('home');
});


Route::resource('/home', \App\Http\Controllers\HomeController::class)->name('index', 'home');
Route::resource('/lab', \App\Http\Controllers\LabController::class)->name('index', 'lab');
Route::resource('/lab-group', \App\Http\Controllers\LabGroupController::class)->name('index', 'lab-group');

//

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
Route::post('/restore/{type}/{id}', [RestoreController::class, 'restore']);
// Gabungan DELETE dari DB only
Route::delete('/delete-only-db/{type}/{id}', [RestoreController::class, 'deleteOnlyDb']);


//
Route::post('/topologi/save/{id}', [TopologyController::class, 'save']);
Route::get('/topologi/load/{id}', [TopologyController::class, 'load']);
