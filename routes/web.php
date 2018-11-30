<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'PageController@welcome')->name('welcome');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/chat', 'PageController@chat')->name('chat');

    Route::get('/rooms', 'Api\RoomController@rooms');
    Route::post('/rooms/create', 'Api\RoomController@create');
    Route::delete('/room/{id}', 'Api\RoomController@delete')->middleware('room-owner');

    Route::get('/room/{room}/messages', 'Api\MessageController@messages');
    Route::post('/room/{room}/messages', 'Api\MessageController@create');
});
