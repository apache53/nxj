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
        Route::get('user/index', 'UserController@index');
        Route::get('game/login', 'GameController@login');
        Route::get('game/pay', 'GameController@pay');
        Route::get('h5game/login', 'H5GameController@login');
        Route::get('h5game/pay', 'H5GameController@pay');
        Route::get('h5game/createOrderId', 'H5GameController@createOrderId');
        Route::get('h5game/report','H5GameController@report');
        Route::get('h5game/getAppInfo','H5GameController@getAppInfo');

        Route::get('sound/getlist','SoundController@getlist');
        Route::any('sound/store','SoundController@store');
    });
});

//后台
Route::group(['namespace'=>'Backend','middleware'=>'checkBackendSign'],function(){

    Route::group(['prefix' =>'backend'],function(){
        Route::get('index/login','IndexController@login');
    });
});