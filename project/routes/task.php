<?php

Route::group(['middleware' => 'auth','namespace' => 'taskController'], function () {
	Route::any('task/list', 'Task\TaskViewController@view')->name('task_dashboard_view');
});

