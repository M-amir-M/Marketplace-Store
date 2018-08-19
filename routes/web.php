<?php

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

use App\Category;
use App\Product;
use App\User;
use phplusir\smsir\Smsir;

Route::get('/', function () {
    return view('pages');
});
Route::get('/login', function () {
    return view('pages');
})->name('login');

Route::get('/home', function (){
    return view('pages');
});

Route::post('/login', 'Auth\LoginController@login');
Route::post('/register', 'Auth\RegisterController@register');

// Authentication Routes...
$this->post('logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
$this->post('password/reset', 'Auth\ResetPasswordController@reset');

Route::get('/v1/categories/get-categories', 'CategoriesController@getCategories');

Route::get('/v1/products/get-products', 'ProductsController@getProducts');
Route::get('/v1/products/get-home-products', 'ProductsController@getHomeProducts');
Route::get('/v1/products/get-cart-products/{cart}', 'ProductsController@getCartProducts');
Route::get('/v1/products/filter/{term}/{page}/{cats}/{brands}', 'ProductsController@search');
Route::get('/v1/products/get-product/{id}', 'ProductsController@getProduct');
Route::get('/v1/products/get-package/{id}', 'ProductsController@getPackage');
Route::get('/v1/packages/get-package/{id}', 'ProductsController@package');
Route::get('/v1/packages/get-packages', 'ProductsController@getPackages');

Route::get('/v1/brands/get-brands', 'BrandController@getBrands');

Route::get('/v1/pages/get-page/{page}', 'PagesController@getPage');

Route::get('/v1/informations/get-active-infos', 'InformationsController@getActiveInfos');

Route::get('/v1/settings/get-settings', 'SettingsController@getSettings');

Route::get('/v1/users/check-auth', function (){
    return auth()->check() ? 'success':'error';
});

Route::get('/v1/users/get-provinces', 'UserController@getProvinces');
Route::get('/v1/users/get-cities/{province_id}', 'UserController@getCities');
Route::post('/v1/users/reset-password', 'UserController@resetPassword');
Route::post('/v1/users/reset-password/verify', 'UserController@resetPasswordVerify');

Route::get('/v1/carousels/get-carousels', 'CarouselController@getCarousels');

Route::get('/v1/orders/cart', 'OrdersController@orderCart');

Route::post('/v1/verify', 'Auth\RegisterController@verify');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', function () {
        return view('admin');
    });

//    Route::get('/checkout', function () {
//        return view('checkOut');
//    });

    Route::get('/v1/users/filter/{name}/{page}/{role}', 'UserController@search');
    Route::get('/v1/users/get-auth', 'UserController@getAuth');
    Route::post('/v1/users/store', 'UserController@store');
    Route::post('/v1/users/name/store', 'UserController@storeName');
    Route::post('/v1/users/update', 'UserController@update');
    Route::post('/v1/users/delete', 'UserController@delete');
    Route::post('/v1/users/profile/update', 'UserController@ProfileUpdate');
    Route::post('/v1/users/password/update', 'UserController@PasswordUpdate');

    Route::get('/v1/products/get-category/{category_id}', 'ProductsController@getCategory');
    Route::post('/v1/products/store', 'ProductsController@store');
    Route::post('/v1/products/update', 'ProductsController@update');
    Route::post('/v1/products/delete', 'ProductsController@delete');
    Route::post('/v1/products/change-favorite', 'ProductsController@changeFavorite');
    Route::get('/v1/products/get-favorites', 'ProductsController@getFavorite');
    Route::post('/v1/products/change-rate', 'ProductsController@changeRate');
    Route::post('/v1/products/packages/add-to-package', 'ProductsController@addToPackage');
    Route::post('/v1/products/packages/delete-from-package', 'ProductsController@deleteFromPackage');

    Route::get('/v1/products/packages/filter/{term}/{page}', 'ProductsController@searchPackage');
    Route::post('/v1/products/packages/store', 'ProductsController@storePackage');
    Route::post('/v1/products/change-status', 'ProductsController@changeStatus');
    Route::post('/v1/products/packages/update', 'ProductsController@updatePackage');
    Route::post('/v1/products/packages/delete', 'ProductsController@deletePackage');

    Route::post('/v1/addresses/store', 'AddressesController@store');
    Route::post('/v1/addresses/update', 'AddressesController@update');

    Route::post('/v1/categories/store', 'CategoriesController@store');
    Route::post('/v1/categories/update', 'CategoriesController@update');
    Route::post('/v1/categories/delete', 'CategoriesController@delete');

    Route::get('/v1/orders/get-orders', 'OrdersController@getOrders');
    Route::post('/v1/orders/store', 'OrdersController@store')->name('orders.store');
    Route::post('/v1/orders/change-status', 'OrdersController@changeStatus');
    Route::post('/orders/payment/checker' , 'OrdersController@checker');

    Route::post('/v1/brands/store', 'BrandController@store');
    Route::post('/v1/brands/update', 'BrandController@update');
    Route::post('/v1/brands/delete', 'BrandController@delete');

    Route::get('/v1/pages/get-pages', 'PagesController@getPages');
    Route::post('/v1/pages/store', 'PagesController@store');
    Route::post('/v1/pages/update', 'PagesController@update');
    Route::post('/v1/pages/delete', 'PagesController@delete');

    Route::get('/v1/informations/get-informations', 'InformationsController@getInformations');
    Route::post('/v1/informations/change-status', 'InformationsController@changeStatus');
    Route::post('/v1/informations/store', 'InformationsController@store');
    Route::post('/v1/informations/update', 'InformationsController@update');
    Route::post('/v1/informations/delete', 'InformationsController@delete');

    Route::post('/v1/carousels/change-status', 'CarouselController@changeStatus');
    Route::post('/v1/carousels/store', 'CarouselController@store');
    Route::post('/v1/carousels/update', 'CarouselController@update');
    Route::post('/v1/carousels/delete', 'CarouselController@delete');

    Route::post('/v1/settings/update', 'SettingsController@update');

});

Route::get('/test',function(){
   auth()->loginUsingId(1);
   return \App\Package::with('products')->first();
});

Route::get('/dashboard/{vue?}', function () {
    return view('admin');
})->where('vue', '[\/\w\.-]*');

Route::get('/{vue?}', function () {
    return view('pages');
})->where('vue', '.*');




