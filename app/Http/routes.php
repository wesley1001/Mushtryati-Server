<?php

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
Route::group(['prefix' => 'api/v1'], function () {

    Route::post('auth/login', 'Auth\AuthController@postLogin');
    Route::post('auth/register', 'Auth\AuthController@postRegister');
    Route::post('auth/activate', 'Auth\AuthController@postActivate');
    Route::post('auth/login/token', 'Auth\AuthController@loginUsingToken');

    Route::resource('medias', 'MediaController');
    Route::get('medias/{mediaID}/comments','MediaController@getMediaComments');
    Route::get('medias/{mediaID}/favorites','MediaController@getMediaFavorites');

    Route::resource('users', 'UserController');
    Route::get('users/{id}/medias', 'UserController@getUserMedias');
    Route::get('users/{id}/followers', 'UserController@getUserFollowers');
    Route::get('users/{id}/followings', 'UserController@getUserFollowings');
    Route::get('favorites','ProfileController@getFavorites');
    Route::get('downloads','ProfileController@getDownloads');
    Route::post('medias/comment','ProfileController@commentMedia');
    Route::post('medias/favorite','ProfileController@favoriteMedia');
    Route::post('medias/download','ProfileController@downloadMedia');
    Route::post('follow','ProfileController@followUser');
});

Route::group(['middleware' => ['web']], function () {
    //
});
