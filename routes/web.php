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
    Route::get('/state', [App\Http\Controllers\Master\StateController::class, 'index'])->name('state');
    Route::get('/pincode', [App\Http\Controllers\Master\PincodeController::class, 'index'])->name('pincode');
    Route::get('/city', [App\Http\Controllers\Master\CityController::class, 'index'])->name('city');
    Route::get('/brands', [App\Http\Controllers\Master\BrandController::class, 'index'])->name('brand');
    Route::get('/main_category', [App\Http\Controllers\Category\MainCategoryController::class, 'index'])->name('main_category');
    Route::get('/sub_category', [App\Http\Controllers\Category\SubCategoryController::class, 'index'])->name('sub_category');

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

    Route::post('/state/addOrEdit', [App\Http\Controllers\Master\StateController::class, 'modalAddEdit'])->name('state.add.edit');
    Route::post('/state/status', [App\Http\Controllers\Master\StateController::class, 'changeStatus'])->name('state.status');
    Route::post('/state/delete', [App\Http\Controllers\Master\StateController::class, 'delete'])->name('state.delete');
    Route::post('/state/save', [App\Http\Controllers\Master\StateController::class, 'saveForm'])->name('state.save');
    Route::get('/state/export/excel', [App\Http\Controllers\Master\StateController::class, 'export'])->name('state.export.excel');
    Route::get('/state/export/pdf', [App\Http\Controllers\Master\StateController::class, 'exportPdf'])->name('state.export.pdf');

    Route::post('/city/addOrEdit', [App\Http\Controllers\Master\CityController::class, 'modalAddEdit'])->name('city.add.edit');
    Route::post('/city/status', [App\Http\Controllers\Master\CityController::class, 'changeStatus'])->name('city.status');
    Route::post('/city/delete', [App\Http\Controllers\Master\CityController::class, 'delete'])->name('city.delete');
    Route::post('/city/save', [App\Http\Controllers\Master\CityController::class, 'saveForm'])->name('city.save');
    Route::get('/city/export/excel', [App\Http\Controllers\Master\CityController::class, 'export'])->name('city.export.excel');
    Route::get('/city/export/pdf', [App\Http\Controllers\Master\CityController::class, 'exportPdf'])->name('city.export.pdf');

    Route::post('/pincode/addOrEdit', [App\Http\Controllers\Master\PincodeController::class, 'modalAddEdit'])->name('pincode.add.edit');
    Route::post('/pincode/status', [App\Http\Controllers\Master\PincodeController::class, 'changeStatus'])->name('pincode.status');
    Route::post('/pincode/delete', [App\Http\Controllers\Master\PincodeController::class, 'delete'])->name('pincode.delete');
    Route::post('/pincode/save', [App\Http\Controllers\Master\PincodeController::class, 'saveForm'])->name('pincode.save');
    Route::get('/pincode/export/excel', [App\Http\Controllers\Master\PincodeController::class, 'export'])->name('pincode.export.excel');
    Route::get('/pincode/export/pdf', [App\Http\Controllers\Master\PincodeController::class, 'exportPdf'])->name('pincode.export.pdf');

    Route::post('/brand/addOrEdit', [App\Http\Controllers\Master\BrandController::class, 'modalAddEdit'])->name('brand.add.edit');
    Route::post('/brand/status', [App\Http\Controllers\Master\BrandController::class, 'changeStatus'])->name('brand.status');
    Route::post('/brand/delete', [App\Http\Controllers\Master\BrandController::class, 'delete'])->name('brand.delete');
    Route::post('/brand/save', [App\Http\Controllers\Master\BrandController::class, 'saveForm'])->name('brand.save');
    Route::get('/brand/export/excel', [App\Http\Controllers\Master\BrandController::class, 'export'])->name('brand.export.excel');
    Route::get('/brand/export/pdf', [App\Http\Controllers\Master\BrandController::class, 'exportPdf'])->name('brand.export.pdf');

    Route::post('/main_category/addOrEdit', [App\Http\Controllers\Category\MainCategoryController::class, 'modalAddEdit'])->name('main_category.add.edit');
    Route::post('/main_category/status', [App\Http\Controllers\Category\MainCategoryController::class, 'changeStatus'])->name('main_category.status');
    Route::post('/main_category/delete', [App\Http\Controllers\Category\MainCategoryController::class, 'delete'])->name('main_category.delete');
    Route::post('/main_category/save', [App\Http\Controllers\Category\MainCategoryController::class, 'saveForm'])->name('main_category.save');
    Route::get('/main_category/export/excel', [App\Http\Controllers\Category\MainCategoryController::class, 'export'])->name('main_category.export.excel');
    Route::get('/main_category/export/pdf', [App\Http\Controllers\Category\MainCategoryController::class, 'exportPdf'])->name('main_category.export.pdf');

    Route::post('/sub_category/addOrEdit', [App\Http\Controllers\Category\SubCategoryController::class, 'modalAddEdit'])->name('sub_category.add.edit');
    Route::post('/sub_category/status', [App\Http\Controllers\Category\SubCategoryController::class, 'changeStatus'])->name('sub_category.status');
    Route::post('/sub_category/delete', [App\Http\Controllers\Category\SubCategoryController::class, 'delete'])->name('sub_category.delete');
    Route::post('/sub_category/save', [App\Http\Controllers\Category\SubCategoryController::class, 'saveForm'])->name('sub_category.save');
    Route::get('/sub_category/export/excel', [App\Http\Controllers\Category\SubCategoryController::class, 'export'])->name('sub_category.export.excel');
    Route::get('/sub_category/export/pdf', [App\Http\Controllers\Category\SubCategoryController::class, 'exportPdf'])->name('sub_category.export.pdf');
});


