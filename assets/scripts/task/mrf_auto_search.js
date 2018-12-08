var auto_check_mrf = (function(){
  return {
        init: function () {

          var mrf_options = {

            url: function(phrase) {
              return baseURL+"/get/mrf/id";
            },

            getValue: function(element) {
              return element.mrf_id;
            },

            list: {
                match: {
                    enabled: true
                },
                onChooseEvent: function(t){
                    var taskType = $('#taskType').val();

                    if (taskType == 'MRF'){
                      $('#mrfIdList').append('<div class="challan_item"><span>'+t.val()+'</span><span class="challan_list_rmv"> x</span></div>');
                      $('#hiddenMrfIdList').val($('#hiddenMrfIdList').val()+ t.val() +' , ');
                      $("#mrfId").val("").focus();
                      $('#mrfIdList').removeClass('hidden');
                    }
                }
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

          $("#mrfId").easyAutocomplete(mrf_options);
          $(".easy-autocomplete").css("width","100%");

          $("#mrfIdList").on("click",".challan_list_rmv", function(){
              var order_item = $(this).parent(".challan_item").text();
              $(this).parent(".challan_item").remove();
              var order_item_1 = order_item.replace('x', ",");
              var order_items = $('#hiddenMrfIdList').val();
              var order_itemss =  order_items.replace(order_item_1, " ");
              $('#hiddenMrfIdList').val(" ");
              $('#hiddenMrfIdList').val(order_itemss);
          })
    }
  };
})();

$(document).ready(function(){
  auto_check_mrf.init();
});