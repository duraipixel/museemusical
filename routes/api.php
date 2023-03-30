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
Route::get('/get/home/details', [App\Http\Controllers\Api\CommonController::class, 'getAllHomeDetails']);
Route::get('/get/topMenu/{slug?}', [App\Http\Controllers\Api\MenuController::class, 'getTopMenu']);
Route::get('/get/allMenu', [App\Http\Controllers\Api\MenuController::class, 'getAllMenu']);
Route::get('/get/testimonials', [App\Http\Controllers\Api\CommonController::class, 'getAllTestimonials']);
Route::get('/get/history', [App\Http\Controllers\Api\CommonController::class, 'getAllHistoryVideo']);
Route::get('/get/banners', [App\Http\Controllers\Api\CommonController::class, 'getAllBanners']);
Route::get('/get/brands', [App\Http\Controllers\Api\CommonController::class, 'getAllBrands']);
Route::get('/get/brands/all/{slug}', [App\Http\Controllers\Api\CommonController::class, 'getBrandInfo']);
Route::get('/get/brands/alphabets', [App\Http\Controllers\Api\CommonController::class, 'getBrandByAlphabets']);
Route::get('/get/discount/collections', [App\Http\Controllers\Api\CommonController::class, 'getDiscountCollections']);
Route::get('/get/product/collections/{order_by?}', [App\Http\Controllers\Api\CollectionController::class, 'getProductCollections']);
Route::get('/get/product/collections/byorder/{order_by}', [App\Http\Controllers\Api\CollectionController::class, 'getProductCollectionByOrder']);
Route::get('/get/filter/static/sidemenus', [App\Http\Controllers\Api\FilterController::class, 'getFilterStaticSideMenu']);
Route::get('/get/products', [App\Http\Controllers\Api\FilterController::class, 'getProducts']);
Route::get('/get/products/by/slug/{product_url}', [App\Http\Controllers\Api\FilterController::class, 'getProductBySlug']);
Route::get('/get/states', [App\Http\Controllers\Api\CommonController::class, 'getSates']);
Route::post('/get/meta', [App\Http\Controllers\Api\CommonController::class, 'getMetaInfo']);
Route::post('/get/global/search', [App\Http\Controllers\Api\FilterController::class, 'globalSearch']);
Route::post('/get/other/category', [App\Http\Controllers\Api\FilterController::class, 'getOtherCategories']);
Route::post('/get/dynamic/filter/category', [App\Http\Controllers\Api\FilterController::class, 'getDynamicFilterCategory']);

Route::post('/register/customer', [App\Http\Controllers\Api\CustomerController::class, 'registerCustomer']);
Route::post('/login', [App\Http\Controllers\Api\CustomerController::class, 'doLogin']);
Route::post('/send/password/link', [App\Http\Controllers\Api\CustomerController::class, 'sendPasswordLink']);
Route::post('/reset/password', [App\Http\Controllers\Api\CustomerController::class, 'resetPasswordLink']);
Route::post('/check/tokenValid', [App\Http\Controllers\Api\CustomerController::class, 'checkValidToken']);
Route::get('/quickLink', [App\Http\Controllers\Api\QuickLinkController::class, 'index']);
Route::get('/get/orderCancelReason', [App\Http\Controllers\Api\OrderCancelController::class, 'index']);

Route::get('/verify/account', [App\Http\Controllers\Api\CustomerController::class, 'verifyAccount']);

Route::post('/add/cart', [App\Http\Controllers\Api\CartController::class, 'addToCart']);
Route::post('/update/cart', [App\Http\Controllers\Api\CartController::class, 'updateCart']);
Route::post('/delete/cart', [App\Http\Controllers\Api\CartController::class, 'deleteCart']);    
Route::post('/get/cart', [App\Http\Controllers\Api\CartController::class, 'getCarts']);
Route::post('/clear/cart', [App\Http\Controllers\Api\CartController::class, 'clearCart']);  

Route::middleware(['client'])->group(function(){
    
    Route::post('/update/cartAmount', [App\Http\Controllers\Api\CartController::class, 'updateCartAmount']);

    Route::post('/apply/coupon', [App\Http\Controllers\Api\Couponcontroller::class, 'applyCoupon']);
    Route::post('/add/customer/address', [App\Http\Controllers\Api\CustomerController::class, 'addCustomerAddress']);
    Route::post('/update/customer/address', [App\Http\Controllers\Api\CustomerController::class, 'updateCustomerAddress']);
    Route::post('/get/customer/address', [App\Http\Controllers\Api\CustomerController::class, 'getCustomerAddress']);
    Route::post('/delete/customer/address', [App\Http\Controllers\Api\CustomerController::class, 'deleteCustomerAddress']);
    Route::post('/proceed/checkout', [App\Http\Controllers\Api\CheckoutController::class, 'proceedCheckout']);
    Route::post('/verify/payment/signature', [App\Http\Controllers\Api\CheckoutController::class, 'verifySignature']);
    Route::post('/get/shipping/charges', [App\Http\Controllers\Api\CartController::class, 'getShippingCharges']);
    Route::post('/get/shipping/rocket/charges', [App\Http\Controllers\Api\CartController::class, 'getShippingRocketCharges']);

    Route::post('/set/recent', [App\Http\Controllers\Api\CommonController::class, 'setRecentView']);

    Route::post('/update/profile', [App\Http\Controllers\Api\CustomerController::class, 'updateProfile']);
    Route::post('/update/profile/image', [App\Http\Controllers\Api\CustomerController::class, 'updateProfileImage']);
    Route::post('/change/password', [App\Http\Controllers\Api\CustomerController::class, 'changePassword']);

    Route::post('/get/orders', [App\Http\Controllers\Api\OrderController::class, 'getOrders']);
    Route::post('/cancel/request/orders', [App\Http\Controllers\Api\OrderController::class, 'requestCancelOrder']);
    Route::post('/get/orderByno', [App\Http\Controllers\Api\OrderController::class, 'getOrderByOrderNo']);
    Route::post('/get/recent/view', [App\Http\Controllers\Api\CollectionController::class, 'getRecentViews']);
    
});

