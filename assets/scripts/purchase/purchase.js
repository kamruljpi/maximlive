
var purchase = (function(){
  return {
        init: function () {

            // when purchase_order revert on purchase 
            // page then need this code to get autocomplete raw item value

            var getRawItemOptionValueEditPage = {

              url: function(phrase) {
                return baseURL+"/get/raw_item_code";
              },

              getValue: function(element) {
                return element.name;
              },

              list: {
                  match: {
                      enabled: true
                  },
              },

              ajaxSettings: {
                dataType: "json",
                method: "GET",
                data: {
                  dataType: "json"
                }
              },

              requestDelay: 400
            };

            $('.abc').easyAutocomplete(getRawItemOptionValueEditPage);

            //end

            // this section add copy and add new row

            var rawItemOption = {

              url: function(phrase) {
                return baseURL+"/get/raw_item_code";
              },

              getValue: function(element) {
                return element.name;
              },

              list: {
                  match: {
                      enabled: true
                  },
              },

              ajaxSettings: {
                dataType: "json",
                method: "GET",
                data: {
                  dataType: "json"
                }
              },

              requestDelay: 400
            };

            $('.tr_clone .raw_item_code').easyAutocomplete(rawItemOption);

            var incre = 0;

        	$("#add_new_field").click(function () {

                total_price_2 = 0 ;
                var clone = $('.idclone .tr_clone:last').clone(true);

                clone.addClass('tr_clone_'+incre).removeClass('tr_clone').appendTo(".idclone");

                // remove value in clone field
                $(".tr_clone_"+incre+" .price").val(0);
                $(".tr_clone_"+incre+" .item_qty").val(0);
                $(".tr_clone_"+incre+" .raw_item_id").val(' ');
                $(".tr_clone_"+incre+" .total_price").val(0);
                // end

                $(".tr_clone_"+incre+" .raw_item_code").removeAttr('id');
                $(".tr_clone_"+incre+" .raw_item_code").parents('.easy-autocomplete').remove();
                $(".tr_clone_"+incre+" .easy-autocomplete-container").remove();

                $(".tr_clone_"+incre+" .remove_field").removeAttr('disabled', 'false');

                var itmelm = '<input class="form-control raw_item_code" data-parent="tr_clone_'+incre+'" type="text" name="item_code[]"  id="raw_item_code_'+incre+'" placeholder="Item Code">';
                
                $(".tr_clone_"+incre+" .item_code_parent").append(itmelm);

                $('#raw_item_code_'+incre).easyAutocomplete(rawItemOption);

                incre++;

                return false;
        	});

            // End

            // this section remove add new field

        	$('.idclone').on('click','.remove_field',function(){
        		var remove_class = $(this).parent().parent().prop('className');

        		if(remove_class != 'tr_clone'){
        			$('.idclone .'+remove_class).remove();
        		}else{
        			alert('You cannot remove this row.');

                    return false ;
        		}
        	});

            // End
        }
    };
})();

var raw_item_code_event = (function(){
  return {
        init: function () {
        	$('.idclone').on('change','.raw_item_code',function(){
        		var item_code = encodeURIComponent($(this).val());
        		var item_parent_class = $.trim($(this).parent().parent().parent().parent().prop('className'));

        		$.ajax({
        		    type: "GET",
        		    url: baseURL+"/get/raw_item/details_by_code",
        		    data: "item_code="+item_code,
        		    datatype: 'json',
        		    cache: true,
        		    async: true,
        		    success: function(result) {
        		    	var myObj = JSON.parse(result);

        		    	if(myObj != '' && myObj != null){
                            $('.'+item_parent_class+' .price').val(myObj.price);
                            $('.'+item_parent_class+' .raw_item_id').val(myObj.id_raw_item);
                            $('.'+item_parent_class+' .item_qty').val('0');
                            $('.'+item_parent_class+' .total_price').val('0');
                            $('.in_all_total_price').val('0');
                            $('.grand_total').val('0');
                            
        		    	}
        		    },
		            error:function(result){
		            	alert("Something is wrong.");
		            }

			      });
        	});
        }
    };
})();


var item_price_event = (function(){
  return {
        init: function () {
            $('.idclone').on('keyup','.price',function(){
                var tt = 0;

                var item_price = $(this).val();

                // empty check
                if(item_price == '') {
                  item_price = 0 ;
                }

                // dot (.) check
                if(item_price == '.') {
                  item_price = 0 ;
                }

                // if( Object.prototype.toString.call(myvar) == '[object String]' ) {
                //    // a string
                // }

                var item_parent_class = $(this).parent().parent().parent().prop('className');

                var item_qty = $('.'+item_parent_class+' .item_qty').val();
                var total_price = item_qty * item_price ;

                $('.'+item_parent_class+' .total_price').val(total_price);

                $(".total_price").each(function(a,b){

                    tt += parseFloat($(this).val());
                    
                    $(".in_all_total_price").val(parseFloat(tt));
                    $('.grand_total').val(parseFloat(tt));
                });
            });
        }
    };
})();

var main = function () {
    $('.idclone').on('keyup','.item_qty',function(){
        var tt = 0;
        var item_qty = $(this).val();

        //empty check
        if(item_qty == '') {
          item_qty = 0 ;
        }

        // dot (.) check
        if(item_qty == '.') {
          item_qty = 0 ;
        }

        var item_parent_class = $(this).parent().parent().parent().prop('className');

        var item_price = $('.'+item_parent_class+' .price').val();

        var total_price = item_qty * item_price ;

        $('.'+item_parent_class+' .total_price').val(total_price);

        $(".total_price").each(function(a,b){

            tt += parseFloat($(this).val());

            $(".in_all_total_price").val(parseFloat(tt));
            $('.grand_total').val(parseFloat(tt));
        });

    });

};

var grand_total = function(){
    $('#TableFooter').on('keyup', '.discount', function () {
       var discount = parseFloat($('.discount').val() );
       var total = parseFloat($('.in_all_total_price').val() );
       if (discount >= 0){
           var grand_total = parseFloat(total - discount);
           $('.grand_total').val(grand_total);
       }

    });
    $('#TableFooter').on('keyup', '.vat', function () {

        var vat = parseFloat($('.vat').val() );
        var total = parseFloat($('.in_all_total_price').val() );
        if (vat >= 0){
            var grand_total = parseFloat(total + vat);
            $('.grand_total').val(grand_total);
        }
    });
}

$(document).ready(function(){
    main();
    grand_total();
  purchase.init();
  raw_item_code_event.init();
  // item_qty_event.init();
  item_price_event.init();
});