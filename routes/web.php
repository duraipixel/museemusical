<?php

use App\Http\Controllers\RazorpayPaymentController;
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
// Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/test', [App\Http\Controllers\TestController::class, 'index']);
Auth::routes();
Route::middleware(['auth'])->group(function(){
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    Route::prefix('global')->group(function(){
        Route::get('/', [App\Http\Controllers\GlobalSettingController::class, 'index'])->name('global');
        Route::post('/save', [App\Http\Controllers\GlobalSettingController::class, 'saveForm'])->name('global.save');
        Route::post('/getTab', [App\Http\Controllers\GlobalSettingController::class, 'getTab'])->name('global.tab');
    });    
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
    Route::get('/tax', [App\Http\Controllers\Settings\TaxController::class, 'index'])->name('tax');
    Route::get('/coupon', [App\Http\Controllers\Offers\CouponController::class, 'index'])->name('coupon');
    Route::get('/email-template', [App\Http\Controllers\Master\EmailTemplateController::class, 'index'])->name('email-template');
    Route::get('/customer', [App\Http\Controllers\CustomerController::class, 'index'])->name('customer');
    Route::get('/video-booking', [App\Http\Controllers\VideoBookingController::class, 'index'])->name('video-booking');
    Route::get('/banner', [App\Http\Controllers\BannerController::class, 'index'])->name('banner');
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

    $categoriesArray = array('sub_category', 'product-tags', 'product-labels');
    foreach ($categoriesArray as $catUrl ) {
        Route::prefix($catUrl)->group(function() use($catUrl) {
            Route::get('/', [App\Http\Controllers\Category\SubCategoryController::class, 'index'])->name($catUrl);
            Route::post('/addOrEdit', [App\Http\Controllers\Category\SubCategoryController::class, 'modalAddEdit'])->name($catUrl.'.add.edit');
            Route::post('/status', [App\Http\Controllers\Category\SubCategoryController::class, 'changeStatus'])->name($catUrl.'.status');
            Route::post('/delete', [App\Http\Controllers\Category\SubCategoryController::class, 'delete'])->name($catUrl.'.delete');
            Route::post('/save', [App\Http\Controllers\Category\SubCategoryController::class, 'saveForm'])->name($catUrl.'.save');
            Route::get('/export/excel', [App\Http\Controllers\Category\SubCategoryController::class, 'export'])->name($catUrl.'.export.excel');
            Route::get('/export/pdf', [App\Http\Controllers\Category\SubCategoryController::class, 'exportPdf'])->name($catUrl.'.export.pdf');
        });
    }
    /***** loop for same routes */
    $routeArray = array(
        'brands' => App\Http\Controllers\Master\BrandController::class,
        'product-category' => App\Http\Controllers\Product\ProductCategoryController::class,
        'tax' => App\Http\Controllers\Settings\TaxController::class,
        'coupon' => App\Http\Controllers\Offers\CouponController::class,
        'email-template' => App\Http\Controllers\Master\EmailTemplateController::class,
        'video-booking' => App\Http\Controllers\VideoBookingController::class,
        'walkthroughs' => App\Http\Controllers\WalkThroughController::class,
        'testimonials' => App\Http\Controllers\TestimonialsController::class,
        'main_category' => App\Http\Controllers\Category\MainCategoryController::class,        
        'pincode' => App\Http\Controllers\Master\PincodeController::class,
        'city' => App\Http\Controllers\Master\CityController::class,
        'state' => App\Http\Controllers\Master\StateController::class,
        'country' => App\Http\Controllers\Master\CountryController::class,
        'order-status' => App\Http\Controllers\Master\OrderStatusController::class,
        'users' => App\Http\Controllers\UserController::class,
        'sms-template' => App\Http\Controllers\SmsTemplateController::class,
        'payment-gateway' => App\Http\Controllers\PaymentGatewayController::class,
        'roles' => App\Http\Controllers\Settings\RoleController::class,
        'customer' => App\Http\Controllers\CustomerController::class,
    );
   
    foreach ($routeArray as $key => $value) {
        Route::prefix($key)->group(function() use($key, $value) {
            Route::get('/', [$value, 'index'])->name($key);
            Route::post('/addOrEdit', [$value, 'modalAddEdit'])->name($key.'.add.edit');
            Route::post('/status', [$value, 'changeStatus'])->name($key.'.status');
            Route::post('/delete', [$value, 'delete'])->name($key.'.delete');
            Route::post('/save', [$value, 'saveForm'])->name($key.'.save');
            Route::get('/export/excel', [$value, 'export'])->name($key.'.export.excel');
            Route::get('/export/pdf', [$value, 'exportPdf'])->name($key.'.export.pdf');
        });
    }

    Route::prefix('coupon')->group(function(){
        Route::get('/coupon-gendrate', [App\Http\Controllers\Offers\CouponController::class, 'couponGendrate'])->name('coupon.coupon-gendrate');
        Route::post('/coupon-apply', [App\Http\Controllers\Offers\CouponController::class, 'couponType'])->name('coupon.coupon-apply'); 
    });

    Route::prefix('products')->group(function(){
        Route::get('/', [App\Http\Controllers\Product\ProductController::class, 'index'])->name('products'); 
        Route::get('/add/{id?}', [App\Http\Controllers\Product\ProductController::class, 'addEditPage'])->name('products.add.edit'); 
        Route::post('/status', [App\Http\Controllers\Product\ProductController::class, 'changeStatus'])->name('products.status');
        Route::post('/delete', [App\Http\Controllers\Product\ProductController::class, 'delete'])->name('products.delete');
        Route::post('/save', [App\Http\Controllers\Product\ProductController::class, 'saveForm'])->name('products.save');
        Route::post('/remove/image', [App\Http\Controllers\Product\ProductController::class, 'removeImage'])->name('products.remove.image');
        Route::post('/remove/brochure', [App\Http\Controllers\Product\ProductController::class, 'removeBrochure'])->name('products.remove.brochure');
        Route::post('/upload/brochure', [App\Http\Controllers\Product\ProductController::class, 'uploadBrochure'])->name('products.upload.brochure');
        Route::post('/upload/gallery', [App\Http\Controllers\Product\ProductController::class, 'uploadGallery'])->name('products.upload.gallery');
        Route::get('/export/excel', [App\Http\Controllers\Product\ProductController::class, 'export'])->name('products.export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\Product\ProductController::class, 'exportPdf'])->name('products.export.pdf');

        Route::post('/attribute/row', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'getAttributeRow'])->name('products.attribute.row'); 
        /***** Attribute set values */
        Route::get('/attribute', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'index'])->name('product-attribute'); 
        Route::post('/attribute/addOrEdit', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'modalAddEdit'])->name('product-attribute.add.edit');
        Route::post('/attribute/status', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'changeStatus'])->name('product-attribute.status');
        Route::post('/attribute/delete', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'delete'])->name('product-attribute.delete');
        Route::post('/attribute/save', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'saveForm'])->name('product-attribute.save');
        Route::get('/attribute/export/excel', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'export'])->name('product-attribute.export.excel');
        Route::get('/attribute/export/pdf', [App\Http\Controllers\Product\ProductAttributeSetController::class, 'exportPdf'])->name('product-attribute.export.pdf');
        /****** Product Collection */
        Route::get('/collection', [App\Http\Controllers\Product\ProductCollectionController::class, 'index'])->name('product-collection'); 
        Route::post('/collection/addOrEdit', [App\Http\Controllers\Product\ProductCollectionController::class, 'modalAddEdit'])->name('product-collection.add.edit');
        Route::post('/collection/status', [App\Http\Controllers\Product\ProductCollectionController::class, 'changeStatus'])->name('product-collection.status');
        Route::post('/collection/delete', [App\Http\Controllers\Product\ProductCollectionController::class, 'delete'])->name('product-collection.delete');
        Route::post('/collection/save', [App\Http\Controllers\Product\ProductCollectionController::class, 'saveForm'])->name('product-collection.save');
        Route::get('/collection/export/excel', [App\Http\Controllers\Product\ProductCollectionController::class, 'export'])->name('product-collection.export.excel');
        Route::get('/collection/export/pdf', [App\Http\Controllers\Product\ProductCollectionController::class, 'exportPdf'])->name('product-collection.export.pdf');
    });

    Route::post('/getProduct/category/list', [App\Http\Controllers\CommonController::class, 'getProductCategoryList'])->name('common.category.dropdown');
    Route::post('/getProduct/brand/list', [App\Http\Controllers\CommonController::class, 'getProductBrandList'])->name('common.brand.dropdown');
    Route::post('/getProduct/dynamic/list', [App\Http\Controllers\CommonController::class, 'getProductDynamicList'])->name('common.dynamic.dropdown');

    Route::prefix('customer')->group(function(){
        Route::get('/coupon-gendrate', [App\Http\Controllers\CustomerController::class, 'couponGendrate'])->name('customer.coupon-gendrate');
        Route::post('/coupon-apply', [App\Http\Controllers\CustomerController::class, 'couponType'])->name('customer.coupon-apply');
        Route::get('/customer/view/{id}', [App\Http\Controllers\CustomerController::class, 'view'])->name('customer.view');
        Route::get('/add-address', [App\Http\Controllers\CustomerController::class, 'addAddress'])->name('customer.add-address');
        Route::post('/address', [App\Http\Controllers\CustomerController::class, 'customerAddress'])->name('customer.address');
        Route::post('/address/list', [App\Http\Controllers\CustomerController::class, 'addressList'])->name('customer.address.list');
        Route::post('/address/delete', [App\Http\Controllers\CustomerController::class, 'addressDelete'])->name('customer.delete');
    });

    Route::prefix('video-booking')->group(function(){
        Route::post('/addOrEdit', [App\Http\Controllers\VideoBookingController::class, 'modalAddEdit'])->name('video-booking.add.edit');
        Route::post('/status', [App\Http\Controllers\VideoBookingController::class, 'changeStatus'])->name('video-booking.status');
        Route::post('/delete', [App\Http\Controllers\VideoBookingController::class, 'delete'])->name('video-booking.delete');
        Route::post('/save', [App\Http\Controllers\VideoBookingController::class, 'saveForm'])->name('video-booking.save');
        Route::get('/export/excel', [App\Http\Controllers\VideoBookingController::class, 'export'])->name('video-booking.export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\VideoBookingController::class, 'exportPdf'])->name('video-booking.export.pdf');
    });
    Route::prefix('banner')->group(function(){
        Route::post('/addOrEdit', [App\Http\Controllers\BannerController::class, 'modalAddEdit'])->name('banner.add.edit');
        Route::post('/status', [App\Http\Controllers\BannerController::class, 'changeStatus'])->name('banner.status');
        Route::post('/delete', [App\Http\Controllers\BannerController::class, 'delete'])->name('banner.delete');
        Route::post('/save', [App\Http\Controllers\BannerController::class, 'saveForm'])->name('banner.save');
        Route::get('/export/excel', [App\Http\Controllers\BannerController::class, 'export'])->name('banner.export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\BannerController::class, 'exportPdf'])->name('banner.export.pdf');
    });
});

Route::get('razorpay-payment', [RazorpayPaymentController::class, 'index']);
Route::post('razorpay/process', [RazorpayPaymentController::class, 'razorpay_response'])->name('razorpay.payment.store');
Route::any('/payment/failed', [RazorpayPaymentController::class, 'fail_page'])->name('fail.page');