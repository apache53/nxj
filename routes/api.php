<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/', function () {
    return 'Welcome';
});
//接口
Route::group(['namespace'=>'Api','middleware'=>'checkYunSign'],function(){

    Route::group(['prefix' =>'api'],function(){

        Route::get('sound/getlist','SoundController@getlist');
        Route::any('sound/store','SoundController@store');

        Route::any('user/login','UserController@login');
        Route::any('user/info','UserController@info');
        Route::any('user/logout','UserController@logout');
        Route::any('user/list','UserController@lists');
        Route::any('user/add','UserController@add');
        Route::any('user/del','UserController@del');

        Route::any('scenic/add','ScenicController@add');
        Route::any('scenic/list','ScenicController@lists');
        Route::any('scenic/del','ScenicController@del');

        Route::any('file/upload','FileController@upload');
        Route::any('file/scenic_upload','FileController@scenic_upload');

        Route::any('driving/report','DrivingController@report');
        Route::any('driving/list','DrivingController@lists');

        Route::any('voice/send','VoiceController@send');
        Route::any('voice/senduserlist','VoiceController@senduserlist');
        Route::any('voice/sendlist','VoiceController@sendlist');
        Route::any('voice/receivelist','VoiceController@receivelist');
    });
});

//后台
Route::group(['namespace'=>'Backend','middleware'=>'checkBackendSign'],function(){

    Route::group(['prefix' =>'backend'],function(){
        Route::get('index/login','IndexController@login');
        Route::post('index/dologin','IndexController@dologin');
        Route::get('index/vcode','IndexController@vcode');

    });
});

Route::group(['namespace'=>'Web'],function(){

    Route::group(['prefix' =>'web'],function(){
        Route::any('login','WebController@login');
        Route::any('scenic','WebController@scenic');

    });
});


