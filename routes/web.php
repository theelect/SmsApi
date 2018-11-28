<?php


Route::any('/', 'AuthController@login')->name('login');
Route::any('login', 'AuthController@login')->name('login');
Route::any('register', 'AuthController@register');
Route::get('auth/verify/{token}', 'AuthController@verify');
Route::get('auth/resend', 'AuthController@resend');
Route::get('auth/i/{user_id?}', 'AuthController@impersonate');
Route::any('logout', 'AuthController@logout');
Route::any('locals/{state_id}', 'HomeController@locals');


Route::group(['middleware' => 'auth'], function(){

	Route::get('home', 'HomeController@index');
	Route::get('settings', 'SettingsController@index');
	Route::post('settings/save', 'SettingsController@store');
	

	Route::get('transactions', 'TransactionsController@index');
	
	Route::group(['namespace' => 'Contacts'], function(){

		Route::get('contacts', 'ContactsController@index');
		Route::get('contact/{id?}', 'ContactsController@contact');
		Route::post('contact/{id?}', 'ContactsController@store');

		Route::get('contacts-upload-guide', 'UploadController@index');
		Route::post('contacts-upload', 'UploadController@store');

		Route::post('contacts-search', 'SearchController@index');

	});

	Route::group(['namespace' => 'Messages'], function(){

		Route::group(['prefix' => 'message'], function(){

			Route::any('new/{id?}/{random?}', 'BodyController@index');
			Route::get('recipients/{id?}/{random?}', 'RecipientController@index');
			Route::post('save-recipients', 'RecipientController@save');
			Route::get('options/{id?}/{random?}', 'OptionsController@index');
			Route::post('save-options', 'OptionsController@save');
			Route::get('review/{id?}/{random?}', 'ReviewController@index');
			Route::get('queue/{id?}', 'QueueController@index');

		});
		
		Route::get('messages', 'MessagesController@index');

		Route::get('messages/single/{id?}', 'MessagesController@single');

	});

});

Route::group(['prefix' => 'ajax'], function(){

	Route::any('age-brackets', 'Contacts\AgeBracketsController@index');
	Route::get('destory-age-bracket/{id?}', 'Contacts\AgeBracketsController@destroy');

	Route::any('groups', 'Contacts\GroupsController@index');
	Route::get('destory-group/{id?}', 'Contacts\GroupsController@destroy');

});


Route::get('cron', 'Cron\MessageController@run');


