<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController\History'], function () {
	Route::group(['middleware' => 'routeAccess'], function () {
		Route::get('restore','RestoreData@index')->name('restore');
		Route::get('restore/pi','RestoreData@getPiDeletedValue')->name('pi_deleted_data');
		Route::get('pi/restore/request/{id?}','RestoreData@piRestoreRequest')->name('pi_restore_request');
		Route::post('restore/find/request','RestoreData@restoreFindRequest')->name('restore_find_request');
	});
});