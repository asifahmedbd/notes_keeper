<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});


Route::get('/reset-password', 'Auth\ResetPasswordController@index')->middleware('guest')->name('reset.password');
Route::post('/reset-password', 'Auth\ResetPasswordController@resetPassword')->middleware('guest')->name('request.reset.password');
Route::get('/revert-password/{token}', 'Auth\ResetPasswordController@revertPassword')->middleware('guest');



Auth::routes();



Route::group(['middleware'=> ['auth']],function () {


    Route::get('/test', 'TestController@index');



    /*
    * Dashboard Routes
    */

    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');



    /*
    * Profile Routes
    */

    Route::get('/profile', 'DashboardController@index')->name('profile');
    Route::get('/folder-details', 'DashboardController@getDetails')->name('getDetails');
    Route::get('/file-viewer', 'DashboardController@viewFile')->name('file.viewer');



});
