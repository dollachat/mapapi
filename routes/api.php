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

Route::post('/findrestaurant', 'Api\MapController@index');
Route::post('/testapi', 'Api\MapController@test');
Route::get('/get_photo/{maxwidth}/{photoreference}', 'Api\MapController@get_photo');
Route::post('/next_set', 'Api\MapController@next_set');
