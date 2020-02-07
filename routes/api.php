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

/**
 *Shoe 
 */
Route::post('login', 'AuthController@login');
Route::post('register', 'API\UserController@register');

Route::post('reset-password', 'ResetPasswordController@resetPassword');
Route::post('call-reset-password', 'ResetPasswordController@callResetPassword');

Route::get('list/shoes','ShoeController@getShoes');

Route::get('shoes', 'ShoeController@index');
Route::get('shoes/{shoe}', 'ShoeController@show');
Route::post('shoes', 'ShoeController@store');
Route::put('shoes/{shoe}', 'ShoeController@update');
Route::delete('shoes/{shoe}', 'ShoeController@destroy');

Route::group(
    ['middleware' => 'auth:api', 'cors'],
    function () {
        // Route::get('shoes', 'ShoeController@index');
        // Route::get('shoes/{shoe}', 'ShoeController@show');
        // Route::post('shoes', 'ShoeController@store');
        // Route::put('shoes/{shoe}', 'ShoeController@update');
        // Route::delete('shoes/{shoe}', 'ShoeController@destroy');
        Route::post('logout', 'AuthController@logout');
    }
);

