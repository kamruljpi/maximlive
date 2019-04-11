var location_event = (function(){
  return {
        init: function () {
			$('.tbody_tr').on('change','.location_id',function(){
				var selected = $(this).val();
				var item_parent_class = $.trim($(this).parent().parent().parent().prop('className'));

				$.ajax({
				    url:baseURL+"/zone/details",
				    type:"GET",
				    data:{selected},
				    datatype: 'json',
				    cache: false,
				    async: false,
					success:function(result){
		  		    var myObj3 = JSON.parse(result);

		  		    $('.'+item_parent_class+' .zone_id').html($('<option>', {
		  		        value: "",
		  		        text : "--Select--"
		  		    }));
		  		    
		  		    if(myObj3 != null) {
		  		    	var i;
		  		    	for (i = 0; i < myObj3.length; i++) {
		  		    	    $(".zone_id").append('<option value="'+myObj3[i].zone_id+'">'+myObj3[i].zone_name+'</option>');
		  		    	}
		  		    }
				    },
				    error:function(result){
				        alert("ERROR > "+result);
				    }
				});
			});

			$('.tbody_tr').on('click','.store_purchase_submit',function(){

				var item_parent_class = $.trim($(this).parent().parent().parent().prop('className'));

				var raw_item_id = $.trim($('.'+item_parent_class+' .raw_item_id').val());
				var raw_item_code = $.trim($('.'+item_parent_class+' .raw_item_code').val());
				var item_qty = $.trim($('.'+item_parent_class+' .item_qty').val());
				var price = $.trim($('.'+item_parent_class+' .price').val());
				var total_price = $.trim($('.'+item_parent_class+' .total_price').val());
				var location_id = $.trim($('.'+item_parent_class+' .location_id').val());
				var zone_id = $.trim($('.'+item_parent_class+' .zone_id').val());
				var warehouse_type_id = $.trim($('.'+item_parent_class+' .warehouse_type_id').val());

				var datas = {
					'item_parent_class' : item_parent_class,
					'raw_item_id' : raw_item_id,
					'raw_item_code' : raw_item_code,
					'item_qty' : item_qty,
					'price' : price,
					'total_price' : total_price,
					'location_id' : location_id,
					'zone_id' : zone_id,
					'warehouse_type_id' : warehouse_type_id
				}

					// console.log(datas);
				$.ajax({
				    url:baseURL+"/store/show_purchase",
				    type:"GET",
				    data:"datas="+datas,
				    datatype: 'json',
				    cache: false,
				    async: false,
					success:function(result){
		  		    // var myObj3 = JSON.parse(result);

					console.log(result);
				    },
				    error:function(result){
				        alert("ERROR > "+result);
				    }
				});


				return false;
			});
        }
    };
})();

$(document).ready(function(){
  location_event.init();
});