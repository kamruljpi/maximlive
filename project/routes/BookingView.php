<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController'], function () {
	// Route::group(['middleware' => 'routeAccess'], function () {
		Route::post('/booking_list_view', 'BookingView\BookingListView@IpoOrMrfDefine')->name('ipo_mrf_define');

		Route::any('/booking_list_view/{id?}', 'BookingView\AcceptedBooking')->name('accepted_booking');
	// });
});