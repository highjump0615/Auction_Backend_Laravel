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

Route::group(['prefix'=>'api/v1'], function() {

    Route::group(['middleware' => ['api']], function() {
        // user
        Route::post('/signup', 'Auth\AuthController@register');
        Route::post('/login', 'Auth\AuthController@login');
    });

    Route::group(['middleware' => ['auth:api']], function() {
        // item
        Route::post('/uploaditem', 'ItemController@upload');
        Route::get('/explore', 'ItemController@getExplore');
        Route::get('/category/{id}', 'ItemController@getCategory');
        Route::get('/search/{keyword}', 'ItemController@getSearch');

        // user
        Route::get('/user', 'UserController@getUser');

        // bid
        Route::post('/bid', 'ItemController@placeBid');
        Route::get('/maxbid/{id}', 'ItemController@getMaxBidPrice');

        // comment
        Route::get('/comment', 'ItemController@getComment');
        Route::post('/addcomment', 'ItemController@addComment');
    });
});
