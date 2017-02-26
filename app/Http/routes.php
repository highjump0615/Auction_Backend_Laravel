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
        Route::post('/contact', 'ItemController@contact');

        // user
        Route::get('/user/{id}', 'UserController@getUser');
        Route::get('/userinfo', 'UserController@getUserInfo');
        Route::post('/saveprofile', 'UserController@saveProfile');
        Route::post('/savesetting', 'UserController@saveSetting');

        // bid
        Route::post('/bid', 'ItemController@placeBid');
        Route::get('/maxbid/{id}', 'ItemController@getMaxBidPrice');
        Route::post('/giveup', 'ItemController@giveupBid');
        Route::post('/delete', 'ItemController@deleteBid');

        // comment
        Route::get('/comment', 'ItemController@getComment');
        Route::post('/addcomment', 'ItemController@addComment');

        // inbox
        Route::get('/inbox', 'InboxController@getInbox');
        Route::post('/deleteinbox', 'InboxController@deleteInbox');

        // rate
        Route::post('/rate', 'ItemController@setRate');
    });
});
