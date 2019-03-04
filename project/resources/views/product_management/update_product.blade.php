@extends('layouts.dashboard')
@section('page_heading',trans('others.update_product_label'))
@section('section')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"rel="stylesheet">
    @extends('product_management.product_modal')

    <?php 
        use App\Http\Controllers\Source\User\RoleDefine;
        $object = new RoleDefine();
        $os_define_role = $object->getRole('OS');
    ?>
    <style type="text/css">
        .price_icon{
            float: left;
            padding-top: 4px;
        }
        .float_left{
            float:left;
        }

        .float_left_padding{
            float: left;
            padding-left:5px;
        }
    </style> 

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-2">
                <div class="form-group ">
                    <a href="{{ URL::previous() }}" class="btn btn-primary " style="width: 100%; margin: 10px 0px 5px 0px;">
                    <i class="fa fa-arrow-left"></i> Back</a>
                </div>
            </div>
        </div>
        <div class="row">
             <div class="col-md-12 ">   <!--col-md-offset-2 -->
                @if(count($errors) > 0)
                    <div class="alert alert-danger" role="alert">
                        @foreach($errors->all() as $error)
                          <li><span>{{ $error }}</span></li>
                        @endforeach
                    </div>
                @endif

                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('others.update_product_label') }}</div>
                    <div class="panel-body">
                        @foreach($product as $data)            
                            <form class="form-horizontal" action="{{ Route('update_product_action') }}/{{$data->product_id}}" method="POST" autocomplete="off">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="redirects_to" value="{{ URL::previous() }}">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6">
                                        {{--<div class="form-group">--}}
                                            {{--<label class="col-md-4 control-label">{{ trans('others.product_brand_label') }}</label>--}}
                                            {{--<div class="col-md-6">--}}
                                                {{--<select class="form-control " name="p_brand" required value="">--}}
                                                    {{--<option value="{{$data->brand}}">{{$data->brand}}</option>--}}
                                                    {{--@foreach($brands as $brand)--}}
                                                        {{--<option value="{{$brand->brand_name}}">{{$brand->brand_name}}</option>--}}
                                                    {{--@endforeach--}}
                                                {{--</select>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Brand</label>
                                            <div class="col-md-6">
                                                <select class ="form-control" name="id_buyer" id="id_buyer" style="{{($os_define_role == 'os')?'pointer-events: none; background-color: #ddd;':''}}">
                                                    <option value="">Choose a option</option>
                                                    @foreach($buyers as $buyer)
                                                        <option 
                                                            @if($buyer->id_mxp_buyer == $data->id_buyer)
                                                                selected="selected"
                                                            @endif data-id="{{ $buyer->id_mxp_buyer }}" 
                                                            value="{{ $buyer->id_mxp_buyer }}">{{ $buyer->buyer_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">{{ trans('others.product_code_label') }}</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control  input_required" name="p_code" value="{{$data->product_code}}" {{($os_define_role == 'os')?'readonly':''}}>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">{{ trans('others.product_description_label') }}</label>
                                            <div class="col-md-6">
                                                <select class="form-control " name="p_description" required style="{{($os_define_role == 'os')?'pointer-events: none; background-color: #ddd;':''}}">
                                                    <option value="">Choose a option</option>
                                                    @foreach($itemList as $item)
                                                        <option value="{{$item->id}}" {{ ( $item->id == $data->item_description_id) ? 'selected' : '' }}>{{$item->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>                                     

                                            {{--Add Color MultiSelect Box--}}
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Color</label>
                                            <div class="col-md-6">
                                                <div class="product-brand-list" style="width:80%; float: left; {{($os_define_role == 'os')?' pointer-events: none;':''}}">
                                                    <select class="select-color-list" name="colors[]" multiple="multiple" {{($os_define_role == 'os')?'readonly':''}}>
                                                        <option value="">Choose Color</option>
                                                        @foreach($colors as $color)
                                                            <option <?php if(in_array($color['id'],$SelectedColors)){ ?> selected="selected" 
                                                        <?php } ?> value="{{$color['id']}},{{$color['color_name']}}" >{{$color['color_name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                @if($os_define_role != 'os')
                                                <div class="add-color-btn" style="width:20%; float: left; padding-top: 5px;">
                                                    <a class="hand-cursor"  data-toggle="modal" data-target="#addColorModal">
                                                        <i class="material-icons">
                                                            add_circle_outline
                                                        </i>
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        {{--End Add Color MultiSelect Box--}}

                                        {{--Add Size MultiSelect Box--}}
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Size Range</label>
                                            <div class="col-md-6">
                                                <div class="product-size-list" style="width:80%; float: left;{{($os_define_role == 'os')?' pointer-events: none;':''}}">
                                                      <select class="select-size-list" name="sizes[]" multiple="multiple">
                                                        <option value="">Choose Size Range</option>
                                                        @foreach($sizes as $size)
                                                            <option <?php if(in_array($size['proSize_id'],$SelectedSizes)){ ?> selected="selected" 
                                                            <?php } ?> value="{{$size['proSize_id']}},{{$size['product_size']}}">{{$size['product_size']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @if($os_define_role != 'os')
                                                <div class="add-brand-btn" style="width:20%; float: left; padding-top: 5px;">
                                                    <a class="hand-cursor" data-toggle="modal" data-target="#addSizeModal">
                                                        <i class="material-icons">
                                                            add_circle_outline
                                                        </i>
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        {{--End Add Size MultiSelect Box--}}

                                        {{--Item  Size  Box--}}
                                        <?php $valuess = explode('-', $data->item_size_width_height); ?>
                                        <div class="form-group itemSize">
                                            <label class="col-md-4 control-label">Item Size</label>
                                            <div class="col-md-3">
                                                <div id="custom-search-input">
                                                    <div class="input-group col-md-3">
                                                        <input type="text" class="form-control input-sm" name="item_size_width" placeholder="width" style="width: 60px !important;" value="{{$valuess[0]}}" {{($os_define_role == 'os')?'readonly':''}}/>
                                                        <span class="input-group-btn" style=" color: #555;font-size: 18px; padding: 0px 5px; border: 1px solid #ddd; border-left:none ;" >
                                                            mm
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div id="custom-search-input">                
                                                    <div class="input-group col-md-3">
                                                        <input type="text" class="form-control input-sm" name="item_size_height" placeholder="height" style="width: 60px !important;" value="{{$valuess[1]}}" {{($os_define_role == 'os')?'readonly':''}}/>
                                                        <span class="input-group-btn" style="color: #555;font-size: 18px;padding: 0px 5px; border: 1px solid #ddd; border-left:none ;">
                                                            mm
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($os_define_role == 'os' || session::get('user_type') == "super_admin")
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Item Color</label>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control" name="other_colors" value="{{ $data->other_colors }}" placeholder="Item Color">
                                                </div>
                                            </div>
                                        @endif 
                                    </div>

                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">{{ trans('others.product_erp_code_label') }}</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control  input_required" name="p_erp_code" value="{{$data->erp_code}}" {{($os_define_role == 'os')?'readonly':''}}>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">{{ trans('others.product_unit_price_label') }}</label>
                                            <div class="col-md-6">
                                                <div>
                                                    <input type="text" class="form-control " name="p_unit_price" value="{{ $data->unit_price}}" {{($os_define_role == 'os')?'readonly':''}}>
                                                </div>
                                                <div class="add-vendor-com-price-btn">
                                                    @if($os_define_role != 'os')
                                                        <a class="hand-cursor float_left" data-toggle="modal" data-target="#addVendorComPrice">
                                                            <i class="material-icons">add_circle_outline</i>
                                                        </a>
                                                        <small class="price_icon">Vendor Price</small>
                                                    @endif

                                                    <a class="hand-cursor float_left_padding" data-toggle="modal" data-target="#addSupplierPrice">
                                                        <i class="material-icons">add_circle_outline</i>
                                                    </a>
                                                    <small class="price_icon">Supplier Price</small>

                                                    @if($os_define_role != 'os')
                                                        <a class="hand-cursor float_left_padding" data-toggle="modal" data-target="#addCostPrice">
                                                            <i class="material-icons">add_circle_outline </i>
                                                        </a>
                                                        <small class="price_icon">Cost Price</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">{{ trans('others.product_weight_qty_label') }}</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="p_weight_qty" value="{{$data->weight_qty}}" {{($os_define_role == 'os')?'readonly':''}}>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label">{{ trans('others.product_weight_amt_label') }}</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="p_weight_amt" value="{{$data->weight_amt}}" {{($os_define_role == 'os')?'readonly':''}}>
                                            </div>
                                        </div>

                                        {{--<div class="form-group">--}}
                                            {{--<label class="col-md-4 control-label">{{ trans('others.product_type_label') }}</label>--}}
                                            {{--<div class="col-sm-6">--}}
                                                {{--<div class="select">--}}
                                                    {{--<select class="form-control" type="select" name="product_type" >--}}
                                                        {{--@if($data->product_type == 'MRF')--}}
                                                            {{--<option  value="MRF" >MRF</option>--}}
                                                            {{--<option value="IPO" >IPO</option>--}}
                                                        {{--@else--}}
                                                            {{--<option value="IPO" >IPO</option>--}}
                                                            {{--<option  value="MRF" >MRF</option>--}}
                                                        {{--@endif--}}

                                                    {{--</select>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}

                                        {{--<div class="form-group">--}}
                                            {{--<label class="col-md-4 control-label">{{ trans('others.product_type_label') }}</label>--}}
                                            {{--<div class="col-sm-6">--}}
                                                {{--<div class="select">--}}
                                                    {{--<select class="form-control" type="select" name="product_type" >--}}
                                                        {{--@if($data->product_type == 'MRF')--}}
                                                            {{--<option  value="MRF" >MRF</option>--}}
                                                            {{--<option value="IPO" >IPO</option>--}}
                                                        {{--@else--}}
                                                            {{--<option value="IPO" >IPO</option>--}}
                                                            {{--<option  value="MRF" >MRF</option>--}}
                                                        {{--@endif--}}
                                                    {{--</select>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}

                                        <div class="form-group ipo_increase_percentage" style="display: none;">
                                            <label class="col-md-4 control-label">{{ trans('others.ipo_increase_percentage') }}</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="item_inc_percentage" value="{{ $data->item_inc_percentage }}" {{($os_define_role == 'os')?'readonly':''}}>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4">Product Status</label>
                                            <div class="col-md-6">
                                                <div class="select">
                                                    <select class="form-control" type="select" name="is_active" style="{{($os_define_role == 'os')?'pointer-events: none; background-color: #ddd;':''}}">
                                                        <option value="{{$data->status}}">
                                                            {{($data->status == 1) ? "Active" : "Inactive"}}
                                                        </option>

                                                        <option  value="1" name="is_active" >{{ trans('others.action_active_label') }}</option>
                                                        <option value="0" name="is_active" >{{ trans('others.action_inactive_label') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        @if($os_define_role == 'os' || session::get('user_type') == "super_admin")
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Material</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" name="material" value="{{ $data->material }}" placeholder="Material">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Add Vendor Company Price-->
                                <div class="modal fade" id="addVendorComPrice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">Vendor Company Price
                                                        <button type="button" class="close" data-dismiss="addVendorComPrice" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>

                                                    <div class="panel-body">
                                                        {{--<form class="form-horizontal vendor-price" role="form" method="POST" action="{{ Route('create_brand_action') }}">--}}

                                                        @if ($errors->any())
                                                            <div class="alert alert-danger">
                                                                <ul>
                                                                    @foreach ($errors->all() as $error)
                                                                        <li>{{ $error }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif

                                                        @foreach($vendorCompanyListPrice as $vCom)
                                                            <input type="hidden" name="price_id[]" value="{{ $vCom->price_id  }}" >
                                                            <input type="hidden" name="party_table_id[]" value="{{ $vCom->party_table_id  }}" >

                                                            <div class="col-md-4">
                                                                {{--<label class="control-label">Size Name</label>--}}
                                                                <input type="text" class="form-control" value="{{ $vCom->party->name_buyer  }}" readonly>
                                                            </div>

                                                            <div class="col-md-5">
                                                                {{--<label class="control-label col-md-12">Size Name</label>--}}
                                                                <input type="text" class="form-control" value="{{ $vCom->party->name  }}" readonly>
                                                            </div>

                                                            <div class="col-md-3">
                                                                {{--<label class="control-label">Size Name</label>--}}
                                                                <input type="text" class="form-control v_com_price" name="v_com_price[]" value="{{$vCom->vendor_com_price}}" placeholder="Enter Price">
                                                            </div>

                                                        @endforeach

                                                        @if(isset($vendorCompanyListPrice->missingParty) && !empty($vendorCompanyListPrice->missingParty))

                                                            @foreach($vendorCompanyListPrice->missingParty as $missing_party)
                                                            <input type="hidden" name="price_id[]" value="" >
                                                            <input type="hidden" name="party_table_id[]" value="{{ $missing_party->id  }}" >

                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control" value="{{ $missing_party->name_buyer  }}" readonly>
                                                            </div>

                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control" value="{{ $missing_party->name  }}" readonly>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control v_com_price" name="v_com_price[]" value="" placeholder="Enter Price">
                                                            </div>
                                                            @endforeach
                                                        @endif

                                                        @foreach($vendorCompanyList as $vCom)
                                                            <input type="hidden" name="party_table_id[]" value="{{ $vCom->id  }}" >

                                                            <div class="col-md-4">
                                                                {{--<label class="control-label">Size Name</label>--}}
                                                                <input type="text" class="form-control" value="{{ $vCom->name_buyer  }}" readonly>
                                                            </div>

                                                            <div class="col-md-5">
                                                                {{--<label class="control-label col-md-12">Size Name</label>--}}
                                                                <input type="text" class="form-control" value="{{ $vCom->name  }}" readonly>
                                                            </div>

                                                            <div class="col-md-3">
                                                                {{--<label class="control-label">Size Name</label>--}}
                                                                <input type="text" class="form-control" name="v_com_price[]" value="" placeholder="Enter Price">
                                                            </div>
                                                        @endforeach

                                                        {{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}

                                                            <div class="form-group">
                                                                <div class="col-md-2 col-md-offset-10">
                                                                    <button class="btn btn-primary vendor-price-btn" style="margin-right: 15px;">
                                                                        Next
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        {{--</form>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Supplier Price-->
                                <div class="modal fade" id="addSupplierPrice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">Supplier Price
                                                        <button type="button" class="close" data-dismiss="addSupplierPrice" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="panel-body">
                                                        @if ($errors->any())
                                                            <div class="alert alert-danger">
                                                                <ul>
                                                                    @foreach ($errors->all() as $error)
                                                                        <li>{{ $error }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif

                                                        @foreach($supplierPrices as $supplierPrice)
                                                            <input type="hidden" name="supplie_price_id[]" value="{{ $supplierPrice->supplier_price_id  }}" >
                                                            <input type="hidden" name="supplier_id[]" value="{{ $supplierPrice->supplier_id  }}" >
                                                            <input type="hidden" name="price_id[]" value="{{ $supplierPrice->price_id  }}" >

                                                            <div class="col-md-5 col-md-offset-2">
                                                                <input type="text" class="form-control" value="{{ $supplierPrice->supplier->name  }}" readonly>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control supplier_price" name="supplier_price[]" value="{{ $supplierPrice->supplier_price}}" placeholder="Enter Price">
                                                            </div>
                                                        @endforeach

                                                        @foreach($supplierList as $supplier)
                                                            <input type="hidden" name="supplier_id[]" value="{{ $supplier->supplier_id  }}" >

                                                            <div class="col-md-5 col-md-offset-2">
                                                                <input type="text" class="form-control" value="{{ $supplier->name  }}" readonly>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control" name="supplier_price[]" value="" placeholder="Enter Price">
                                                            </div>
                                                        @endforeach

                                                        <div class="form-group">
                                                            <div class="col-md-2 col-md-offset-10">
                                                                <button class="btn btn-primary supplier-price-btn" style="margin-right: 15px;">
                                                                    Next
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Cost Price Modal -->
                                <div class="modal fade" id="addCostPrice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">Cost Price
                                                        <button type="button" class="close addCostPrice" data-dismiss="addCostPrice" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="label-control col-md-12">Price 1</label>
                                                                <div class="col-md-12">
                                                                    <input type="text" name="cost_price_1" class="form-control" placeholder="Ammount.." value="{{$data->cost_price->price_1}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label class="label-control col-md-12">Price 2</label>
                                                                <div class="col-md-12">
                                                                    <input type="text" name="cost_price_2" class="form-control" placeholder="Ammount.." value="{{$data->cost_price->price_2}}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <div class="col-md-2 col-md-offset-10">
                                                                <button class="btn btn-primary addCostPrice " style="margin-right: 15px;">
                                                                    Next
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="col-sm-2 col-sm-offset-8">
                                        <button type="submit" class="btn btn-primary form-control" style="margin-right: 15px;">
                                            {{ trans('others.update_button') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(".selections").select2();
        $(".select-color-list").select2();
        $(".select-size-list").select2();

        // var selectedColors = $(".select-color-list").select2();
        // var selectedSizes = $(".select-size-list").select2();

        // var colors = {!! json_encode($colorsJs) !!};
        // var sizes = {!! json_encode($sizesJs) !!};


        // selectedColors.val(colors).trigger("change");
        // selectedSizes.val(sizes).trigger("change");
    </script>

    <script type="text/javascript">
        // inputOnlyNumberCheck('p_unit_price');

        // function inputOnlyNumberCheck(classs) {
        //     var regex = /[0-9]|\./;
        //     $('.'+classs).on('focusout',function(){
        //         var key = $(this).val();
        //         if( !regex.test(key) ) {
        //             alert("Only allow Number.");
        //             $(this).val(' ');
        //         }
        //     });
        // }       
    </script>
@endsection
