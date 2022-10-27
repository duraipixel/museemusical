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
    Route::get('/testimonials', [App\Http\Controllers\TestimonialsController::class, 'index'])->name('testimonials');
    Route::get('/product', [App\Http\Controllers\Product\ProductController::class, 'index'])->name('product');
    Route::get('/walkthroughs', [App\Http\Controllers\WalkThroughController::class, 'index'])->name('walkthroughs');
    Route::get('/product-category', [App\Http\Controllers\Product\ProductCategoryController::class, 'index'])->name('product-category');
    
    Route::prefix('roles')->group(function(){
        Route::post('/addOrEdit', [App\Http\Controllers\Settings\RoleController::class, 'modalAddEdit'])->name('roles.add.edit');
        Route::post('/delete', [App\Http\Controllers\Settings\RoleController::class, 'delete'])->name('roles.delete');
        Route::post('/status', [App\Http\Controllers\Settings\RoleController::class, 'changeStatus'])->name('roles.status');
        Route::post('/save', [App\Http\Controllers\Settings\RoleController::class, 'saveForm'])->name('roles.save');
        Route::get('/export/excel', [App\Http\Controllers\Settings\RoleController::class, 'export'])->name('roles.export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\Settings\RoleController::class, 'exportPdf'])->name('roles.export.pdf');
    });

    Route::get('/global', [App\Http\Controllers\GlobalSettingController::class, 'index'])->name('global');
    Route::post('/global/save', [App\Http\Controllers\GlobalSettingController::class, 'saveForm'])->name('global.save');

    Route::prefix('my-profile')->group(function(){
        Route::get('/', [App\Http\Controllers\MyProfileController::class, 'index'])->name('my-profile');
        Route::get('/password', [App\Http\Controllers\MyProfileController::class, 'getPasswordTab'])->name('my-profile.password');
        Route::post('/getTab', [App\Http\Controllers\MyProfileController::class, 'getTab'])->name('my-profile.get.tab');
        Route::post('/save', [App\Http\Controllers\MyProfileController::class, 'saveForm'])->name('my-profile.save');
    });

    Route::prefix('users')->group(function(){
        Route::post('/addOrEdit', [App\Http\Controllers\UserController::class, 'modalAddEdit'])->name('users.add.edit');
        Route::post('/delete', [App\Http\Controllers\UserController::class, 'delete'])->name('users.delete');
        Route::post('/status', [App\Http\Controllers\UserController::class, 'changeStatus'])->name('users.status');
        Route::post('/save', [App\Http\Controllers\UserController::class, 'saveForm'])->name('users.save');
        Route::get('/export/excel', [App\Http\Controllers\UserController::class, 'export'])->name('users.export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\UserController::class, 'exportPdf'])->name('users.export.pdf');
    });

    Route::prefix('order-status')->group(function(){
        Route::post('/addOrEdit', [App\Http\Controllers\Master\OrderStatusController::class, 'modalAddEdit'])->name('order-status.add.edit');
        Route::post('/status', [App\Http\Controllers\Master\OrderStatusController::class, 'changeStatus'])->name('order-status.status');
        Route::post('/delete', [App\Http\Controllers\Master\OrderStatusController::class, 'delete'])->name('order-status.delete');
        Route::post('/save', [App\Http\Controllers\Master\OrderStatusController::class, 'saveForm'])->name('order-status.save');
        Route::get('/export/excel', [App\Http\Controllers\Master\OrderStatusController::class, 'export'])->name('order-status.export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\Master\OrderStatusController::class, 'exportPdf'])->name('order-status.export.pdf');
    });

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

    Route::post('/testimonials/addOrEdit', [App\Http\Controllers\TestimonialsController::class, 'modalAddEdit'])->name('testimonials.add.edit');
    Route::post('/testimonials/status', [App\Http\Controllers\TestimonialsController::class, 'changeStatus'])->name('testimonials.status');
    Route::post('/testimonials/delete', [App\Http\Controllers\TestimonialsController::class, 'delete'])->name('testimonials.delete');
    Route::post('/testimonials/save', [App\Http\Controllers\TestimonialsController::class, 'saveForm'])->name('testimonials.save');
    Route::get('/testimonials/export/excel', [App\Http\Controllers\TestimonialsController::class, 'export'])->name('testimonials.export.excel');
    Route::get('/testimonials/export/pdf', [App\Http\Controllers\TestimonialsController::class, 'exportPdf'])->name('testimonials.export.pdf');

    Route::prefix('products')->group(function(){
        Route::get('/', [App\Http\Controllers\Product\ProductController::class, 'index'])->name('products'); 
        Route::get('/add', [App\Http\Controllers\Product\ProductController::class, 'addEditPage'])->name('products.add.edit'); 
        Route::post('/status', [App\Http\Controllers\Product\ProductController::class, 'changeStatus'])->name('products.status');
        Route::post('/delete', [App\Http\Controllers\Product\ProductController::class, 'delete'])->name('products.delete');
        Route::post('/save', [App\Http\Controllers\Product\ProductController::class, 'saveForm'])->name('products.save');
        Route::get('/export/excel', [App\Http\Controllers\Product\ProductController::class, 'export'])->name('products.export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\Product\ProductController::class, 'exportPdf'])->name('products.export.pdf');
    });

    Route::prefix('walkthroughs')->group(function(){
        Route::post('/addOrEdit', [App\Http\Controllers\WalkThroughController::class, 'modalAddEdit'])->name('walkthroughs.add.edit');
        Route::post('/status', [App\Http\Controllers\WalkThroughController::class, 'changeStatus'])->name('walkthroughs.status');
        Route::post('/delete', [App\Http\Controllers\WalkThroughController::class, 'delete'])->name('walkthroughs.delete');
        Route::post('/save', [App\Http\Controllers\WalkThroughController::class, 'saveForm'])->name('walkthroughs.save');
        Route::get('/export/excel', [App\Http\Controllers\WalkThroughController::class, 'export'])->name('walkthroughs.export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\WalkThroughController::class, 'exportPdf'])->name('walkthroughs.export.pdf');
    });

    Route::prefix('product-category')->group(function(){
        Route::post('/addOrEdit', [App\Http\Controllers\Product\ProductCategoryController::class, 'modalAddEdit'])->name('product-category.add.edit');
        Route::post('/status', [App\Http\Controllers\Product\ProductCategoryController::class, 'changeStatus'])->name('product-category.status');
        Route::post('/delete', [App\Http\Controllers\Product\ProductCategoryController::class, 'delete'])->name('product-category.delete');
        Route::post('/save', [App\Http\Controllers\Product\ProductCategoryController::class, 'saveForm'])->name('product-category.save');
        Route::get('/export/excel', [App\Http\Controllers\Product\ProductCategoryController::class, 'export'])->name('product-category.export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\Product\ProductCategoryController::class, 'exportPdf'])->name('product-category.export.pdf');
    });

});


