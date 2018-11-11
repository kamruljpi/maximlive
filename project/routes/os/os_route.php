<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController\Os'], function () {
	Route::get('os/mrf/list', 'Mrf\MrfListController@mrfListView')->name('os_mrf_list_view');
	Route::get('os/mrf/report', 'Mrf\MrfListController@showMrfReport')->name('os_mrf_list_report_view');
	Route::get('os/mrf/report/{mid?}', 'Mrf\MrfListController@detailsViewForm')->name('os_mrf_details_view');
	Route::post('os/po/genarate', 'Po\PoController@poGenarateView')->name('os_po_genarate_view');
	Route::post('os/po/genarate/action', 'Po\PoController@storeOsPo')->name('os_po_genarate_report_action');
	Route::get('os/po/list', 'Po\PoListController@opListView')->name('os_po_list_view');
	Route::get('os/accepted/mrf/{mid?}', 'Accept\AcceptMrf')->name('os_accepted_mrf_action');
	Route::get('os/cencel/mrf/{mid?}', 'Cancel\CancelMrf')->name('os_cancel_mrf_action');
	Route::get('os/accept/jobid/{job_id?}', 'Accept\AcceptJobidByMrf')->name('os_mrf_jobid_accept');
	Route::get('os/cencel/jobid/{job_id?}', 'Cancel\CancelJobidByMrf')->name('os_mrf_jobid_cancel');
    Route::get('os/tracking/report', 'OsTrackingController@trackingReportView')->name('os_tracking_list');
    Route::post('os/export','OsTrackingController@exportReport')->name('os_export');
    Route::get('/os_tracking_report/','Po\PoController@getOsMrfValues')->name('get_ospo_by_id');
});