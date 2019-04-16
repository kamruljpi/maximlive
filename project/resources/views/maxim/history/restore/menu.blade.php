<?php 
	use App\Http\Controllers\taskController\Flugs\HeaderType;
?>
<div class="row" style="margin: 10px;">
	<div class="col-sm-12">
		<ul class="nav nav-pills">
		  <li role="presentation" class="{{(Session::get('type') == HeaderType::BOOKING)?'active':''}}">
		  	<a href="{{Route('sent_list_request',[HeaderType::BOOKING])}}">Booking</a>
		  </li>

		  <li role="presentation" class="{{(Session::get('type') == HeaderType::PI)?'active':''}}">
		  	<a href="{{Route('sent_list_request',[HeaderType::PI])}}">Pi List</a>
		  </li>

		  <li role="presentation" class="{{(Session::get('type') == HeaderType::IPO)?'active':''}}">
		  	<a href="{{Route('sent_list_request',[HeaderType::IPO])}}">IPO</a></li>
		  <li role="presentation" class="{{(Session::get('type') == HeaderType::MRF)?'active':''}}">
		  	<a href="{{Route('sent_list_request',[HeaderType::MRF])}}">MRF</a>
		  </li>
		  <li role="presentation" class="{{(Session::get('type') == HeaderType::CHALLAN)?'active':''}}">
		  	<a href="{{Route('sent_list_request',[HeaderType::CHALLAN])}}">Challan</a>
		  </li>
		</ul>
	</div>
</div>