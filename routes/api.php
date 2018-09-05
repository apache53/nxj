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