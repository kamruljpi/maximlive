var opening = (function() {
    return {
        init: function () {
            var selected = $('select[name=zone]').val();

            $(".product_entry").on("change", "#location", function () {
                var selected = $(this).val();
                $.ajax({
                    url:baseURL+"/zone/details",
                    type:"GET",
                    data:{selected},
                    datatype: 'json',
                    cache: false,
                    async: false,
                success:function(result){
                    var myObj3 = JSON.parse(result);
                    var i;
                    for (i = 0; i < myObj3.length; i++) {
                        $("#zone ").html('<option value="'+myObj3[i].zone_id+'">'+myObj3[i].zone_name+'</option>');
                    }
                    if(myObj3 != null) {

                    } else
                            alert("Something went wrong.");
                    },

                    error:function(result){
                        alert("ERROR > "+result);
                    }
                });

            });
        }
    }
})();

$(document).ready(function(){
    opening.init();
});