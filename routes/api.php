<?php

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

Route::group(['middleware' => ['api', 'cors'], 'namespace' => 'Api\V1'], function() {
    Route::post('/register', 'User\RegisterController@register');
    Route::post('/login', 'User\LoginController@login');
    Route::post('/feedback', 'User\FeedbackController@question');
    Route::get('/logout', 'User\LogoutController@logout');
    Route::get('/activation/{token}', 'User\ActivateController@activate');
    Route::get('/new/email/{token}', 'User\ChangeEmailController@change');
    Route::delete('/new/email/cancel/{email}', 'User\ChangeEmailController@cancel');

    Route::post('/new/password', 'User\ResetPasswordController@reset');

    Route::put('/me', 'User\ProfileController@update');
    Route::get('/me', 'User\ProfileController@profile');
    Route::get('/me/status', 'User\ProfileController@status');
    Route::get('/me/roles', 'User\RoleController@get');
    Route::put('/me/product/{id}', 'User\ProductController@update');

    Route::get('/products', 'Product\ProductController@index');
    Route::put('/products/{id?}', 'Product\ProductController@save');

    Route::get('/dictionary', 'Dictionary\DictionaryController@index');
    Route::get('/dictionary/fragment/{lang}', 'Dictionary\DictionaryController@getFragments');
    Route::get('/country/search/{name}', 'Country\CountryController@search');

    Route::post('/product/{id}/pay', 'Payment\PayController@pay');
    Route::post('/product/demo', 'Payment\PayController@demo');

    Route::post('/pay/receive', 'Payment\ReceiveController@index');
});