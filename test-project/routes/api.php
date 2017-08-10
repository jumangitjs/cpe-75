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

Route::post('/entry', [
    'uses' => 'EntryController@postEntry',
    'middleware' => 'auth.jwt'
]);

Route::get('/entries', [
    'uses' => 'EntryController@getEntries'
]);

Route::put('/entry/{id}', [
    'uses' => 'EntryController@putEntry',
    'middleware' => 'auth.jwt'
]);

Route::delete('/entry/{id}', [
    'uses' => 'EntryController@deleteEntry',
    'middleware' => 'auth.jwt'
]);

Route::post('/user/login', [
    'uses' => 'UserController@signIn'
]);

Route::post('/user/logout', function() {
    JWTAuth::parseToken()->invalidate();
});

Route::post('/user', [
    'uses' => 'UserController@signUp'
]);

Route::get('/users', [
    'uses' => 'UserController@getUsers',
    'middleware' => 'auth.jwt'
]);

Route::delete('/user/{id}', [
    'uses' => 'UserController@deleteUser',
    'middleware' => 'auth.jwt'
]);

Route::put('/user/{id}', [
    'uses' => 'UserController@putUser',
    'middleware' => 'auth.jwt'
]);