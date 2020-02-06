<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/**
 *Shoe 
 */
Route::post('login', 'AuthController@login');
Route::post('register', 'API\UserController@register');

Route::post('reset-password', 'ResetPasswordController@resetPassword');
Route::post('call-reset-password', 'ResetPasswordController@callResetPassword');

Route::get('list/shoes','ShoeController@getShoes');

Route::resource('shoes', 'ShoeController');
Route::group(
    ['middleware' => 'auth:api', 'cors'],
    function () {
        Route::post('logout', 'AuthController@logout');
    }
);

