<?php

Route::group(['middleware' => 'auth','namespace' => 'Search'], function () {
    Route::get('product/lists','ItemSearchController@itemSearch')->name('item_search_action');

    // use __invoke

    Route::any('vendor/search','vendorSearch')->name('vendor_simple_searchs'); 
    Route::any('description/search','ItemDescription')->name('description_simple_searchs'); 
    Route::any('buyer/search','BuyerSearch')->name('buyer_simple_searchs');
    Route::any('item/color/search','ItemColor')->name('item_color_simple_searchs');
    Route::any('item/size/search','ItemSize')->name('item_size_simple_searchs');
    Route::any('supplier/search','SupplierSearch')->name('supplier_simple_searchs');
});
