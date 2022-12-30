<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/get/site/info', [App\Http\Controllers\Api\SiteController::class, 'siteInfo']);
Route::get('/get/topMenu', [App\Http\Controllers\Api\MenuController::class, 'getTopMenu']);
Route::get('/get/allMenu', [App\Http\Controllers\Api\MenuController::class, 'getAllMenu']);
Route::get('/get/testimonials', [App\Http\Controllers\Api\CommonController::class, 'getAllTestimonials']);
Route::get('/get/history', [App\Http\Controllers\Api\CommonController::class, 'getAllHistoryVideo']);
Route::get('/get/banners', [App\Http\Controllers\Api\CommonController::class, 'getAllBanners']);
Route::get('/get/brands', [App\Http\Controllers\Api\CommonController::class, 'getAllBrands']);
Route::get('/get/brands/alphabets', [App\Http\Controllers\Api\CommonController::class, 'getBrandByAlphabets']);
Route::get('/get/discount/collections', [App\Http\Controllers\Api\CommonController::class, 'getDiscountCollections']);
Route::get('/get/product/collections/{order_by?}', [App\Http\Controllers\Api\CollectionController::class, 'getProductCollections']);
Route::get('/get/product/collections/byorder/{order_by}', [App\Http\Controllers\Api\CollectionController::class, 'getProductCollectionByOrder']);
Route::get('/get/filter/static/sidemenus', [App\Http\Controllers\Api\FilterController::class, 'getFilterStaticSideMenu']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
