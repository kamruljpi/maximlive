<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
	<meta charset="utf-8"/>
	<title>{{ trans('others.company_name')}}</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="maxproit.solutions" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="keywords" content="HTML5, CSS3, Bootsrtrap, Responsive, Template, Theme, Website, ERP" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="shortcut icon" href="{{asset('assets/img/icon.png')}}" type="image/x-icon" />
	<link rel="stylesheet" href="{{ asset('assets/stylesheets/styles.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/stylesheets/main.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/stylesheets/bootstrap-datepicker.css') }}" />

		{{-- for select2 --}}

	<link href="{{ asset('assets/customByMxp/css/select2.min.css') }}" rel="stylesheet" />
	<link rel="stylesheet" href="{{ asset('assets/stylesheets/preloder.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/scripts/easy-autocomplete.min.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/scripts/easy-autocomplete.themes.min.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/scripts/bootstrap-datetimepicker.min.css') }}" />
	<script src="{{ asset('assets/scripts/jquery-3.3.1.min.js') }}"></script>
	<script src="{{ asset('assets/customByMxp/js/select2.min.js') }}"></script>
	<script type="text/javascript">
		var baseURL = '{{ url("/") }}';
	</script>
	<style type="text/css">
		/* Preloader */
		#preloader2 {
		  /*position: fixed;*/
		  top: 0;
		  left: 0;
		  right: 0;
		  bottom: 0;
		  z-index: 9999;
		}

		#status {
		  width: 200px;
		  height: 200px;
		  position: absolute;
		  left: 50%;
		  top: 50%;
		  /*background-color: #ddd; */
		  background-repeat: no-repeat;
		  background-position: center;
		  margin: -100px 0 0 -100px;
		  background-image: url({{asset('assets/img/preloader/status.gif')}});
		}
		.navbar-brand {
		    padding: 0 15px !important;
		}
		.navbar-top-links {
		    padding-top: 10px !important;
		}
		.page-header {
		    margin: 20px 0 10px; 
		}
		nav.navbar.navbar-default.navbar-static-top {
		    padding: 10px 0px 5px;
		}
	</style>
</head>
<body>
	<?php $languages = App\Http\Controllers\Trans\TranslationController::getLanguageList();?>
	@yield('body')
	<!-- Preloader -->
    <div class="preloader2">
      <div class="status">
          <div class="abc"></div>
      </div>
    </div>

	<script src="{{ asset('assets/scripts/moment.min.js') }}"></script>
	<script src="{{ asset('assets/scripts/bootstrap-datepicker.js') }}" type="text/javascript"></script>
	<script src="{{ asset('assets/scripts/bootstrap-datetimepicker.min.js') }}"></script>
	<script src="{{ asset('assets/scripts/frontend.js') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('js/custom.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/scripts/custom.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/new_custom.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/all_product_table.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/journal.js') }}"></script>
	<script src="{{ asset('assets/scripts/json-2.4.js') }}"></script>
	{{--<!-- <script src="{{ asset('assets/scripts/bootstrap-datetimepicker.min.js') }}"></script> -->--}}
	<script src="{{ asset('assets/scripts/multipleTable.js') }}"></script>
	<script src="{{ asset('assets/scripts/task/buyer.js') }}"></script>
	<script src="{{ asset('assets/scripts/task/taskTpye.js') }}"></script>
	<script src="{{ asset('assets/scripts/item/apend_unit_price.js') }}"></script>
	<script src="{{ asset('assets/scripts/jquery.easy-autocomplete.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/production.js') }}"></script>
	<script src="{{ asset('assets/scripts/booking/booking_view/view_page.js') }}"></script>
	<script src="{{ asset('assets/scripts/confirm/confirmation.js') }}"></script>

	<script>
	    $('.click_preloder').on('click', function() {
            var parentClass = $('.abc').parent().parent().attr('id');
            if(typeof(parentClass) =="undefined"){
                $('.preloader2').attr('id', 'preloader2');
                $('.status').attr('id', 'status');
            }
          $('#status').fadeOut(); 
          $('#preloader2').delay(10000).fadeOut('slow'); 
          $('body').delay(10000).css({'overflow':'visible'});
          $('.status').removeAttr('style',' '); 
          $('.preloader2').removeAttr('style',' '); 
        });

        $('.keyup_preloder').on('keyup', function() {
            var parentClass = $('.abc').parent().parent().attr('id');
            if(typeof(parentClass) =="undefined"){
                $('.preloader2').attr('id', 'preloader2');
                $('.status').attr('id', 'status');
            }
          $('#status').fadeOut(); 
          $('#preloader2').delay(5000).fadeOut('slow'); 
          $('body').delay(5000).css({'overflow':'visible'});
          $('.status').removeAttr('style',' '); 
          $('.preloader2').removeAttr('style',' '); 
        });

        $('.close__').on('click',function(){
            $('.modal').hide();
        });

        $('.__close').on('click',function(){
            $('.view_page').hide();
        });
	</script>
	@yield('LoadScript')
</body>
</html>