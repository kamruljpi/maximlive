<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController\Ipo'], function () {
    Route::get('booking/ipo/cancel/{id}', 'IpoController@cancelIpo')->name('ipo_details_cancel_action');
});
Route::group(['middleware' => 'auth'], function(){
	Route::get('ipo/view/{id}','StoreController@ipoView')->name('ipo_view');
	Route::post('ipo/store','StoreController@ipoStore')->name('store_ipo');
});