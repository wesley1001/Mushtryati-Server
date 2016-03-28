<?php

Route::get('/', function () {
    return view('welcome');
});


Route::get('test',function() {
    $ffmpeg = FFMpeg\FFMpeg::create([
        'ffmpeg.binaries'  => '/bin/ffmpeg',
        'ffprobe.binaries' => '/bin/ffprobe'
    ]);

    $uploadPath =  public_path().'/uploads/medias/';
    $demoMovie =$uploadPath.'demo.mov';
    $video = $ffmpeg->open($demoMovie);
//    $video
//        ->filters()
//        ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
//        ->synchronize();
    $video
        ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(1))
        ->save($uploadPath.'frame.jpg');
//    $video
//        ->save(new FFMpeg\Format\Video\X264(), 'export-x264.mp4')
//        ->save(new FFMpeg\Format\Video\WMV(), 'export-wmv.wmv')
//        ->save(new FFMpeg\Format\Video\WebM(), 'export-webm.webm');
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
