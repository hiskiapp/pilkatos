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
Route::group(['middleware'=>'auth'], function(){
	Route::get('/', 'FrontController@getIndex');
	Route::post('/electy', 'FrontController@postData');
});

Route::get('{token}/set-password','FrontController@getForgot');
Route::post('set-password','FrontController@postForgot');
Route::get('/{path}/login', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout');
Route::get('log-out', 'FrontController@getLogout');
