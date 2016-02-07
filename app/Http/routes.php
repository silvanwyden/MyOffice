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

Route::get('/', function () {
	return redirect('/auth/login');
})->middleware('guest');

// Task Routes
Route::get('/tasks', 'TaskController@index');
Route::get('/task', 'TaskController@create');
Route::post('/task', 'TaskController@store');
Route::get('/task/{task}/update', 'TaskController@update');
Route::get('/task/{task}/delete', 'TaskController@destroy');
Route::get('/task/{task}/done', 'TaskController@done');

// Counters Routes
Route::get('/counters', 'CounterController@index');
Route::get('/counter', 'CounterController@create');
Route::post('/counter', 'CounterController@store');
Route::get('/counter/{counter}/update', 'CounterController@update');
Route::get('/counter/{counter}/delete', 'CounterController@destroy');
Route::get('/counter/{counter}/done', 'CounterController@done');

// Authentication Routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration Routes...
//Route::get('auth/register', 'Auth\AuthController@getRegister');
//Route::post('auth/register', 'Auth\AuthController@postRegister');
