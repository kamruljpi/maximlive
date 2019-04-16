<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Source\User\RoleDefine;
use Illuminate\Http\Request;
use App\Notification;
use Auth;

class NotificationController extends Controller
{
    public function postNotification( $type, $type_id ){
    	$notification = Notification::create([
    			'type' 		=> $type,
    			'type_id' 	=> $type_id
    		]);
    	return $notification;
    }

    public function getNotification($status, $limit = null ){

        $get_notification = Notification::orderBy('created_at','DESC');

        if($status == 1){

        } else {

        }

    	if($status == 1){

			if($limit != null) {
	    		$getBookingNotification = Notification::where(
					'type', Notification::CREATE_BOOKING	
	    			)->orderBy('created_at','DESC')->take($limit)->get();
			}else{
				$getBookingNotification = Notification::where(
					'type', Notification::CREATE_BOOKING	
	    			)->orderBy('created_at','DESC')->get();
			}

			if($limit != null) {
				$getMrfNotification = Notification::where(
					'type',Notification::CREATE_MRF	
					)->orderBy('created_at','DESC')->take($limit)->get();
			}else{
				$getMrfNotification = Notification::where(
					'type',Notification::CREATE_MRF	
					)->orderBy('created_at','DESC')->get();
			}

            if($limit != null) {
                $getOsNotification = Notification::where(
                    'type', Notification::CREATE_SPO
                    )->orderBy('created_at','DESC')->take($limit)->get();
            }else{
                $getOsNotification = Notification::where(
                'type', Notification::CREATE_SPO
                )->orderBy('created_at','DESC')->get(); 
            }

    		if($limit != null) {
	    		$getMrfGoodsReceiveNotification = Notification::where(
					'type', Notification::GOODS_RECEIVE
	    			)->orderBy('created_at','DESC')->take($limit)->get();
			}else{
				$getMrfGoodsReceiveNotification = Notification::where(
				'type', Notification::GOODS_RECEIVE
    			)->orderBy('created_at','DESC')->get();	
			}

    	}else{
    		
			if($limit != null) {
				$getBookingNotification = Notification::where(
    				[
    					'type' => Notification::CREATE_BOOKING,
    					'seen' => $status
    				]	
				)->orderBy('created_at','DESC')->take($limit)->get();
			}else {
				$getBookingNotification = Notification::where(
					[
						'type' => Notification::CREATE_BOOKING,
						'seen' => $status
					]	
				)->orderBy('created_at','DESC')->get();
			}

			if($limit != null) {
				$getMrfNotification = Notification::where(
    				[
    					'type' => Notification::CREATE_MRF,
    					'seen' => $status
    				]	
    			)->orderBy('created_at','DESC')->take($limit)->get();
			}else {
				$getMrfNotification = Notification::where(
    				[
    					'type' => Notification::CREATE_MRF,
    					'seen' => $status
    				]	
    			)->orderBy('created_at','DESC')->get();
			}

            if($limit != null) {
                $getOsNotification = Notification::where(
                    [
                        'type' => Notification::CREATE_SPO,
                        'seen' => $status
                    ]   
                )->orderBy('created_at','DESC')->take($limit)->get();
            }else {
                $getOsNotification = Notification::where(
                    [
                        'type' => Notification::CREATE_SPO,
                        'seen' => $status
                    ]   
                )->orderBy('created_at','DESC')->get();
            }

			if($limit != null) {
				$getMrfGoodsReceiveNotification = Notification::where(
    				[
    					'type' => Notification::GOODS_RECEIVE,
    					'seen' => $status
    				]	
    			)->orderBy('created_at','DESC')->take($limit)->get();
			}else {
				$getMrfGoodsReceiveNotification = Notification::where(
    				[
    					'type' => Notification::GOODS_RECEIVE,
    					'seen' => $status
    				]	
    			)->orderBy('created_at','DESC')->get();
			}
    	}

    	return [
    		'bookingNotification' => $getBookingNotification,
    		'mrfNotification' => $getMrfNotification,
            'getOsNotification' => $getOsNotification,
    		'getMrfGoodsReceiveNotification' => $getMrfGoodsReceiveNotification,
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

            $notification['booking'] = [];

            if($status == 1){
                if($limit != null) {
                    $notification['booking'] = Notification::where(function($query){
                            $query->where('type', Notification::CREATE_BOOKING)
                                ->orWhere('type', Notification::CREATE_MRF)
                                ->orWhere('type', Notification::CREATE_SPO)
                                ->orWhere('type', Notification::GOODS_RECEIVE);
                        })
                        ->orderBy('created_at','DESC')
                        ->take($limit)
                        ->get();
                }else{
                    $notification['booking'] = Notification::where(function($query){
                            $query->where('type', Notification::CREATE_BOOKING)
                                ->orWhere('type', Notification::CREATE_MRF)
                                ->orWhere('type', Notification::CREATE_SPO)
                                ->orWhere('type', Notification::GOODS_RECEIVE);
                        })
                        ->orderBy('created_at','DESC')
                        ->get();
                }
            } else {
                if($limit != null) {
                    $notification['booking'] = Notification::where(function($query){
                            $query->where('type', Notification::CREATE_BOOKING)
                                ->orWhere('type', Notification::CREATE_MRF)
                                ->orWhere('type', Notification::CREATE_SPO)
                                ->orWhere('type', Notification::GOODS_RECEIVE);
                        })
                        ->orderBy('created_at','DESC')
                        ->where('seen', $status)
                        ->take($limit)
                        ->get();
                }else{
                    $notification['booking'] = Notification::where(function($query){
                            $query->where('type', Notification::CREATE_BOOKING)
                                ->orWhere('type', Notification::CREATE_MRF)
                                ->orWhere('type', Notification::CREATE_SPO)
                                ->orWhere('type', Notification::GOODS_RECEIVE);
                        })
                        ->orderBy('created_at','DESC')
                        ->where('seen', $status)
                        ->get();
                }
            }

    		// $notification['booking'] = $notifications['bookingNotification'];
    		// $notification['mrf'] = $notifications['mrfNotification'];
    		// $notification['os'] = $notifications['getOsNotification'];
      //       $notification['mrf_goods_receive'] = $notifications['getMrfGoodsReceiveNotification'];

    	}elseif(($cs == 'customer') && ($cs != '') ){

            $notification['mrf'] = [];

            if($status == 1){
                if($limit != null) {
                    $notification['mrf'] = Notification::where(function($query){
                            $query->Where('type', Notification::CREATE_MRF)
                                ->orWhere('type', Notification::CREATE_SPO)
                                ->orWhere('type', Notification::GOODS_RECEIVE);
                        })
                        ->orderBy('created_at','DESC')
                        ->take($limit)
                        ->get();
                }else{
                    $notification['mrf'] = Notification::where(function($query){
                            $query->Where('type', Notification::CREATE_MRF)
                                ->orWhere('type', Notification::CREATE_SPO)
                                ->orWhere('type', Notification::GOODS_RECEIVE);
                        })
                        ->orderBy('created_at','DESC')
                        ->get();
                }
            } else {
                if($limit != null) {
                    $notification['mrf'] = Notification::where(function($query){
                            $query->Where('type', Notification::CREATE_MRF)
                                ->orWhere('type', Notification::CREATE_SPO)
                                ->orWhere('type', Notification::GOODS_RECEIVE);
                        })
                        ->orderBy('created_at','DESC')
                        ->where('seen', $status)
                        ->take($limit)
                        ->get();
                }else{
                    $notification['mrf'] = Notification::where(function($query){
                            $query->Where('type', Notification::CREATE_MRF)
                                ->orWhere('type', Notification::CREATE_SPO)
                                ->orWhere('type', Notification::GOODS_RECEIVE);
                        })
                        ->orderBy('created_at','DESC')
                        ->where('seen', $status)
                        ->get();
                }
            }

    		// $notification['mrf'] = $notifications['mrfNotification'];
      //       $notification['os'] = $notifications['getOsNotification'];
    		// $notification['mrf_goods_receive'] = $notifications['getMrfGoodsReceiveNotification'];

    	}elseif(($pl == 'planning') && ($pl != '')) {

            $notification['booking'] = [];

            if($status == 1){
                if($limit != null) {
                    $notification['booking'] = Notification::where(function($query){
                            $query->Where('type', Notification::CREATE_BOOKING)
                                ->orWhere('type', Notification::CREATE_SPO);
                        })
                        ->orderBy('created_at','DESC')
                        ->take($limit)
                        ->get();
                }else{
                    $notification['booking'] = Notification::where(function($query){
                            $query->Where('type', Notification::CREATE_BOOKING)
                                ->orWhere('type', Notification::CREATE_SPO);
                        })
                        ->orderBy('created_at','DESC')
                        ->get();
                }
            } else {
                if($limit != null) {
                    $notification['booking'] = Notification::where(function($query){
                            $query->Where('type', Notification::CREATE_BOOKING)
                                ->orWhere('type', Notification::CREATE_SPO);
                        })
                        ->orderBy('created_at','DESC')
                        ->where('seen', $status)
                        ->take($limit)
                        ->get();
                }else{
                    $notification['booking'] = Notification::where(function($query){
                            $query->Where('type', Notification::CREATE_BOOKING)
                                ->orWhere('type', Notification::CREATE_SPO);
                        })
                        ->orderBy('created_at','DESC')
                        ->where('seen', $status)
                        ->get();
                }
            }

    		// $notification['booking'] = $notifications['bookingNotification'];
    		// $notification['os'] = $notifications['getOsNotification'];

    	}elseif(($os == 'os') && ($os != '')) {

            $notification['booking'] = [];

            if($status == 1){
                if($limit != null) {
                    $notification['booking'] = Notification::where(function($query){
                            $query->Where('type', Notification::CREATE_MRF)
                                ->orWhere('type', Notification::GOODS_RECEIVE);
                        })
                        ->orderBy('created_at','DESC')
                        ->take($limit)
                        ->get();
                }else{
                    $notification['booking'] = Notification::where(function($query){
                            $query->Where('type', Notification::CREATE_MRF)
                                ->orWhere('type', Notification::GOODS_RECEIVE);
                        })
                        ->orderBy('created_at','DESC')
                        ->get();
                }
            } else {
                if($limit != null) {
                    $notification['booking'] = Notification::where(function($query){
                            $query->Where('type', Notification::CREATE_MRF)
                                ->orWhere('type', Notification::GOODS_RECEIVE);
                        })
                        ->orderBy('created_at','DESC')
                        ->where('seen', $status)
                        ->take($limit)
                        ->get();
                }else{
                    $notification['booking'] = Notification::where(function($query){
                            $query->Where('type', Notification::CREATE_MRF)
                                ->orWhere('type', Notification::GOODS_RECEIVE);
                        })
                        ->orderBy('created_at','DESC')
                        ->where('seen', $status)
                        ->get();
                }
            }

    		// $notification['mrf'] = $notifications['mrfNotification'];
      //       $notification['mrf_goods_receive'] = $notifications['getMrfGoodsReceiveNotification'];
            
    	}else{
    		$notification = 'Super Admin' ;
    	}
    	return $notification;
    }

    public function getAllNotificationView(){
    	$not = self::getAllNotification($status=1);
    
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
