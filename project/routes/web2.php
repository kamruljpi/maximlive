<?php


Route::group(['middleware' => 'auth'], function(){
   Route::group(['middleware' => 'routeAccess'], function(){

       Route::get('supplier/list', 'Supplier\SupplierController@supplierList')->name('supplier_list_view');
       Route::get('supplier/add', 'Supplier\SupplierController@supplierAdd')->name('supplier_add_view');
       Route::post('supplier/add', 'Supplier\SupplierController@supplierAddAction')->name('supplier_add_action');
       Route::get('supplier/update/{supplier_id?}', 'Supplier\SupplierController@supplierUpdate')->name('supplier_update');
       Route::post('supplier/update/{supplier_id?}', 'Supplier\SupplierController@supplierUpdateAction')->name('supplier_update_action');
       Route::get('supplier/delete/{supplier_id?}', 'Supplier\SupplierController@supplierDeleteAction')->name('supplier_delete_action');

       Route::get('booking/list/download/file/{booking_buyer_id?}/', 'taskController\BookingListController@bookingFilesDownload')->name('booking_files_download');



        Route::get('/warehouse_in/list', 'WarehouseInTypeController@index')->name('warehouseintypelist');

        Route::get('/warehouse_in', 'WarehouseInTypeController@createview')->name('warehouseintype');

        Route::post('/warehouse_in/store', 'WarehouseInTypeController@store')->name('warehouseintypestore');

        Route::get('/warehouse_in/updateview/{id_warehouse_type?}', 'WarehouseInTypeController@updateView')->name('warehouseintypeupdateView');

        Route::post('/warehouse_in/updatestore', 'WarehouseInTypeController@updatestore')->name('warehouseintypeupdatestore');

   		Route::get('/warehouse_in/delete/{id_warehouse_type?}', 'WarehouseInTypeController@delete')->name('warehouseintypedelete');



        Route::get('/warehouse_out/list', 'WarehouseOutTypeController@index')->name('warehouseouttypelist');

        Route::get('/warehouse_out', 'WarehouseOutTypeController@createview')->name('warehouseouttype');

        Route::post('/warehouse_out/store', 'WarehouseOutTypeController@store')->name('warehouseouttypestore');

        Route::get('/warehouse_out/updateview/{id_warehouse_type?}', 'WarehouseOutTypeController@updateView')->name('warehouseouttypeupdateView');

        Route::post('/warehouse_out/updatestore', 'WarehouseOutTypeController@updatestore')->name('warehouseouttypeupdatestore');

        Route::get('/warehouse_out/delete/{id_warehouse_type?}', 'WarehouseOutTypeController@delete')->name('warehouseouttypedelete');

   });
});   

/** Opening Stock Routes **/

Route::group(['middleware' => 'auth'], function(){
   Route::group(['middleware' => 'routeAccess'], function(){
   		Route::get('opening/stock', 'OpeningStockController@index')->name('opening_stock_view');
   		Route::post('store/opening/stock', 'OpeningStockController@store')->name('store_opening_stock_action');
	});

   /** Ajax Request Route**/

   Route::get('get/item/waise/color/size', 'OpeningStockController@getColorSizeByitemCode')->name('get_item_waise_color_size');
});

/** End **/

?>