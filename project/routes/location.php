<?php

Route::group(['middleware' => 'auth'], function () {
    Route::group(['middleware' => 'routeAccess'], function () {
    	Route::get('locations', 'LocationController@index')->name('location_list_view');
    	Route::get('location/create', 'LocationController@create')->name('location_create_view');
    	Route::get('location/edit/{id?}', 'LocationController@edit')->name('location_edit_view');
    	Route::post('store/location', 'LocationController@store')->name('location_save_action');

    	Route::post('update/location/{id?}', 'LocationController@update')->name('location_update_action');
    	Route::any('delete/location/{id?}', 'LocationController@delete')->name('location_delete_action');
    });
});