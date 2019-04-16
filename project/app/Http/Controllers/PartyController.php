<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MaxParty;
use Validator;
use App\Http\Controllers\Message\StatusMessage;
use DB;
use Auth;
use App\buyer;
use App\userbuyer;

class PartyController extends Controller
{
    public function index()
    {

        $userbuyer = userbuyer::where("id_user",Auth::user()->user_id)->get();
        $buyerList = [];
        if(isset($userbuyer) && !empty($userbuyer)){
            foreach ($userbuyer as $buyerusr) {
                $buyerList[] = $buyerusr->id_buyer;
            }
        }

        if(isset($buyerList) && !empty($buyerList)){
            // $party_list = MaxParty::where('user_id',Auth::user()->user_id)->paginate(10);

            $party_list = DB::table('mxp_party')->whereIn('id_buyer',$buyerList)->paginate(10);
        }else if(Auth::user()->type == 'super_admin'){
            $party_list = DB::table('mxp_party')->orderBy('id','DESC')->paginate(10);
        }else{
            $party_list = DB::table('mxp_party')->whereIn('id_buyer',$buyerList)->paginate(10);
            // $party_list = [];
        }

        return view('party_management.party_list', compact('party_list'));
    }

    public function create()
    {
        // $buyers = buyer::all();
        $buyers = DB::table('mxp_buyer')->select('id_mxp_buyer','buyer_name')->orderBy('buyer_name', ASC)->get();
        return view('party_management.party_create', compact('buyers'));
    } 

    public function updateView(Request $request)
    {
        $party_edits = MaxParty::Where('id',$request->id )->get();
        // $buyers = buyer::all();
        $buyers = DB::table('mxp_buyer')->select('id_mxp_buyer','buyer_name')->orderBy('buyer_name', ASC)->get();
        return view('party_management.party_edit', compact('party_edits','buyers'));
    }

    public function store(Request $request)
    {   
        /** make sort_name instead name**/
        $name = isset($request->name) ? $request->name : '' ;
        $names = explode(' ', $name);

        $sort_name_as = '';

        if(!empty($names)) {
            foreach ($names as $name_) {
                if ($name_[0] != '&') {
                    if($name_[0] != '(') {
                        $str = str_replace('/', '', $name_[0]);
                        $str_n = trim($str, '/');

                        $sort_name_as .= $str_n;                    
                    }
                }
            }
        }else{
            $sort_name_as = $name ;
        }
        
        /** End **/
        // $this->print_me(strtoupper($sort_name_as));

        $validMassage = [            
            'name.required' => 'Company Name is required',
            // 'sort_name.required' => 'Company sort name is required',
            'name_buyer.required' => 'Brand name is required',
            'name.unique' => 'This vendor ( '.$request->name.' ) name already inserts',
            
        ];

        $validator = Validator::make($request->all(), [
                'name'                   => 'required||unique:mxp_party,name',
                // 'sort_name'              =>'required',
                'name_buyer'             => 'required',
            ],
            $validMassage
        );

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
        }

        $party = new MaxParty();
        $party->party_id               = $request->party_id;
        $party->user_id                = Auth::user()->user_id;
        $party->name                   = $request->name;
        $party->sort_name              = (isset($request->sort_name) ? $request->sort_name : strtoupper($sort_name_as));
        $party->name_buyer             = $request->name_buyer;
        $party->address_part1_invoice  = $request->address_part_1_invoice;
        $party->address_part2_invoice  = $request->address_part_2_invoice;
        $party->attention_invoice      = $request->attention_invoice;
        $party->mobile_invoice         = $request->mobile_invoice;
        $party->telephone_invoice      = $request->telephone_invoice;
        $party->fax_invoice            = $request->fax_invoice;
        $party->address_part1_delivery = $request->address_part_1_delivery;
        $party->address_part2_delivery = $request->address_part_2_delivery;
        $party->attention_delivery     = $request->attention_delivery;
        $party->mobile_delivery        = $request->mobile_delivery;
        $party->telephone_delivery     = $request->telephone_delivery;
        $party->fax_delivery           = $request->fax_delivery;
        $party->description_1          = $request->description_1;
        $party->description_2          = $request->description_2;
        $party->description_3          = $request->description_3;
        $party->status                 = $request->status;
        $party->id_buyer               = $request->id_buyer;
        $party->save();

        StatusMessage::create('party_added', $request->name.' Party Added Successfully');

        return Redirect()->Route('party_list_view');
    }

    public function update(Request $request)
    {
        /** make sort_name instead name **/
        $name = isset($request->name) ? $request->name : '' ;
        $names = explode(' ', $name);

        $sort_name_as = '';

        if(!empty($names)) {
            foreach ($names as $name_) {
                if ($name_[0] != '&') {
                    if($name_[0] != '(') {
                        $str = str_replace('/', '', $name_[0]);
                        $str_n = trim($str, '/');

                        $sort_name_as .= $str_n;                    
                    }
                }
            }
        }else{
            $sort_name_as = $name ;
        }
        
        /** End **/
        
        $validMassage = [
            'name.required' => 'Company Name is required',
            'sort_name.required' => 'Company sort name is required',
            'id_buyer.required' => 'Buyer name is required',
        ];

        $validator = Validator::make($request->all(), [
                'name'                   => 'required',
                'sort_name'              => 'required',
                'id_buyer'             => 'required',
            ],
            $validMassage
        );

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
        }

        $name_buyer = (buyer::where('id_mxp_buyer',$request->id_buyer)->select('buyer_name')->first())->buyer_name;

        $update_party = MaxParty::find($request->id);
        $update_party->party_id               = $request->party_id;
        $update_party->user_id                = Auth::user()->user_id;
        $update_party->name                   = $request->name;
        $update_party->sort_name              = strtoupper($sort_name_as);
        $update_party->name_buyer             = $name_buyer;
        $update_party->address_part1_invoice  = $request->address_part_1_invoice;
        $update_party->address_part2_invoice  = $request->address_part_2_invoice;
        $update_party->attention_invoice      = $request->attention_invoice;
        $update_party->mobile_invoice         = $request->mobile_invoice;
        $update_party->telephone_invoice      = $request->telephone_invoice;
        $update_party->fax_invoice            = $request->fax_invoice;
        $update_party->address_part1_delivery = $request->address_part_1_delivery;
        $update_party->address_part2_delivery = $request->address_part_2_delivery;
        $update_party->attention_delivery     = $request->attention_delivery;
        $update_party->mobile_delivery        = $request->mobile_delivery;
        $update_party->telephone_delivery     = $request->telephone_delivery;
        $update_party->fax_delivery           = $request->fax_delivery;
        $update_party->description_1          = $request->description_1;
        $update_party->description_2          = $request->description_2;
        $update_party->description_3          = $request->description_3;
        $update_party->id_buyer               = $request->id_buyer;
        $update_party->status                 = $request->status;
        $update_party->save();

        StatusMessage::create('party_updated', $request->name .' '. $request->name_buyer .'(buyer) updated Successfully');
        return Redirect()->Route('party_list_view');
    }

    public function deleteParty(Request $request) {
      $party = MaxParty::find($request->id);
      $party->delete();
      StatusMessage::create('party_delete',$party->name .' is deleted Successfully');
      return redirect()->Route('party_list_view');
    }
}
