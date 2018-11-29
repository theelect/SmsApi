<?php


Route::any('/', function(){

	return response()->json(['status' => true, 'data' => 'https://documenter.getpostman.com/view/1050902/RzfasXPc']);

})->name('login');


Route::get('cron', 'Cron\MessageController@run');
Route::get('pull', 'PullingController@run');


