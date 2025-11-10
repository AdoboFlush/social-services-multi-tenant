<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'GuestController@index')->name('guest.landing');
Route::get('login', 'Auth\MemberLoginController@showLoginForm')->name('guest.login');
Route::post('login', 'Auth\MemberLoginController@login')->name('guest.login');
Route::get('logout', 'Auth\MemberLoginController@logout')->name('guest.logout');
Route::post('logout', 'Auth\MemberLoginController@logout')->name('guest.logout');

Route::post('check-code', 'GuestController@checkMemberCode')->name('check.code');
Route::get('validate-code', 'GuestController@validateMemberCode')->name('validate.code');
Route::get('register', 'GuestController@register')->name('guest.register');
Route::post('register', 'GuestController@validateRegistration')->name('guest.register');

Route::group(['middleware' => ['auth']], function () {
    Route::prefix('profile')->group(function () {
        Route::get('/', 'GuestController@profile')->name('guest.profile');
        Route::get('/view/{id}', 'GuestController@previewID')->name('guest.profile.id');
        Route::post('/update', 'GuestController@updateProfile')->name('guest.profile.update');
        Route::get('/edit', 'GuestController@edit')->name('guest.profile.edit');
        Route::post('/store', 'GuestController@storeProfile')->name('guest.profile.store');
        Route::get('/create', 'GuestController@create')->name('guest.profile.create');
        Route::get('/assistance', 'GuestController@assistance')->name('guest.profile.assistance');
        Route::get('/event', 'GuestController@event')->name('guest.profile.event');
    });
});