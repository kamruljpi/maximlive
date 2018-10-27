var simple_search = (function(){
	return {
		init: function(){
			$("#booking_list_simple_search").click(function ()
			{
			    var booking_id = $('#booking_id_search').val();
			    if(booking_id == ''){
			        alert("The search field cannot be empty");
			        return false;
			    }
			    else
			    {
			       $.ajax({
		           type: "GET",
		           url: baseURL+"/booking_list_by_booking_id",
		           data: "booking_id="+booking_id,
		           datatype: 'json',
		           cache: true,
		           async: true,
		           success: function(result) {
		           	console.log(result);
		           },
	                error:function(result){
	                   alert("Something is wrong.");
	                }

	            	});
			    }
			});
		}
	}
})();

$(document).ready(function(){
	simple_search.init();
});