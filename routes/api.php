<?php

use Illuminate\Http\Request;


Route::middleware('auth:api')->get('/user', function (Request $request) {

    return $request->user();
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'Api'], function(){

	Route::get('messages', 'MessagesController@index');
	Route::get('messages/{type?}', 'MessagesController@index');
	Route::get('messages/count/analysis', 'MessagesController@analysis');

	Route::post('message', 'MessageController@save');
	Route::post('message/{id?}/destroy', 'MessageController@deleteMessage');
	Route::get('message/{id?}', 'MessageController@index');

});

Route::get('contacts-defaults', 'Api\ContactsController@contactsDefaults');
Route::get('wards', 'Api\ContactsController@wards');

