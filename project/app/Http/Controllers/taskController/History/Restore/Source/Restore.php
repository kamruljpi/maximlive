<?php

namespace App\Http\Controllers\taskController\History\Restore\Source;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\History\Restore\Source\Resource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\MxpBooking;
use App\Model\MxpPi;
use Session;
use Carbon;
use Auth;
use DB;

Class Restore extends Controller
{
	public function __invoke($id){
		return Resource::piRestoreRequest($id);
	}
}