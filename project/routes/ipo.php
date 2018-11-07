<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController\Ipo'], function () {
    Route::get('booking/ipo/cancel/{id}', 'IpoController@cancelIpo')->name('ipo_details_cancel_action');
});
