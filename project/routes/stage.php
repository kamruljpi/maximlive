<?php
Route::group(['middleware' => 'auth'], function () {

	Route::get('stage','StageController@index')->name('stage_list_view');
	Route::get('stage_create','StageController@create')->name('stage_create_view');
	Route::post('stage_create','StageController@store')->name('stage_create_action');
	Route::get('stage_edit/{id?}','StageController@edit')->name('stage_edit_view');
	Route::post('stage_update/{id?}','StageController@update')->name('stage_edit_action');
	Route::get('stage_delete/{id?}','StageController@destroy')->name('stage_delete_action');
});