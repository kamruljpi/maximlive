<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController\pi'], function () {
    Route::group(['middleware' => 'routeAccess'], function () {
    Route::post('pi/generate/report',
        [
            'as'=>'pi_generate_action',
            'uses'=>'PiController@piGenerate'
        ]);

    Route::get('pi/list/views',
        [
            'as'=>'pi_list_view',
            'uses'=>'PiListController@getPiList'
        ]);
    Route::get('pi/list/report',
        [
            'as'=>'pi_list_report_view',
            'uses'=>'PiListController@getPiReport'
        ]);
    Route::get('pi/cancel/{p_id?}','PiController@piEdit')->name('pi_edit_action');
    
    Route::get('pi/reverse/{p_id?}','PiListController@piReverseView')->name('pi_reverse_view');

    Route::get('pi/edit/{job_id?}','PiReverseController@piEditView')->name('pi_reverse_edit_view');
    Route::post('pi/edit/action','PiReverseController@piEditAction')->name('pi_reverse_edit_action');
    Route::get('pi/{job_id?}/delete/{p_id?}','PiReverseController@piDeleteAction')->name('pi_delete_action');
    });
    
    Route::post('pi/list','PiListController@piSearch')->name('pi_list_search');
});