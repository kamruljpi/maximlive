<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController'], function () {

	Route::get('booking/store/report', 'BookingController@redirectBookingReport')->name('refresh_booking_view');
	Route::get('mrf/store/report', 'MrfController@redirectMrfReport')->name('refresh_mrf_view');
	Route::get('ipo/store/report', 'Ipo\IpoController@redirectIpoReport')->name('refresh_ipo_view');
});