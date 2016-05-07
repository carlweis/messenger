<?php


Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::group(['middleware' => ['web']], function() {
	Route::get('/home', 'HomeController@index');
});

Route::group([
	'middleware' => ['api', 'auth:api'],
	'prefix' => 'api/v1',
	'namespace' => 'API\V1'], function() {

	// user api routes
	Route::get('/', function() {
		return Auth::guard('api')->user();
	});
	Route::get('users', 'UsersController@index');

	// conversation api routes
	Route::get('conversations', 'ConversationsController@index');
	Route::post('conversations', 'ConversationsController@store');

});

