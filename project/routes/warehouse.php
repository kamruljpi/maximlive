<?php

Route::group(['middleware' => 'auth','namespace' => 'Purchase'], function () {

	/*************************************************************
	 * Purchase Order Route
	 *************************************************************/

	Route::get('purchase_order', 'PurchaseOrder@index')->name('purchase_order_view');
	Route::get('create/purchase_order', 'PurchaseOrder@create')
	                                  ->name('purchase_order_create_view');
	Route::post('store/purchase_order', 'PurchaseOrder@store')
	                                  ->name('purchase_order_store_action');


	/*******************************************************************
	 * Purchase List Route
	 *******************************************************************/

	Route::get('purchase_list', 'Purchase@index')->name('purchase_list_view');
	Route::get('create_purchase', 'Purchase@create')->name('purchase_create_view');
	Route::post('store_purchase', 'Purchase@store')->name('purchase_store_action');
	Route::get('show_purchase/{id?}', 'Purchase@show')->name('purchase_show_view');

	Route::any('store/show_purchase', 'Purchase@showStore')->name('purchase_show_store_action');

});

Route::group(['middleware' => 'auth'], function () {

	/*****************************************************************
     * Raw Item Route
     *****************************************************************/

      Route::get('raw_item','RawitemController@index')->name('raw_item_view');
      Route::get('create/raw_item','RawitemController@create')->name('raw_item_create_view');
      Route::post('store/raw_item','RawitemController@store')->name('raw_item_create_action');
      Route::get('edit/raw_item/{id?}','RawitemController@edit')->name('raw_item_edit_view');
      Route::post('edit/raw_item/{id?}','RawitemController@update')->name('raw_item_edit_action');
      Route::any('delete/raw_item/{id?}','RawitemController@destroy')->name('raw_item_delete_action');

      // ajax request

      Route::any('/get/raw_item_code','RawitemController@getRawItemCode');
      Route::any('/get/raw_item/details_by_code','RawitemController@getRawItemByItemCode');

});

Route::group(['middleware' => 'auth','namespace' => 'Production'], function () {


	/*****************************************************************
     * Issue Slips Route
     *****************************************************************/

	Route::get('issue_slips','IssueSlips@index')->name('issue_slips_from_warehouse_view');
	Route::get('create_slips', 'IssueSlips@create')->name('issue_slips_create_view');
	Route::post('store_slips', 'IssueSlips@store')->name('issue_slips_store_action');


	/*****************************************************************
     * Received For Production Route
     *****************************************************************/

	Route::get('received','ReceivedForProduction@index')->name('received_for_production_view');
	// Route::get('create_received', 'ReceivedForProduction@create')
								// ->name('received_for_production_create_view');
	// Route::post('store_slips', 'ReceivedForProduction@store')->name('issue_slips_store_action');


	/*****************************************************************
     * Final Production Route
     *****************************************************************/

	Route::get('final_production','FinalProduction@index')->name('final_production_view');
	Route::get('create/final_product', 'FinalProduction@create')->name('final_product_create_view');
	Route::post('store/final_product', 'FinalProduction@store')->name('final_product_store_action');
	Route::get('view/final_product/{product_id}', 'FinalProduction@show')->name('final_product_view');
	Route::any('update/final/product', 'FinalProduction@edit')->name('warehouse_final_product_update');
	Route::get('delete/final_product/{product_id}', 'FinalProduction@destroy')->name('final_product_delete_action');

	// Ajax
	Route::any('get/product_details', 'FinalProduction@getProductDetails');
});