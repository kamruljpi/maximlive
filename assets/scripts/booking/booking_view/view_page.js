var booking_views_js = (function(){
	return {
		init: function(){
			$('#normal-btn-success').delay(5000).fadeOut( "slow", function() {
			    $('.close').prop("disabled", false);
			});
			$('.increase_field').addClass('hidden');
			$('input[name="ipo_or_mrf"]').on('click',function () {
		        var value = $(this).val();
		        if(value == 'ipo'){
		            $('.increase_field').removeClass('hidden');
		        }else{
		            $('.increase_field').addClass('hidden');
		        }
		    });

		    var $checked = $('.vi_table tbody tr').find('input[type="checkbox"]');
			if($checked.prop("checked") == true){
	        	$('.vi_table tbody tr').css('background-color','#C4d1FF');
	        }

		    $('.vi_table tbody tr').on('click', function (e) {
		        var checked= $(this).find('input[type="checkbox"]');
		        if(checked.prop('disabled') != true){
			        checked.prop('checked', !checked.is(':checked'));
			        if(checked.prop("checked") == true){
			        	$(this).css('background-color','#C4d1FF');
			        }else{
			        	$(this).css('background-color','');
			        }
		    	}else{
		    		alert('Please Check the Booking and Job id Item Quentity.');
		    	}
		    });

		    $('#select_check input[type="checkbox"]').on('click',function () {
		        $(this).prop('checked', !$(this).is(':checked'));
		    });
		}
	}
})();

$(document).ready(function(){
	booking_views_js.init();
});