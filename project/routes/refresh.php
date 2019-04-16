<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController'], function () {

	Route::get('booking/store/report','BookingController@redirectBookingReport')->name('refresh_booking_view');
	Route::get('mrf/store/report', 'MrfController@redirectMrfReport')->name('refresh_mrf_view');
	Route::get('ipo/store/report', 'Ipo\IpoController@redirectIpoReport')->name('refresh_ipo_view');
	Route::get('pi/store/report', 'pi\PiController@redirectPiReport')->name('refresh_pi_view');
	Route::get('os/po/store/report', 'Os\Po\PoController@redirectOsPoReport')->name('refresh_os_po_view');

	Route::get('booking/draft/store/{id?}', 'DraftBooking@redirectDraftBooking')->name('refresh_booking_draft');
	Route::get('challan/report', 'ChallanController@redirectChallanReport')->name('refresh_challan_view');
});