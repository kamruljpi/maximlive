@extends('layouts.dashboard')
@section('page_heading','Bulk Upload')
@section('section')
	<div class="row">
		<div class="col-sm-12">
			@if(session()->has('bulkerror'))
				<div class="alert alert-danger">
					{!! session('bulkerror') !!}
				</div>
			@endif
			@if(session()->has('bulksuccess'))
				<div class="alert alert-success">
					{!! session('bulksuccess') !!}
				</div>
			@endif
		</div>
	</div>

	<div class="col-sm-8 col-sm-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3>
					Item Upload
				</h3>
			</div>
			<div class="panel-body">
				<form action="{{ route('itemuploadactionview')}}" method="POST" role="form" enctype="multipart/form-data">
					{{csrf_field()}}
					<div class="form-group row">
						<label class="col-sm-3 control-label" for="bulkupload"> <span class="pull-right">Item file :</span></label>
						<div class="col-sm-5">
							<input type="file" name="bulk" class="form-control-file" id="bulkupload">
						</div>
						<div class="col-sm-4">
							<button type="submit" class="btn btn-primary" style="margin-right: 15px;">
	                        	<span class="pull-left">
	                        		Upload
	                        	</span>
							</button>
						</div>
					</div>
				</form>
				<div class="col-sm-12">
					<div class="form-group">
						<label class="col-sm-4">
							<span class="pull-right" style="padding-top: 8px">Preferred File Format :</span>
						</label>
						<div class="col-sm-6">
							<a href="{{ asset('excel/item.xlsx')}}" class="btn btn-default">Download excel <i class="fa fa-download"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<table class="table table-bordered table-striped">
				<thead>

				<tr>
                    <?php
                    $title_key = ['brand','item_code','item_description','item_size','erp_code','unit_price','unit','error'];

                    if(session('err_item_list')){
                        foreach(session('err_item_list') as $xkey => $xval){
                            foreach($xval as $xxkey => $xxval){
                                if(in_array($xxkey, $title_key)){
                                    print '<th class="tablehead_'.$xxkey.'">'.str_replace("_"," ", $xxkey).'</th>';
                                }
                            }
                            break;
                        }
                    }
                    ?>
				</tr>
				</thead>
				<tbody>
                <?php
                $title_key = ['brand','item_code','item_description','item_size','erp_code','unit_price','unit','error'];

                if(session('err_item_list')){
                foreach(session('err_item_list') as $xkey => $xval){
                ?>
				<tr>
                    <?php
                    foreach($xval as $xxkey => $xxval){
                        if(in_array($xxkey, $title_key)){
                            print '<td class="table_data_'.$xxkey.'">'.$xxval.'</td>';
                        }
                    }
                    ?>
				</tr>
                <?php
                }
                }
                ?>
				</tbody>
			</table>
		</div>
	</div>
@endsection