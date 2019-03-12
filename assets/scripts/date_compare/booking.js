var trainingScheduleValidDate = (function(){
	return {
		init: function(){
			var poupValue ;
			$('#datePickerDate').on('change',function(){
				var _token = $('input[name=_token]').val();
				var dates_ = $(this).attr("value");
				var requested_date = $('#datePickerDate').val();
				var d = new Date();
				var day = d.getDate();
				var Month = 1 + d.getMonth();
				var years = d.getFullYear();
				var current_date = years+'-'+Month+'-'+day;

				var requested_dates = dateFormatter(requested_date);
				var current_dates = dateFormatter(current_date);

				if(current_dates - requested_dates > 0){
					alert("Please enter the requested date after the current.");
					$('#datePickerDate').val(dates_);
					return false;
				}
			});
		}
	}
})();

$(document).ready(function(){
	trainingScheduleValidDate.init();
});