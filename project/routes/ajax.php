<?php

Route::group(['middleware' => 'auth','namespace' => 'AjaxRequest\Task'], function () {

	Route::get('/get/item/check/user/access', 'Booking\Booking@checkItem')->name('get_item_check_user/access');
});