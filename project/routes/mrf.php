<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController\BookingView\Mrf'], function () {
    Route::group(['middleware' => 'routeAccess'], function () {

    Route::get('booking/mrf/cancel/{id}', 'MrfController@cancelMrfById')->name('mrf_details_cancel_action');
	Route::get('planning/booking/cancel/{b_id?}', 'MrfController@cancelBookingByPlanning')->name('planning_cancel_booking_action');
	});
});
