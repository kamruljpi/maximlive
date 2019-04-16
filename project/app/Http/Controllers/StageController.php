<?php

namespace App\Http\Controllers;

use App\Http\Controllers\taskController\Flugs\booking\BookingFulgs;
use App\Http\Controllers\taskController\Flugs\LastActionFlugs;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Model\MxpStage;
use Validator;
use Session;
use Auth;

class StageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $details = MxpStage::where('is_deleted',BookingFulgs::IS_NOT_DELETED)->paginate(20);
        return view('stage.index',compact('details'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('stage.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $datas = $request->all();

        $validMessages = [
            'name.required' => 'The Name field is required.',
            'name.unique' => 'The Name field has a duplicate value.',
            ];


        $validator = Validator::make($datas, 
            [
                'name' => [
                            'required',
                            Rule::unique('mxp_stage')->ignore(1, 'is_deleted')
                          ]
            ],
            $validMessages
        );

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
        }

        $create = new MxpStage();
        $create->name = $request->name;
        $create->user_id = Auth::User()->user_id;
        $create->last_action_at = LastActionFlugs::CREATE_ACTION;
        $create->save();
        
        Session::flash('create', 'New Stage Created Successfully');

        return \Redirect()->Route('stage_list_view');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $details = MxpStage::where([
                        ['is_deleted',BookingFulgs::IS_NOT_DELETED],
                        ['id_stage',$id],
                    ])
                    ->first();

        return view('stage.edit',compact('details'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $this->print_me($request->all());
        $datas = $request->all();

        $validMessages = [
            'name.required' => 'The Name field is required.',
            'name.unique' => 'The Name field has a duplicate value.',
            ];


        $validator = Validator::make($datas, 
            [
                'name' => 'required|unique:mxp_stage,name,'.$id.',id_stage',
            ],
            $validMessages
        );

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator->messages());
        }

        $update = MxpStage::find($id);
        $update->name = $request->name;
        $update->user_id = Auth::user()->user_id;
        $update->is_active = $request->is_active;
        $update->last_action_at = LastActionFlugs::UPDATE_ACTION;
        $update->save();
        
        Session::flash('update', 'The Stage Updated Successfully');

        return \Redirect()->Route('stage_list_view');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = MxpStage::find($id);
        $delete->is_deleted = 1;
        $delete->last_action_at = LastActionFlugs::DELETE_ACTION;
        $delete->save();

        Session::flash('delete', 'The Stage Successfully Deleted');

        return \Redirect()->Route('stage_list_view');
    }
}
