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
// start route page 404
Route::get('page404','Page404@index');
Route::get('bloked','bloked@index');
// end route page 404
// start login
Route::post('actionlogin','Auth\LoginController@actionlogin');
Route::get('logout','Auth\LoginController@logout');
// end login

Route::any('/','Dashboard@index');

Route::group(['middleware' => 'auth'], function(){
	// // Start master data roles
	// Route::any('roles', 'Roles@index');
	// Route::any('roles/select', 'Roles@select');
	// Route::any('roles/create', 'Roles@create');
	// Route::post('roles/store', 'Roles@store');
	// Route::any('roles/edit/{id}', 'Roles@edit');
	// Route::post('roles/update/{id}', 'Roles@update');
	// Route::any('roles/destroy/{id}', 'Roles@destroy');
	// Route::get('roles/preview_menu/{id?}', 'Roles@preview_menu');
	// // End master data roles

	// Start master data roles
	Route::any('roles', 'RoleCtrl@index');
	Route::any('roles/select', 'RoleCtrl@select');
	Route::any('roles/create', 'RoleCtrl@create');
	Route::post('roles/store', 'RoleCtrl@store');
	Route::any('roles/edit/{id}', 'RoleCtrl@edit');
	Route::post('roles/update/{id}', 'RoleCtrl@update');
	Route::any('roles/destroy/{id}', 'RoleCtrl@destroy');
	Route::get('roles/preview_menu/{id?}', 'RoleCtrl@preview_menu');
	// End master data roles


	// start sourcing management
	Route::any('sourcing', 'Sourcing@index');
	Route::any('sourcing/select', 'Sourcing@select');
	Route::any('sourcing/create', 'Sourcing@create');
	Route::post('sourcing/store', 'Sourcing@store');
	Route::any('sourcing/edit/{id}', 'Sourcing@edit');
	Route::post('sourcing/update/{id}', 'Sourcing@update');
	Route::any('sourcing/destroy/{id}', 'Sourcing@destroy');
	// end sourcing management

	// Master data menu
	Route::any('menus', 'Menus@index');
	Route::get('menus_preview', 'Menus@preview_menu');
	Route::get('menus_detail/{id}', 'Menus@detail');
	Route::post('menus_store', 'Menus@store_parent');
	Route::post('menus_update/{id}', 'Menus@update_menus');

	// Start master data user
	Route::group(['prefix' => 'user'], function () {
		Route::any('/', 'Users@index');
		Route::any('/select', 'Users@select');
		Route::any('/create', 'Users@create');
		Route::post('/store', 'Users@store');
		Route::any('/edit/{id}', 'Users@edit');
		Route::post('/update/{id}', 'Users@update');
		Route::any('/destroy/{id}', 'Users@destroy');
		Route::any('/profile', 'Users@profile');
		Route::post('/changeProfile/{id}', 'Users@changeProfile');

	});
	// End master data user

	// Start master data venpolicy
	Route::any('venpolicy', 'venpolicy@index');
	Route::any('venpolicy/select', 'venpolicy@select');
	Route::any('venpolicy/create', 'venpolicy@create');
	Route::post('venpolicy/store', 'venpolicy@store');
	Route::any('venpolicy/edit/{id}', 'venpolicy@edit');
	Route::post('venpolicy/update/{id}', 'venpolicy@update');
	Route::any('venpolicy/status/{id}/{status}', 'venpolicy@status');
	Route::any('venpolicy/destroy/{id}', 'venpolicy@destroy');
	// End master data roles

	// Start master data Category & Sub Category
	Route::any('category', 'category@index');
	Route::any('category/select', 'category@select');
	Route::any('category/create', 'category@create');
	Route::post('category/store', 'category@store');
	Route::any('category/edit/{id}', 'category@edit');
	Route::post('category/update/{id}', 'category@update');
	Route::any('category/status/{id}/{status}', 'category@status');
	Route::any('category/destroy/{id}', 'category@destroy');
	// End master data Category & Sub Category

});

