<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController\BookingView\Mrf'], function () {
    Route::get('booking/mrf/cancel/{id}', 'MrfController@cancelMrf')->name('mrf_details_cancel_action');
});
