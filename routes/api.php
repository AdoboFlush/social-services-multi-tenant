<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;


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


Route::group(['prefix' => 'auth'], function ($router) {
    // Route::post('register', 'API\AuthController@register');
    Route::post('login', 'API\AuthController@login');
    Route::post('logout', 'API\AuthController@logout');
    Route::post('refresh', 'API\AuthController@refresh');
    // Route::post('profile', 'API\AuthController@profile');
    // Route::post('update_profile', 'API\AuthController@update_profile');
    // Route::post('update_password', 'API\AuthController@update_password');
    // Route::post('update_profile_picture', 'API\AuthController@update_profile_picture');
});

