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
Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Auth::routes();
Route::middleware(['auth'])->group(function(){
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users');
    Route::get('/roles', [App\Http\Controllers\Settings\RoleController::class, 'index'])->name('roles');
    Route::get('/order-status', [App\Http\Controllers\Master\OrderStatusController::class, 'index'])->name('order-status');
    Route::get('/country', [App\Http\Controllers\Master\CountryController::class, 'index'])->name('country');
    Route::post('/roles/addOrEdit', [App\Http\Controllers\Settings\RoleController::class, 'modalAddEdit'])->name('roles.add.edit');
    Route::post('/roles/delete', [App\Http\Controllers\Settings\RoleController::class, 'delete'])->name('roles.delete');
    Route::post('/roles/status', [App\Http\Controllers\Settings\RoleController::class, 'changeStatus'])->name('roles.status');
    Route::post('/roles/save', [App\Http\Controllers\Settings\RoleController::class, 'saveForm'])->name('roles.save');
    Route::get('/roles/export/excel', [App\Http\Controllers\Settings\RoleController::class, 'export'])->name('roles.export.excel');
    Route::get('/roles/export/pdf', [App\Http\Controllers\Settings\RoleController::class, 'exportPdf'])->name('roles.export.pdf');

    Route::get('/global', [App\Http\Controllers\GlobalSettingController::class, 'index'])->name('global');
    Route::post('/global/save', [App\Http\Controllers\GlobalSettingController::class, 'saveForm'])->name('global.save');

    Route::post('/users/addOrEdit', [App\Http\Controllers\UserController::class, 'modalAddEdit'])->name('users.add.edit');
    Route::post('/users/delete', [App\Http\Controllers\UserController::class, 'delete'])->name('users.delete');
    Route::post('/users/status', [App\Http\Controllers\UserController::class, 'changeStatus'])->name('users.status');
    Route::post('/users/save', [App\Http\Controllers\UserController::class, 'saveForm'])->name('users.save');
    Route::get('/users/export/excel', [App\Http\Controllers\UserController::class, 'export'])->name('users.export.excel');
    Route::get('/users/export/pdf', [App\Http\Controllers\UserController::class, 'exportPdf'])->name('users.export.pdf');

    Route::post('/order-status/addOrEdit', [App\Http\Controllers\Master\OrderStatusController::class, 'modalAddEdit'])->name('order-status.add.edit');
    Route::post('/order-status/status', [App\Http\Controllers\Master\OrderStatusController::class, 'changeStatus'])->name('order-status.status');
    Route::post('/order-status/delete', [App\Http\Controllers\Master\OrderStatusController::class, 'delete'])->name('order-status.delete');
    Route::post('/order-status/save', [App\Http\Controllers\Master\OrderStatusController::class, 'saveForm'])->name('order-status.save');
    Route::get('/order-status/export/excel', [App\Http\Controllers\Master\OrderStatusController::class, 'export'])->name('order-status.export.excel');
    Route::get('/order-status/export/pdf', [App\Http\Controllers\Master\OrderStatusController::class, 'exportPdf'])->name('order-status.export.pdf');

    Route::post('/country/addOrEdit', [App\Http\Controllers\Master\CountryController::class, 'modalAddEdit'])->name('country.add.edit');
    Route::post('/country/status', [App\Http\Controllers\Master\CountryController::class, 'changeStatus'])->name('country.status');
    Route::post('/country/delete', [App\Http\Controllers\Master\CountryController::class, 'delete'])->name('country.delete');
    Route::post('/country/save', [App\Http\Controllers\Master\CountryController::class, 'saveForm'])->name('country.save');
    Route::get('/country/export/excel', [App\Http\Controllers\Master\CountryController::class, 'export'])->name('country.export.excel');
    Route::get('/country/export/pdf', [App\Http\Controllers\Master\CountryController::class, 'exportPdf'])->name('country.export.pdf');
});
