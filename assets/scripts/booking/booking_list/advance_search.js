var advance_search = (function(){
	return {
		init: function(){
			$('#booking_list_advance_search').on('click',function (ev)
			{
			    displaySetup("#booking_simple_search_form", "#booking_list_advance_search_form"); // this function use js/production.js
			});

			$('#booking_simple_search_btn').on('click',function (ev)
			{
			    displaySetup("#booking_list_advance_search_form", "#booking_simple_search_form"); // this function use js/production.js
			});

			$('#booking_list_advance_search_form').on('submit',function (ev)
			{
			    ev.preventDefault();
			    var  data = $('#booking_list_advance_search_form').serialize();
			    var results = ajaxFunc("/booking_list_advance_search_", "POST", data);
			    if((results.responseJSON != '') && (results.responseJSON != null)){
			        addBookingListRow(results.responseJSON, 0); // this function use booking/booking_list/simple_search.js
                }
			    else {
			        EmptyValueView('.pagination', '#booking_list_tbody', "#booking_list_pagination", 9); // this function use js/production.js
			    }
			});
		}
	}
})();

$(document).ready(function(){
	advance_search.init();
});