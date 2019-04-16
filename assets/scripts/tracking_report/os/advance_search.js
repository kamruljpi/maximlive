var advance_search = (function(){
	return {
		init: function(){
			$('#os_report_advance_search').on('click',function (ev)
			{
			    displaySetup("#booking_simple_search_form", "#os_advance_search_form"); // this function use js/production.js
			});

			$('#os_report_simple_search_btn').on('click',function (ev)
			{
			    displaySetup("#os_advance_search_form", "#booking_simple_search_form"); // this function use js/production.js
			});

			$('#os_advance_search_form').on('submit',function (ev){
			    ev.preventDefault();
			    var  data = $('#os_advance_search_form').serialize();
			    var results = ajaxFunc("/booking_list_book_search", "POST", data);

			    if((results.responseJSON != '') && (results.responseJSON != null))
			        addPlanningReportRow(results.responseJSON, 0); // this function use planning/simple_search.js
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