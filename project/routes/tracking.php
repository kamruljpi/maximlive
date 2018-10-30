<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController\BookingView\Report'], function () {
    Route::get('management/tracking/report', 'PlanningTrackingController@trackingReportView')->name('planning_tracking_list');
    Route::post('tracking/export','TrackingExportToExcel@exportReport')->name('tracking_export');
});
