<?php 
namespace App\Http\Controllers\Source;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;
class OrderNumber extends AnotherClass
{
	
	public function getNextOrderNumber($model_object,$id,$extention)
	{
	    // Get the last created order
	    $lastOrder = $model_object->orderBy('created_at', 'desc')->first();

	    if ( ! $lastOrder ) {
	        $number = 0;
	    }else {
	        $number = substr($lastOrder->{$id}, 3);
	    }

	    $date = date('dmY');

	    return $extention.'-'.$date. '-' . sprintf('%06d', intval($number) + 1);
	}
}