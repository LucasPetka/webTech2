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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('classes', 'ClasshController@index');

Route::get('getall', 'ClasshController@getAll');
Route::get('getclass/{id}', 'ClasshController@show');
Route::delete('deleteclass/{id}', 'ClasshController@destroy');
Route::get('superclasses/{id}', 'ClasshController@superclasses');
Route::get('subclasses/{id}', 'ClasshController@subclasses');


Route::post('addclass', 'ClasshController@store');
Route::put('updateclass', 'ClasshController@store');
