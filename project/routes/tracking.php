<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController\BookingView\Report'], function () {
    Route::get('management/tracking/report', 'ManagementTrackingController@managementTrackingReport')->name('planning_tracking_list');
    Route::post('tracking/export','TrackingController@exportReport')->name('tracking_export');
});
