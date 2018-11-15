<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController\History\Restore'], function () {
	Route::group(['middleware' => 'routeAccess'], function () {
		Route::get('restore','RestoreData@index')->name('restore');
		Route::get('restore/{type?}','RestoreData@sentListRequest')->name('sent_list_request');
		Route::get('restore/{type?}/{id?}','RestoreData@piRestoreRequest')->name('restore_request');
		Route::post('restore/find','RestoreData@sentFindRequest')->name('sent_find_request');
	});
});