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

		    $('.vi_table tbody tr').on('click', function (e) {
		    	// if ($(this).hasAttribute('disabled')) {
       //          	return false;
       //      	}
		        var checked= $(this).find('input[type="checkbox"]');
		        checked.prop('checked', !checked.is(':checked'));
		    });

		    $('input[type="checkbox"]').on('click',function () {
		        $(this).prop('checked', !$(this).is(':checked'));
		    });
		}
	}
})();

$(document).ready(function(){
	booking_views_js.init();
});