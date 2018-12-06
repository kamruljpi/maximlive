<?php

namespace App\Http\Controllers;

use App\Notification;
use Auth;
use App\Http\Controllers\Source\User\RoleDefine;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function postNotification( $type, $type_id ){
    	$notification = Notification::create([
    			'type' 		=> $type,
    			'type_id' 	=> $type_id
    		]);
    	return $notification;
    }

    public function getNotification( $status ){
    	if($status == 1){
    		$getBookingNotification = Notification::where(
				'type', Notification::CREATE_BOOKING	
    			)->get();

    		$getMrfNotification = Notification::where(
    			'type',Notification::CREATE_MRF	
    			)->get();

    		$getOsNotification = Notification::where(
				'type', Notification::CREATE_SPO
    			)->get();
    	}else{
    		$getBookingNotification = Notification::where(
    				[
    					'type' => Notification::CREATE_BOOKING,
    					'seen' => $status
    				]	
    			)->get();

    		$getMrfNotification = Notification::where(
    				[
    					'type' => Notification::CREATE_MRF,
    					'seen' => $status
    				]	
    			)->get();

    		$getOsNotification = Notification::where(
    				[
    					'type' => Notification::CREATE_SPO,
    					'seen' => $status
    				]	
    			)->get();
    	}

    	return [
    		'bookingNotification' => $getBookingNotification,
    		'mrfNotification' => $getMrfNotification,
    		'getOsNotification' => $getOsNotification
    	]; 
    }

    public function updateSeenStatus( $type_id, $user_id ){
    	
    	Notification::where( 'type_id',  $type_id)
    		->update(
    			[
    				'seen' => 1,
    				'seen_user_id' => $user_id
    			]
    		);
    }

    public function getAllNotification($status){
    	$notifications = self::getNotification($status);
    	$cs = RoleDefine::getRole('Customer');
    	$pl = RoleDefine::getRole('Planning');
    	$os = RoleDefine::getRole('OS');

    	$notification = [];	
    	if(($cs == 'cs') && ($cs != '') ){
    		$notification['mrf'] = $notifications['mrfNotification'];
    		$notification['os'] = $notifications['getOsNotification'];
    	}elseif (($pl == 'planning') && ($pl != '') ) {
    		$notification['booking'] = $notifications['bookingNotification'];
    		$notification['os'] = $notifications['getOsNotification'];
    	}elseif (($os == 'os') && ($os != '') ) {
    		$notification['mrf'] = $notifications['mrfNotification'];
    	}else{
    		$notification = 'Super Admin' ;
    	}
    	return $notification;
    }

    public function getAllNotificationView(){
    	$not = self::getAllNotification( $status=1);
    
    	return view('notification.notification_view',compact('not'));
    }

    public function setAllNotificationSeen(){
    	$not = self::getAllNotification( $status=1);

    	foreach ($not as $key => $value) {
    		foreach ($value as $key => $noti) {
    			self::updateSeenStatus($noti->type_id, Auth::user()->user_id );
    		}	
    	}

    	return redirect()->back();
    }
}
