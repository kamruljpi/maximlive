var hover_event = (function(){
	return {
		init: function(){
			$('#checking_booking_status').on('mouseover',function(){
				alert('ssss');
			});
		}
	}
})();

$(document).ready(function(){
	hover_event.init();
});