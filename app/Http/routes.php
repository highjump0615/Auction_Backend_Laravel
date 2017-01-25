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

Route::group(['middleware' => ['web']], function() {
    Route::get('/', function () {
        return view('welcome');
    });
});

Route::group(['prefix'=>'api/v1', 'middleware' => ['api']], function() {
    // user
    Route::post('/signup', 'Auth\AuthController@register');
    Route::post('/login', 'Auth\AuthController@login');
});
