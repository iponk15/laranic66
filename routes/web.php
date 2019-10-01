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
Auth::routes();

// start login
Route::post('actionlogin','Auth\LoginController@actionlogin');
Route::get('logout','Auth\LoginController@logout');
// end login

Route::any('/','Dashboard@index');

Route::group(['middleware' => 'auth'], function(){
	Route::any('dashboard', ['as' => 'dashboard.index', 'uses' => 'Dashboard@index']);
	// Route::get('page404', ['as' => 'page404.index', 'uses' => 'Page404@page404']);
	Route::resource('page404', 'Page404');
	// Route::get('blocked', ['as' => 'blocked.index', 'uses' => 'Blocked@blocked']);
	Route::resource('blocked', 'Blocked');
	
	
	// Start route Users`
    Route::group(['as' => 'users.', 'prefix' => 'users', 'namespace' => 'Users'], function () {
        Route::post('table', ['as' => 'table', 'uses' => 'UsersController@table']);
		Route::post('{id}/destroy', ['as' => 'destroy', 'uses' => 'UsersController@destroy']);
    });
	Route::resource('users', 'Users\UsersController');
	// End route users
	
	// Start route Menus`
    Route::group(['as' => 'menus.', 'prefix' => 'menus', 'namespace' => 'Menus'], function () {
        Route::post('table', ['as' => 'table', 'uses' => 'MenusController@table']);
		Route::post('{id}/destroy', ['as' => 'destroy', 'uses' => 'MenusController@destroy']);
		Route::post('store_parent', ['as' => 'store_parent', 'uses' => 'MenusController@store_parent']);
		Route::get('detail', ['as' => 'detail', 'uses' => 'MenusController@detail']);
		Route::get('preview_menu', ['as' => 'preview_menu', 'uses' => 'MenusController@preview_menu']);
		Route::post('{id}/update_menus', ['as' => 'update_menus', 'uses' => 'MenusController@update_menus']);
    });
	Route::resource('menus', 'Menus\MenusController');
	// End route Menus

	// start route Config
	Route::group(['as' => 'configurs.', 'prefix' => 'configurs'], function () {

		// Start route permission
		Route::group(['as' => 'permission.', 'prefix' => 'permission', 'namespace' => 'configurs'], function () {
			Route::post('table', ['as' => 'table', 'uses' => 'PermissionController@table']);
			Route::post('destroy/{permission_id}', ['as' => 'destroy', 'uses' => 'PermissionController@destroy']);
		});
		Route::resource('permission', 'Configurs\PermissionController')->except(['show', 'destroy']);
		// End route permission

		// Start route Fetch
		Route::group(['as' => 'fetch.', 'prefix' => 'fetch', 'namespace' => 'configurs'], function () {
			Route::get('sample', ['as' => 'sample', 'uses' => 'FetchController@selectModule']);
		});
		Route::resource('fetch', 'Configurs\FetchController');
		// End route Fetch

	});
	// end route Config
});

