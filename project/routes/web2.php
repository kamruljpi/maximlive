<?php


Route::group(['middleware' => 'auth'], function(){
   Route::group(['middleware' => 'routeAccess'], function(){

       Route::get('supplier_list', 'Supplier\SupplierController@supplierList')->name('supplier_list_view');
       Route::get('supplier/add', 'Supplier\SupplierController@supplierAdd')->name('supplier_add_view');
       Route::post('supplier_create', 'Supplier\SupplierController@supplierAddAction')->name('supplier_add_action');
       Route::get('supplier_edit/{supplier_id?}', 'Supplier\SupplierController@supplierUpdate')->name('supplier_update');
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

   		Route::get('store/opening/product', 'OpeningProductController@index')->name('store_opening_product_view');
      Route::post('store/opening/product', 'OpeningProductController@productStore')->name('store_product_entry_action');

      Route::get('stored/product', 'OpeningProductController@storedProduct')->name('stored_product');
      Route::any('filter/stored/product', 'OpenningProductSearch@filterStoredProduct')->name('product_stored_filter_action');

      Route::get('stored/item', 'OpeningProductController@storedItem')->name('stored_item');    
      Route::any('filter/stored/item', 'OpeningProductController@filterStoredItem')->name('item_stored_filter_action');
     
      Route::get('/stored/product/list', 'OpeningProductController@groubByProductList')->name('stored_product_list');
      Route::any('filter/stored/product/list', 'OpenningProductSearch@filterStoredProductList')->name('product_list_stored_filter_action');

      Route::get('/stored/product/{item_code?}/{item_size?}/{item_color?}', 'OpeningProductController@getSingleProduct')->name('get_single_product');
	});

   /** Ajax Request Route**/

   Route::get('get/item/waise/color/size', 'OpeningStockController@getColorSizeByitemCode')->name('get_item_waise_color_size');
   Route::get('get/product', 'OpeningProductController@getProduct')->name('get_product_action');
});

Route::group(['middleware' => 'auth'], function(){
    Route::get('zone/list', 'ZoneController@zoneList')->name('zone_list');
    Route::get('zone/add', 'ZoneController@zoneAdd')->name('zone_add_view');
    Route::post('zone/add', 'ZoneController@zoneStore')->name('zone_store_action');
    Route::get('zone/edit/{zone_id}', 'ZoneController@zoneView')->name('zone_view');
    Route::post('zone/update', 'ZoneController@zoneUpdate')->name('zone_update_action');
    Route::get('zone/delete/{zone_id}', 'ZoneController@zoneDelete')->name('zone_delete');
    Route::get('zone/details', 'ZoneController@getZoneByLocId')->name('zone_details_by_id');
});

/** End **/

?>