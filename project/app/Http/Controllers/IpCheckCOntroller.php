<?php

namespace App\Http\Controllers;

use App\MxpIp;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class IpCheckCOntroller extends Controller
{
    public function getIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); 

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }elseif($ip == '::1'){
                    	return '103.106.238.107'; //Test Ip 
                    }else{
                        return false;
                    }
                }
            }
        }
    }

    public function redirectIp(){
    	redirect()->route('restricted');
    }

    public function checkIp(){
    	$ip = self::getIp();
    	if ($ip == false) {
    		$location = self::redirectIp();
    	}else{
    		$location = Location::get($ip);
    		self::insertIp($ip, $location);
    	}
    	return $location->countryCode;
    }

    public function insertIp($ip, $location){
    	$ips = MxpIp::where('ip', $ip)->exists();
    	if ($ips != 1) {
    		if (isset($location) && !empty($location)) {

    			$ipadd = new MxpIp();

    			$ipadd->ip = $ip;
    			$ipadd->countryCode = $location->countryCode;
    			$ipadd->cityName = $location->cityName;
    			$ipadd->zipCode = $location->zipCode;
    			$ipadd->latitude = $location->latitude;
    			$ipadd->longitude = $location->longitude;

    			$ipadd->save();	
    		}	
    	}
    }

    public function checkPermission()
    {
	    $countryCode = self::checkIp();
	    if( $countryCode == 'BD' ){
	    	return redirect()->Route('restricted');
	    }
       	return false;
    }
    
    public function restrictedView(){
    	return view('maxim.helper.restricted');
    }
}
