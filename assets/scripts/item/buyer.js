var item_buyer_select = (function(){
	
	return {
		init: function(){
			$('#id_buyer').on('change',function(){
				var $buyer_id = $(this).val();
        // size($buyer_id);
				vendor($buyer_id);
			});
		}
	}
})();

function size(buyer_id){
	$.ajax({
          type: "GET",
          url: baseURL+"/get/buyer/wise/size",
          data: "buyer_id="+buyer_id,
          datatype: 'json',
          cache: true,
          async: true,
          success: function(result) {
          	var myObj = JSON.parse(result);
          	apendSize(myObj);
          },
          error:function(result){
            alert("Something is wrong.");
          }

      });
}

function apendSize(result){
	var length = result.length;
    for (var i = 0; i < length; i++)
    {
    	var $apend_html = '<option value='+result[i].proSize_id+','+result[i].product_size+'>'+result[i].product_size+'</option>';
    	$('#sizes').append($apend_html);
    }
}

function color(buyer_id){
	$.ajax({
          type: "GET",
          url: baseURL+"/get/buyer/wise/color",
          data: "buyer_id="+buyer_id,
          datatype: 'json',
          cache: true,
          async: true,
          success: function(result) {
          	var myObj = JSON.parse(result);
          	console.log(myObj);
          },
          error:function(result){
            alert("Something is wrong.");
          }

      });
}

function vendor(buyer_id){
	$.ajax({
          type: "GET",
          url: baseURL+"/get/buyer/wise/vendor/list",
          data: "buyer_id="+buyer_id,
          datatype: 'json',
          cache: true,
          async: true,
          success: function(result) {
          	var myObj = JSON.parse(result);
            apendVendor(myObj);
          	console.log(myObj);
          },
          error:function(result){
            alert("Something is wrong.");
          }

      });
}

function apendVendor(result){
    var length = result.length;

    $('.party_table_id').html('');
    $('.buyer_name').html('');
    $('.name').html('');
    $('.v_com_price').html('');

    for (var i = 0; i < length; i++)
    {
      var $apend_html_1 = '<input type="hidden" name="party_table_id['+i+']" value="'+result[i].id+'" >';
      var $apend_html_2 = '<input type="text" class="form-control" value="'+result[i].name_buyer+'" disabled>';
      var $apend_html_3 = '<input type="text" class="form-control" value="'+result[i].name+'" disabled>';
      var $apend_html_4 = '<input type="text" class="form-control v_com_price" name="v_com_price['+i+']" placeholder="Enter Price">';

      $('.party_table_id').append($apend_html_1);
      $('.buyer_name').append($apend_html_2);
      $('.name').append($apend_html_3);
      $('.v_com_price').append($apend_html_4);
    }

}

$(document).ready(function(){
	item_buyer_select.init();
});