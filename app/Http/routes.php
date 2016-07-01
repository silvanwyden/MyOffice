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
Route::get('/tasks', ['middleware' => 'auth', 'uses' => 'TaskController@index']);
Route::get('/task', ['middleware' => 'auth', 'uses' => 'TaskController@create']);
Route::post('/task', ['middleware' => 'auth', 'uses' => 'TaskController@store']);
Route::get('/task/{task}/update', ['middleware' => 'auth', 'uses' => 'TaskController@update']);
Route::get('/task/{task}/delete', ['middleware' => 'auth', 'uses' => 'TaskController@destroy']);
Route::get('/task/{task}/done', ['middleware' => 'auth', 'uses' => 'TaskController@done']);
Route::post('/task/{task}/upload', ['middleware' => 'auth', 'uses' => 'TaskController@upload']);

// Counters Routes
Route::get('/counters', ['middleware' => 'auth', 'uses' => 'CounterController@index']);
Route::get('/counter', ['middleware' => 'auth', 'uses' => 'CounterController@create']);
Route::post('/counter', ['middleware' => 'auth', 'uses' => 'CounterController@store']);
Route::get('/counter/{counter}/update', ['middleware' => 'auth', 'uses' => 'CounterController@update']);
Route::get('/counter/{counter}/delete', ['middleware' => 'auth', 'uses' => 'CounterController@destroy']);
Route::get('/counter/stats', ['middleware' => 'auth', 'uses' => 'CounterController@stats']);
Route::get('/counter/stats_month', ['middleware' => 'auth', 'uses' => 'CounterController@stats_month']);


// Persons Routes
Route::get('/persons', ['middleware' => 'auth', 'uses' => 'PersonController@index']);
Route::get('/person', ['middleware' => 'auth', 'uses' => 'PersonController@create']);
Route::post('/person', ['middleware' => 'auth', 'uses' => 'PersonController@store']);
Route::get('/person/{person}/update', ['middleware' => 'auth', 'uses' => 'PersonController@update']);
Route::get('/person/{person}/delete', ['middleware' => 'auth', 'uses' => 'PersonController@destroy']);
Route::get('/persons/excel', ['middleware' => 'auth', 'uses' => 'PersonController@excel']);
Route::get('/persons/search', ['middleware' => 'auth', 'uses' => 'PersonController@search']);

// Passpack Routes
Route::get('/passpacks', ['middleware' => 'auth', 'uses' => 'PasspackController@index']);
Route::get('/passpack', ['middleware' => 'auth', 'uses' => 'PasspackController@create']);
Route::post('/passpack', ['middleware' => 'auth', 'uses' => 'PasspackController@store']);
Route::get('/passpack/{passpack}/update', ['middleware' => 'auth', 'uses' => 'PasspackController@update']);
Route::get('/passpack/{passpack}/delete', ['middleware' => 'auth', 'uses' => 'PasspackController@destroy']);

// Notes Routes
Route::get('/notes', ['middleware' => 'auth', 'uses' => 'NoteController@index']);
Route::get('/note', ['middleware' => 'auth', 'uses' => 'NoteController@create']);
Route::post('/note', ['middleware' => 'auth', 'uses' => 'NoteController@store']);
Route::get('/note/{note}/update', ['middleware' => 'auth', 'uses' => 'NoteController@update']);
Route::get('/note/{note}/delete', ['middleware' => 'auth', 'uses' => 'NoteController@destroy']);
Route::post('/note/{note}/upload', ['middleware' => 'auth', 'uses' => 'NoteController@upload']);

// Tag Routes
Route::get('/tags', ['middleware' => 'auth', 'uses' => 'TagController@index']);
Route::get('/tag', ['middleware' => 'auth', 'uses' => 'TagController@create']);
Route::post('/tag', ['middleware' => 'auth', 'uses' => 'TagController@store']);
Route::get('/tag/{tag}/update', ['middleware' => 'auth', 'uses' => 'TagController@update']);
Route::get('/tag/{tag}/delete', ['middleware' => 'auth', 'uses' => 'TagController@destroy']);
Route::post('/tags/search', ['middleware' => 'auth', 'uses' => 'TagController@search']);
Route::get('/tags/search', ['middleware' => 'auth', 'uses' => 'TagController@search']);

// Countercategory Routes
Route::get('/countercategories', ['middleware' => 'auth', 'uses' => 'CountercategoryController@index']);
Route::get('/countercategory', ['middleware' => 'auth', 'uses' => 'CountercategoryController@create']);
Route::post('/countercategory', ['middleware' => 'auth', 'uses' => 'CountercategoryController@store']);
Route::get('/countercategory/{countercategory}/update', ['middleware' => 'auth', 'uses' => 'CountercategoryController@update']);
Route::get('/countercategory/{countercategory}/delete', ['middleware' => 'auth', 'uses' => 'CountercategoryController@destroy']);

//FileEntry Routes
Route::get('/fileentries', ['middleware' => 'auth', 'uses' => 'FileEntryController@index']);
Route::get('/fileentries_img', ['middleware' => 'auth', 'uses' => 'FileEntryController@index_img']);
Route::post('/fileentry', ['middleware' => 'auth', 'uses' => 'FileEntryController@store']);
Route::get('/fileentry/get/{fileid}', ['middleware' => 'auth', 'uses' => 'FileEntryController@get']);
Route::get('/fileentry/open/{fileid}', ['middleware' => 'auth', 'uses' => 'FileEntryController@open']);
Route::get('/fileentry/open_thumb/{fileid}', ['middleware' => 'auth', 'uses' => 'FileEntryController@open_thumb']);
Route::get('/fileentry/delete/{fileentry}', ['middleware' => 'auth', 'uses' => 'FileEntryController@destroy']);
Route::post('/fileentries/upload', ['middleware' => 'auth', 'uses' => 'FileEntryController@upload']);


//common Routes
Route::get('/common/about', ['middleware' => 'auth', 'uses' => 'CommonController@about']);

// Warranty Routes
Route::get('/warranties', ['middleware' => 'auth', 'uses' => 'WarrantyController@index']);
Route::get('/warranty', ['middleware' => 'auth', 'uses' => 'WarrantyController@create']);
Route::post('/warranty', ['middleware' => 'auth', 'uses' => 'WarrantyController@store']);
Route::get('/warranty/{warranty}/update', ['middleware' => 'auth', 'uses' => 'WarrantyController@update']);
Route::get('/warranty/{warranty}/delete', ['middleware' => 'auth', 'uses' => 'WarrantyController@destroy']);
Route::post('/warranty/{warranty}/upload', ['middleware' => 'auth', 'uses' => 'WarrantyController@upload']);



// Authentication Routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration Routes...
//Route::get('auth/register', 'Auth\AuthController@getRegister');
//Route::post('auth/register', 'Auth\AuthController@postRegister');
