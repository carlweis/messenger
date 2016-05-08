<?php


Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::group(['middleware' => ['web', 'auth']], function() {
	Route::get('{username}', ['uses' => 'ProfileController@show', 'as' => 'profile']);

	Route::get('/dashboard', ['uses' => 'DashboardController@index', 'as' => 'dashboard']);
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
	Route::get('conversations/{id}', 'ConversationsController@show');
	Route::post('conversations', 'ConversationsController@store');

	// messages api routes
	Route::get('messages', 'MessagesController@index');
	Route::post('messages', 'MessagesController@store');
});

