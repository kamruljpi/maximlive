<?php

Route::group(['middleware' => 'auth','namespace' => 'Search'], function () {
    Route::get('product/lists','ItemSearchController@itemSearch')->name('item_search_action');
    Route::any('vendor/search','vendorSearch')->name('vendor_simple_searchs');
});
