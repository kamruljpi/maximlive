<?php
namespace App\Http\Controllers\taskController\Os;

use Illuminate\Http\Request;
use App\Http\Controllers\taskController\Flugs\HeaderType;
use App\Http\Controllers\Controller;
use App\Model\MxpMrf;
use App\MxpIpo;
use Auth;
use DB;

class PoListController extends controller 
{
	public function opListView(){
		$bookingList = MxpIpo::select('*',DB::Raw('sum(ipo_quantity) as ipo_quantity'))
            ->orderBy('ipo_id','DESC')
            ->groupBy('ipo_id')
			->paginate(20);
		return view('maxim.os.po.list.po_list',compact('bookingList'));
	}
}