<?php

namespace App\Http\Controllers;

use App\Model\Location\MxpLocation;
use App\MxpZone;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;
use App\Http\Controllers\Message\StatusMessage;
use Illuminate\Http\Request;
use Carbon;

class ZoneController extends Controller
{
    public function zoneList(){

        $zonelist = DB::table('mxp_zone as mz')
                    ->join('mxp_location as mp','mz.location_id','mp.id_location')
                    ->select('mz.zone_id','mz.zone_name','mz.location_id', 'mp.id_location','mz.status','mp.location as location_name')
                    ->where('mz.is_deleted', 0)
                    ->get();

        return view('zone.zone_list',compact('zonelist'));
    }
    public function zoneAdd(){
        $location = MxpLocation::all()->where('status', 1);

        return view('zone.zone_add',compact('location'));
    }
    public function zoneStore(Request $request){

        $datas = $request->all();
        $validMessages = [
            'zone_name.required' => 'Zone Name field is required.',
            'location.required' => 'Location field is required.',
            'status.required' => 'Status field is required.',
        ];
        $validator = Validator::make($datas,
            [
                'zone_name' => 'required',
                'location' => 'required',
                'status' => 'required',
            ],
            $validMessages
        );
        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
        }

        $zone = new MxpZone();
        $zone->user_id = Auth::user()->user_id;
        $zone->zone_name = $request->zone_name;
        $zone->location_id = $request->location;
        $zone->last_action_at = Carbon\Carbon::now();
        $zone->status = $request->status;
        $zone->save();

        StatusMessage::create('zone', 'Zone Created Successfully');


        return redirect()->route('zone_list');
    }

    public function zoneView( $zone_id ){
        $zone_data = MxpZone::where(
                        [
                            ['zone_id', $zone_id],
                            ['status', 1]
                        ]
                    )
                ->get();
        $location = MxpLocation::all()->where('status', 1);

        return view('zone.zone_view', compact('zone_data','location'));
    }

    public function zoneUpdate(Request $request){
        $datas = $request->all();
        $validMessages = [
            'zone_name.required' => 'Zone Name field is required.',
            'location.required' => 'Location field is required.',
            'status.required' => 'Status field is required.',
        ];
        $validator = Validator::make($datas,
            [
                'zone_name' => 'required',
                'location' => 'required',
                'status' => 'required',
            ],
            $validMessages
        );

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
        }

        $zone = MxpZone::find($request->zone_id);

        if (isset($zone)){
            $zone->user_id = Auth::user()->user_id;
            $zone->zone_name = $request->zone_name;
            $zone->location_id = $request->location;
            $zone->last_action_at = Carbon\Carbon::now();
            $zone->status = $request->status;
            $zone->save();

            StatusMessage::create('zone', 'Zone Updated Successfully');
            return redirect()->route('zone_list');
        }else{
            StatusMessage::create('zone', 'Oops Something went wrong.');
            return redirect()->route('zone_list');
        }

    }

    public function zoneDelete( $zone_id ){

        if (isset($zone_id)){
            DB::table('mxp_zone')
                ->where('zone_id', $zone_id)
                ->update(
                    [
                        'status' => 0,
                        'last_action_at' => Carbon\Carbon::now(),
                        'is_deleted' => 1,
                        'deleted_user_id' => Auth::user()->user_id
                    ]
                );
        }
        StatusMessage::create('delete', 'Zone "'.$zone_id.'" deleted Successfully');

        return redirect()->route('zone_list');
    }

    public function getZoneByLocId( Request $request){
        if (isset($request->selected)){

            $zone = MxpZone::where([
               ['location_id', $request->selected],
               ['status', 1],
               ['is_deleted', 0],
            ])->get();
        }
        return json_encode($zone);
    }

}
