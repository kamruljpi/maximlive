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

    public function getNotification( $status, $limit = null ){
    	if($status == 1){
			if($limit != null) {
	    		$getBookingNotification = Notification::where(
					'type', Notification::CREATE_BOOKING	
	    			)->orderBy('id','DESC')->take($limit)->get();
			}else{
				$getBookingNotification = Notification::where(
					'type', Notification::CREATE_BOOKING	
	    			)->orderBy('id','DESC')->get();
			}
			if($limit != null) {
				$getMrfNotification = Notification::where(
					'type',Notification::CREATE_MRF	
					)->orderBy('id','DESC')->take($limit)->get();
			}else{
				$getMrfNotification = Notification::where(
					'type',Notification::CREATE_MRF	
					)->orderBy('id','DESC')->get();
			}

    		if($limit != null) {
	    		$getOsNotification = Notification::where(
					'type', Notification::CREATE_SPO
	    			)->orderBy('id','DESC')->take($limit)->get();
			}else{
				$getOsNotification = Notification::where(
				'type', Notification::CREATE_SPO
    			)->orderBy('id','DESC')->get();	
			}

    	}else{
    		
			if($limit != null) {
				$getBookingNotification = Notification::where(
    				[
    					'type' => Notification::CREATE_BOOKING,
    					'seen' => $status
    				]	
				)->orderBy('id','DESC')->take($limit)->get();
			}else {
				$getBookingNotification = Notification::where(
					[
						'type' => Notification::CREATE_BOOKING,
						'seen' => $status
					]	
				)->orderBy('id','DESC')->get();
			}

			if($limit != null) {
				$getMrfNotification = Notification::where(
    				[
    					'type' => Notification::CREATE_MRF,
    					'seen' => $status
    				]	
    			)->orderBy('id','DESC')->take($limit)->get();
			}else {
				$getMrfNotification = Notification::where(
    				[
    					'type' => Notification::CREATE_MRF,
    					'seen' => $status
    				]	
    			)->orderBy('id','DESC')->get();
			}

			if($limit != null) {
				$getOsNotification = Notification::where(
    				[
    					'type' => Notification::CREATE_SPO,
    					'seen' => $status
    				]	
    			)->orderBy('id','DESC')->take($limit)->get();
			}else {
				$getOsNotification = Notification::where(
    				[
    					'type' => Notification::CREATE_SPO,
    					'seen' => $status
    				]	
    			)->orderBy('id','DESC')->get();
			}
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

    public function getAllNotification($status, $limit=null){
    	if ($limit != null) {
    		$notifications = self::getNotification($status, $limit);
    	}else{
    		$notifications = self::getNotification($status, $limit=null);
    	}
    	$cs = RoleDefine::getRole('Customer');
    	$pl = RoleDefine::getRole('Planning');
    	$os = RoleDefine::getRole('OS');
    	$super_admin = Auth::user()->type;

    	$notification = [];
    	if($super_admin == 'super_admin'){
    		$notification['booking'] = $notifications['bookingNotification'];
    		$notification['mrf'] = $notifications['mrfNotification'];
    		$notification['os'] = $notifications['getOsNotification'];
    	}	
    	elseif(($cs == 'customer') && ($cs != '') ){
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
