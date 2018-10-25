<?php

Route::group(['middleware' => 'auth','namespace' => 'AjaxRequest\Task'], function () {

	Route::get('/get/item/check/user/access', 'Booking\Booking@checkItem')->name('get_item_check_user/access');
});

Route::group(['middleware' => 'auth','namespace' => 'AjaxRequest\Item'], function () {
    Route::get('get/buyer/wise/size', 'Size')->name('get_buyer_wise_size');
    Route::get('get/buyer/wise/color', 'Color')->name('get_buyer_wise_color');
    Route::get('get/buyer/wise/vendor/list', 'Vendor')->name('get_buyer_wise_vendor_list');
});