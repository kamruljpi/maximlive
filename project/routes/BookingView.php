<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController'], function () {
	// Route::group(['middleware' => 'routeAccess'], function () {
		Route::any('booking/view/mrf/genarate', 'BookingView\BookingListView')->name('ipo_mrf_define');
		Route::any('booking_list_view/{id?}', 'BookingView\AcceptedBooking')->name('accepted_booking');
		Route::post('booking/details/update/view', 'BookingController@updateBookingView')->name('booking_details_update_view');
		Route::post('booking/details/update', 'BookingController@updateBooking')->name('booking_details_update_action');
		Route::get('booking/details/cancel/{id}', 'BookingController@cancelBooking')->name('booking_details_cancel_action');
		Route::get('management/tracking/report', 'ManagementTrackingController@managementTrackingReport')->name('management_booking_list');

		Route::any('booking/details/jobid/delete/{id?}', 'BookingController@bookingJobIdDelete')->name('booking_job_id_delete_action');
		Route::get('draft/booking','DraftBooking@index')->name('getBooking');
		Route::get('draft/booking/{id?}','DraftBooking@redirectDraftBooking')->name('getDraft');
		Route::get('draft/{booking_id?}/delete','DraftBooking@draftDeleteAction')->name('draft_delete_action');

		Route::post('make_challan','BookingListController@makeChallan')->name('make_challan_view');

	// });
});

