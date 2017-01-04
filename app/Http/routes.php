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

// GET
Route::get('/upload', 'FormController@index');
Route::get('/check/{filename?}', 'FormController@check');
Route::get('/get/{filename?}', 'FormController@get');
Route::get('/images/{filename?}', function($filename='default.jpg') {
	$path = storage_path('app/'.$filename);
	if (file_exists($path)) 
	{ 
    	return Response::download($path);
    }
});

// POST
Route::post('/upload', 'FormController@upload');

// DELETE
Route::delete('/delete/{filename?}', 'FormController@delete');
Route::delete('/delete_multiple/{cid?}', 'FormController@delete_multiple');
Route::delete('/delete_all', 'FormController@delete_all');
