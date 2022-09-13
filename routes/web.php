<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Auth::routes();
Route::middleware(['auth'])->group(function(){
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users');
    Route::get('/roles', [App\Http\Controllers\Settings\RoleController::class, 'index'])->name('roles');
    Route::post('/roles/addOrEdit', [App\Http\Controllers\Settings\RoleController::class, 'modalAddEdit'])->name('roles.add.edit');
    Route::post('/roles/delete', [App\Http\Controllers\Settings\RoleController::class, 'delete'])->name('roles.delete');
    Route::post('/roles/status', [App\Http\Controllers\Settings\RoleController::class, 'changeStatus'])->name('roles.status');
    Route::post('/roles/save', [App\Http\Controllers\Settings\RoleController::class, 'saveForm'])->name('roles.save');
    Route::get('/roles/export/excel', [App\Http\Controllers\Settings\RoleController::class, 'export'])->name('roles.export.excel');
    Route::get('/roles/export/pdf', [App\Http\Controllers\Settings\RoleController::class, 'exportPdf'])->name('roles.export.pdf');
});
