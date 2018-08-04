<?php

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {

    return 111; exit;
    return $request->user();
});*/


Route::group(['namespace'=>'Api'],function(){
    Route::get('/newsbulletin', 'NewsbulletinController@index');
    Route::get('/rightbulletin', 'RightbulletinController@index');
    Route::get('/indexbulletin', 'IndexbulletinController@index');
    Route::get('/windowsbulletin', 'WindowsbulletinController@index');
});
