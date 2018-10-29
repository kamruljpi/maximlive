var simple_search = (function(){
	return {
		init: function(){
			$("#planning_simple_search").click(function () {
                var booking_id = $('#booking_id_search').val();
                if(booking_id == ''){
                    alert("The search field cannot be empty");
                    return;
                } else {
                    var results = ajaxFunc("/booking_report_list_by_book_id", "GET", "booking_id="+booking_id);

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
        var itemLists = $.map(rows[i].itemLists, function(value, index) {
            return [value];
        });
        var itemListsi = 0;
        var itemListse = itemLists.length;
        for (var ij = itemListsi; ij < itemListse; ij++) {

            var idstrcount = (8 - itemLists[ij].job_number.toString().length);
            var jobnumber = zeroc.repeat(idstrcount)+''+itemLists[ij].job_number;
            // total_qty += itemLists[ij].item_quantity;

            book_html += '<tr class="booking_list_table">';
            book_html += '<td>'+jobnumber+'</td>';
            book_html += '<td>'+rows[i].buyer_name+'</td>';
            book_html += '<td>'+rows[i].Company_name+'</td>';
            book_html += '<td>'+rows[i].attention_invoice+'</td>';
            book_html += '<td>'+rows[i].booking_order_id+'</td>';
            book_html += '<td>'+itemLists[ij].poCatNo+'</td>';
            // book_html += '<td>'+((itemLists[ij].pi.length != 0)? itemLists[ij].pi[0].p_ids : '')+'</td>';
            book_html += '<td>'+((itemLists[ij].challan.length != 0)? itemLists[ij].challan[0].challan_ids : '')+'</td>';
            book_html += '<td>'+((itemLists[ij].ipo.length != 0)? itemLists[ij].ipo[0].ipo_ids : '')+'</td>';
            book_html += '<td>'+((itemLists[ij].mrf.length != 0)? itemLists[ij].mrf[0].mrf_ids : '')+'</td>';
            book_html += '<td>'+rows[i].created_at+'</td>';
            book_html += '<td>'+rows[i].shipmentDate+'</td>';
            book_html += '<td>'+itemLists[ij].item_code+'</td>';
            book_html += '<td>'+itemLists[ij].erp_code+'</td>';
            book_html += '<td>'+itemLists[ij].item_size+'</td>';
            book_html += '<td>'+itemLists[ij].item_description+'</td>';
            book_html += '<td>'+itemLists[ij].item_quantity+'</td>';
            book_html += '</tr>';
        }
        sl++;
    }
    book_html += '<tr>';
    book_html += '<td colspan="13"></td>';
    book_html += '<td colspan="2"><strong>Total Qty:</strong></td>';
    book_html += '<td><strong>'+total_qty+'</strong></td>';
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