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
    return view('welcome');
});

Route::get('/upload', 'FormController@index');
Route::post('/upload', 'FormController@upload');
Route::get('/check/{filename?}', 'FormController@check');
Route::get('/get/{filename?}', 'FormController@get');
Route::get('/delete/{filename?}', 'FormController@delete');
Route::get('/delete_multiple', 'FormController@delete_multiple');
