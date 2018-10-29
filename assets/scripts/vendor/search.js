var vendor_search = (function(){
	return {
        init: function () {
        	// $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });

        	$('#vendor_search').on('keyup',function(){
        	    var value = $(this).val();
        	    $.ajax({
        	        type : 'GET',
        	        url : baseURL+'/vendor/search',
        	        data:{'vendor_name':value},
        	        success:function(result){
        	          $('#vendor_tbody tbody').html(result);
        	        },
	          	error:function(result){
	            	  alert("Something is wrong.");
	          	}
        	    });
        	});
		}
	};
})();

$(document).ready(function(){
  vendor_search.init();
});