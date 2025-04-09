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
    Route::get('/test-office-html', 'TestController@testOfficeHtml');



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
    Route::get('/create-memo', 'DocumentController@createMemo')->name('memo.create');



    /*
    * Document Routes
    */

    Route::get('/create/document', 'DocumentController@create')->name('create.document');
    Route::post('/store/document', 'DocumentController@store')->name('store.document');
    Route::get('/get/category-structure', 'DocumentController@getCategoryStructure');



    /*
    * Category Routes
    */

    Route::post('/create/category', 'CategoryController@addCategory');
    Route::get('/get-folder-structure', 'CategoryController@getFolderStructure')->name('get-folder-structure');


    /*
    * Directory Scanner Routes
    */

    Route::get('/directory-scanner', 'DirectoryScannerController@index')->name('directory.scanner');
    Route::post('/directory-scanner', 'DirectoryScannerController@directoryScanner');

    /*File Upload Routes*/
    Route::post('/upload-file', 'DocumentController@uploadFile')->name('file.upload');

});
