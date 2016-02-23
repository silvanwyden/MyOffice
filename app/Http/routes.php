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
Route::get('/counter/stats', 'CounterController@stats');

// Persons Routes
Route::get('/persons', 'PersonController@index');
Route::get('/person', 'PersonController@create');
Route::post('/person', 'PersonController@store');
Route::get('/person/{person}/update', 'PersonController@update');
Route::get('/person/{person}/delete', 'PersonController@destroy');
Route::get('/persons/excel', 'PersonController@excel');

// Passpack Routes
Route::get('/passpacks', 'PasspackController@index');
Route::get('/passpack', 'PasspackController@create');
Route::post('/passpack', 'PasspackController@store');
Route::get('/passpack/{passpack}/update', 'PasspackController@update');
Route::get('/passpack/{passpack}/delete', 'PasspackController@destroy');

// Notes Routes
Route::get('/notes', 'NoteController@index');
Route::get('/note', 'NoteController@create');
Route::post('/note', 'NoteController@store');
Route::get('/note/{note}/update', 'NoteController@update');
Route::get('/note/{note}/delete', 'NoteController@destroy');

// Tag Routes
Route::get('/tags', 'TagController@index');
Route::get('/tag', 'TagController@create');
Route::post('/tag', 'TagController@store');
Route::get('/tag/{tag}/update', 'TagController@update');
Route::get('/tag/{tag}/delete', 'TagController@destroy');
Route::post('/tags/search', 'TagController@search');
Route::get('/tags/search', 'TagController@search');


// Authentication Routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration Routes...
//Route::get('auth/register', 'Auth\AuthController@getRegister');
//Route::post('auth/register', 'Auth\AuthController@postRegister');
