<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');
Route::get('log-date/{date}', 'HomeController@logdate');
Route::get('filter_logs/{log_date}/{appId}/{user}/{level}/{search}', 'HomeController@filters');
Route::post('filter', 'HomeController@filters');
Route::post('sids', 'HomeController@sids');
Route::post('users', 'HomeController@getUsers');
Route::post('graphdata', 'HomeController@graphData');
Route::post('invoke', 'HomeController@invoke');
Route::get('invokelist', 'DashboardController@invoke_list');
Route::get('loglist', 'DashboardController@log_list');
Route::get('logdelete', 'DashboardController@log_delete');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
