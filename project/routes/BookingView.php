<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController'], function () {
	// Route::group(['middleware' => 'routeAccess'], function () {
		Route::post('booking/view', 'BookingView\BookingListView')->name('ipo_mrf_define');
		Route::any('booking_list_view/{id?}', 'BookingView\AcceptedBooking')->name('accepted_booking');

	// });
});

