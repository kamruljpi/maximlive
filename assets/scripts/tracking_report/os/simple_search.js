var simple_search = (function(){
	return {
		init: function(){
			$("#os_simple_search").click(function () {
                var mrf_id = $('#os_id_search').val();
                if(mrf_id == ''){
                    alert("The search field cannot be empty");
                    return;
                } else {
                    var results = ajaxFunc("/os_tracking_report", "GET", "mrf_id="+mrf_id);

                    if((results.responseJSON != '') && (results.responseJSON != null)){
                        addPlanningReportRow(results.responseJSON, 0);
                    } else {
                        EmptyValueView('.pagination', '#booking_list_tbody', "#booking_list_pagination", 9); // this function use js/production.js
                    }
                }
            });
		}
	}
})();

function addPlanningReportRow(results, start){
    $('.pagination').empty();
    $('#booking_list_tbody').empty();
    $("#booking_list_pagination").css('display','none');

    var sl = 1;
    var book_html = '';
    var zeroc = '0';
    var total_qty = 0;
    var position = start+1;
    start = start*15;

    if(results.length <start+15)
        end = results.length;
    else
        end = start+15;

    var rows = $.map(results, function(value, index) {
        return [value];
    });

    for (var i = start; i < end; i++)
    {
        console.log("Data"+rows[0].job_id);

            var idstrcount = (8 - rows[i].job_id.toString().length);
            var job_id = zeroc.repeat(idstrcount)+''+rows[i].job_id;
            total_qty += parseInt(rows[i].mrf_quantity);

            book_html += '<tr class="booking_list_table">';
            book_html += '<td><input type="hidden" name="job_id[]" value="'+job_id+'">'+job_id+'</td>';
            book_html += '<td><input type="hidden" name="booking_order_id[]" value="'+rows[i].booking_order_id+'">'+rows[i].booking_order_id+'</td>';
            book_html += '<td><input type="hidden" name="mrf_ids[]" value="'+rows[i].mrf_id+'">'+rows[i].booking_order_id+'</td>';
            book_html += '<td><input type="hidden" name="item_code[]" value="'+rows[i].item_code+'">'+rows[i].item_code+'</td>';
            book_html += '<td><input type="hidden" name="erp_code[]" value="'+rows[i].erp_code+'">'+rows[i].erp_code+'</td>';
            book_html += '<td><input type="hidden" name="item_size[]" value="'+rows[i].item_size+'">'+rows[i].item_size+'</td>';
            book_html += '<td><input type="hidden" name="item_description[]" value="'+rows[i].item_description+'">'+rows[i].item_description+'</td>';
            book_html += '<td><input type="hidden" name="material[]" value="'+rows[i].material+'">'+rows[i].material+'</td>';
            book_html += '<td><input type="hidden" name="order_date[]" value="'+rows[i].created_at+'">'+rows[i].created_at+'</td>';
            book_html += '<td><input type="hidden" name="requested_date[]" value="'+rows[i].shipmentDate+'">'+rows[i].shipmentDate+'</td>';
            book_html += '<td><input type="hidden" name="mrf_status[]" value="'+rows[i].mrf_status+'">'+rows[i].mrf_status+'</td>';
            book_html += '<td><input type="hidden" name="item_quantity[]" value="'+rows[i].mrf_quantity+'">'+rows[i].mrf_quantity+'</td>';
            book_html += '</tr>';

        sl++;
    }
    book_html += '<tr>';
    book_html += '<td colspan="9"></td>';
    book_html += '<td colspan="2"><strong>Total Qty:</strong></td>';
    book_html += '<td><strong><input type="hidden" name="total_qty[]" value="'+total_qty+'">'+total_qty+'</strong></td>';
    book_html += '</tr>';
    $('#booking_list_tbody').append(book_html);
    setPagination(results, position);

    $('.pagination li').on('click',(function () {

        var begin = $(this).attr("data-page");
        addBookingListRow(results, begin-1);
    }));
}

$(document).ready(function(){
	simple_search.init();
});
