<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController\Os'], function () {
	Route::get('os/mrf/list', 'Mrf\MrfListController@mrfListView')->name('os_mrf_list_view');
	Route::get('os/mrf/report', 'Mrf\MrfListController@showMrfReport')->name('os_mrf_list_report_view');
	Route::get('os/mrf/report/{bid?}', 'Mrf\MrfListController@detailsViewForm')->name('os_mrf_details_view');
	Route::post('os/po/genarate', 'PoController@poGenarateView')->name('os_po_genarate_view');
	Route::post('os/po/genarate/action', 'PoController@storeOsPo')->name('os_po_genarate_report_action');
	Route::get('os/po/list', 'PoListController@opListView')->name('os_po_list_view');
});