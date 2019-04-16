var products_details  = (function(){
  return {
        init: function () {

            // this section add copy and add new row
            var addRawItemOption = {

              url: function(phrase) {
                return baseURL+"/get/itemcode";
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

            $('.abc').easyAutocomplete(addRawItemOption);

            $('#p_item_code').easyAutocomplete(addRawItemOption);

            var incre = 0;

            $("#add_new_field_product").click(function () {

                var clone = $('.idclone_product .p_tr_clone:last').clone(true);

                clone.addClass('p_tr_clone_'+incre).removeClass('p_tr_clone').appendTo(".idclone_product");

                // remove value in clone field
                $(".p_tr_clone_"+incre+" .p_item_id").val(' ');
                $(".p_tr_clone_"+incre+" .p_item_qty").val(0);

                $('.p_tr_clone_'+incre+' .p_size_range').html($('<option>', {
	                value: " ",
	                text : "--Select--"
                }));

                $('.p_tr_clone_'+incre+' .p_gmt_color').html($('<option>', {
	                value: " ",
	                text : "--Select--"
                }));
                // end

                $(".p_tr_clone_"+incre+" .p_item_code").removeAttr('id');
                $(".p_tr_clone_"+incre+" .p_item_code").parents('.easy-autocomplete').remove();
                $(".p_tr_clone_"+incre+" .easy-autocomplete-container").remove();
                $(".p_tr_clone_"+incre+" .remove_field").removeAttr('disabled', 'false');
                $(".p_tr_clone_"+incre+" .p_item_code").remove();

                var itmelm = '<input class="form-control p_item_code" data-parent="p_tr_clone_'+incre+'" type="text" name="p_item_code[]"  id="p_item_code'+incre+'" placeholder="Item Code">';
                var addinput = '<input class="form-control addinput" type="hidden" name="addinput" value="p_new">';
                $(".p_tr_clone_"+incre+" .p_item_code_parent").append(itmelm);
                $(".idclone_product").append(addinput);

                $('#p_item_code'+incre).easyAutocomplete(addRawItemOption);

                incre++;

                return false;
            });

            // End

            // this section remove add new field

            $('.idclone_product').on('click','.remove_field',function(){
                var remove_class = $(this).parent().parent().prop('className');

                if(remove_class != 'p_tr_clone'){                   

                  $('.idclone_product .'+remove_class).remove();
                }else{
                    alert('You cannot remove this row.');

                    return false ;
                }
            });

            // End

            $('.p_tr_clone').on('change','.p_item_code', function(){

            	var item_code = encodeURIComponent($(this).val());
            	var item_parent_class = $(this).parent().parent().parent().parent().prop('className');

            	$.ajax({
            	    type: "GET",
            	    url: baseURL+"/get/product_details",
            	    data: "item_code="+item_code,
            	    datatype: 'json',
            	    cache: true,
            	    async: true,
            	    success: function(result) {
            	        var myObj = JSON.parse(result);

            			if(myObj[0].product_id != null) {

            				$('.'+item_parent_class+' .p_item_id').val(myObj[0].product_id);

            				for(i in myObj){
            				  if (myObj[i].size === null) {
            				      $('.'+item_parent_class+' .p_size_range').html($('<option>', {
            				      value: "",
            				      text : "Empty Size"
            				      }));

            				  }else{
            				    $('.'+item_parent_class+' .p_size_range').html($('<option>', {
            				    value: "",
            				    text : "Select Size"
            				    }));

            				    var sizes = myObj[i].size.split(',');
            				        sizes = $.unique(sizes);
            				    for(j in sizes){
            				      $('.'+item_parent_class+' .p_size_range').append($('<option value="'+sizes[j]+'">'+sizes[j]+'</option>'));
            				    }
            				  }             
            				}

            				for(s in myObj){
            				  if(myObj[s].color === null){

            				    $('.'+item_parent_class+' .p_gmt_color').html($('<option>', {
	            				    value: "",
	            				    text : "Empty Colors"
            				    }));

            				  }else{
            				  	
            				    $('.'+item_parent_class+' .p_gmt_color').html($('<option>', {
	            				    value: "",
	            				    text : "Select colors"
            				    }));

            				    var colors = myObj[s].color.split(',');
            				    var colors = $.unique(colors);

            				    for(h in colors){
            				      $('.'+item_parent_class+' .p_gmt_color').append($('<option value="'+colors[h]+'">'+colors[h]+'</option>'));
            				    }
            				  }     
            				}
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


var raw_materials_used  = (function(){
  return {
        init: function () {

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

                var clone = $('.idclone .tr_clone:last').clone(true);

                clone.addClass('tr_clone_'+incre).removeClass('tr_clone').appendTo(".idclone");

                // remove value in clone field
                $(".tr_clone_"+incre+" .price").val(' ');
                $(".tr_clone_"+incre+" .item_qty").val(0);
                $(".tr_clone_"+incre+" .raw_item_id").val(' ');
                $(".tr_clone_"+incre+" .total_price").val(' ');
                // end

                $(".tr_clone_"+incre+" .raw_item_code").removeAttr('id');
                $(".tr_clone_"+incre+" .raw_item_code").parents('.easy-autocomplete').remove();
                $(".tr_clone_"+incre+" .easy-autocomplete-container").remove();

                $(".tr_clone_"+incre+" .remove_field").removeAttr('disabled', 'false');

                var itmelm = '<input class="form-control raw_item_code" data-parent="tr_clone_'+incre+'" type="text" name="raw_item_code[]"  id="raw_item_code_'+incre+'" placeholder="Item Code">';
                var addinput = '<input class="form-control addinput" type="hidden" name="addinput" value="raw_new">';
                $(".tr_clone_"+incre+" .item_code_parent").append(itmelm);
                $(".idclone_product").append(addinput);

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

var w_raw_materials_used  = (function(){
    return {
        init: function () {

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

            $('.tr_new .w_raw_item_code').easyAutocomplete(rawItemOption);

            var incre = 0;

            $("#w_add_new_field").click(function () {

                var clone = $('.new_table .tr_new:last').clone(true);

                clone.addClass('tr_new_'+incre).removeClass('tr_new').appendTo(".new_table");

                // remove value in clone field
                $(".tr_new_"+incre+" .w_price").val(' ');
                $(".tr_new_"+incre+" .w_item_qty").val(0);
                $(".tr_new_"+incre+" .w_raw_item_id").val(' ');
                $(".tr_new_"+incre+" .w_raw_item_code").val(' ');
                $(".tr_new_"+incre+" .w_total_price").val(' ');
                // end

                $(".tr_new_"+incre+" .w_raw_item_code").removeAttr('id');
                $(".tr_new_"+incre+" .w_raw_item_code").parents('.easy-autocomplete').remove();
                $(".tr_new_"+incre+" .easy-autocomplete-container").remove();

                $(".tr_new_"+incre+" .remove_field").removeAttr('disabled', 'false');

                var itmelm = '<input class="form-control w_raw_item_code" data-parent="tr_new_'+incre+'" type="text" name="w_raw_item_code[]"  id="w_raw_item_code_'+incre+'" placeholder="Item Code">';
                var addinput = '<input class="form-control addinput" type="hidden" name="addinput" value="w_new">';
                $(".tr_new_"+incre+" .w_item_code_parent").append(itmelm);

                $(".new_table").append(addinput);

                $('#w_raw_item_code_'+incre).easyAutocomplete(rawItemOption);

                incre++;

                return false;
            });

            // End

            // this section remove add new field

            $('.new_table').on('click','.remove_field',function(){
                var remove_class = $(this).parent().parent().prop('className');

                if(remove_class != 'tr_clone'){
                    $('.new_table .'+remove_class).remove();
                }else{
                    alert('You cannot remove this row.');

                    return false ;
                }
            });


            // End
        }
    };
})();

var w_raw_code_event = (function () {
    return {
        init: function () {
            $('.new_table').on('change','.w_raw_item_code',function(){
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
                            $('.'+item_parent_class+' .w_price').val(myObj.price);
                            $('.'+item_parent_class+' .w_raw_item_id').val(myObj.id_raw_item);
                            $('.'+item_parent_class+' .w_item_qty').val('0');

                        }
                    },
                    error:function(result){
                        alert("Something is wrong.");
                    }

                });
            });
        }
    }
})();

$(document).ready(function(){
  products_details.init();
  raw_materials_used.init();
    w_raw_code_event.init();
    w_raw_materials_used.init();
});