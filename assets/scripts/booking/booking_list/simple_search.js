var simple_search = (function(){
	return {
		init: function(){
			$("#booking_list_simple_search").click(function ()
			{
			    var booking_id = $('#booking_id_search').val();

			    if(booking_id == ''){
			        alert("The search field cannot be empty");
			        return;
			    }
			    else
			    {
			        var results = ajaxFunc("/booking_list_by_booking_id", "GET", "booking_id="+booking_id);
			        if((results.responseJSON != '') && (results.responseJSON != null))
			            addBookingListRow(results.responseJSON, 0);
			        else {
			            EmptyValueView('.pagination', '#booking_list_tbody', "#booking_list_pagination", 9); // this function use js/production.js
			        }
			    }
			});
		}
	}
})();

function addBookingListRow(results, start){
    $('.pagination').empty();
    $('#booking_list_tbody').empty();
    $("#booking_list_pagination").css('display','none');

    var sl = 1;
    var book_html = '';
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
        book_html += '<tr class="booking_list_table">';
        book_html += '<td>'+sl+'</td>';            
        book_html += '<td>'+rows[i].buyer_name+'</td>';        
        book_html += '<td>'+rows[i].Company_name+'</td>';        
        book_html += '<td>'+rows[i].attention_invoice+'</td>';       
        book_html += '<td>'+rows[i].booking_order_id+'</td>';    
        book_html += '<td>'+((rows[i].po != null)? ((rows[i].po.ipo_id !=null)? rows[i].po.ipo_id :'') : '')+'</td>';  
        book_html += '<td>'+rows[i].bookingDetails.po_cat+'</td>';  
        book_html += '<td>'+rows[i].created_at+'</td>';
        book_html += '<td>'+rows[i].shipmentDate+'</td>';
        book_html += '<td><a id="popoverOption" class="btn popoverOption" href="#"  rel="popover" data-placement="top" data-original-title="" style="color:black;">'+rows[i].booking_status+'</a>';
        book_html += '<div class="popper-content hide">';
        book_html += '<label>Booking Prepared by: ';
        book_html += ((rows[i].booking != null)?((rows[i].booking.first_name ==null)?'':rows[i].booking.first_name)+' '+((rows[i].booking.last_name==null)?'':rows[i].booking.last_name)+' '+((rows[i].created_at ==null)?'':'( '+rows[i].created_at)+' )': '');
        book_html += '</label><br>';
        book_html += '<label>Booking Accepted by: ';
        book_html += ((rows[i].accepted != null)?((rows[i].accepted.first_name ==null)?'':rows[i].accepted.first_name)+' '+((rows[i].accepted.last_name==null)?'':rows[i].accepted.last_name)+' '+((rows[i].accepted_date_at ==null)?'':'( '+rows[i].accepted_date_at)+' )': '');
        book_html += '</label><br>';
        book_html += '<label>MRF Issue by: ';
        book_html += ((rows[i].mrf != null)?((rows[i].mrf.first_name ==null)?'':rows[i].mrf.first_name)+' '+((rows[i].mrf.last_name==null)?'':rows[i].mrf.last_name)+' '+((rows[i].mrf.created_at ==null)?'':'( '+rows[i].mrf.created_at)+' )': '');
        book_html += '</label><br>';
        book_html += '<label>PO Issue by: ';
        book_html += ((rows[i].ipo != null)?((rows[i].ipo.first_name ==null)?'':rows[i].ipo.first_name)+' '+((rows[i].ipo.last_name==null)?'':rows[i].ipo.last_name)+' '+((rows[i].ipo.created_at ==null)?'':'( '+rows[i].ipo.created_at)+' )': '');
        book_html += '</label><br>';        
        book_html += '</div>';
        book_html += '</td>';
        book_html += '<td width="12%">';
        book_html += '<div class="btn-group">';
        book_html += '<form action="'+baseURL+'/booking/list/view"  target="_blank">';
        book_html += '<input type="hidden" name="bid" value="'+ rows[i].booking_order_id+'">';
        book_html += '<button class="btn btn-success b1">Report</button>';
        book_html += '</form>';
        book_html += '<button type="button" class="btn btn-success dropdown-toggle b2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        book_html += '<span class="caret"></span>';
        book_html += '<span class="sr-only">Toggle Dropdown</span>';
        book_html += '</button>';
        book_html += '<ul class="dropdown-menu" style="left:-45px !important;">';
        book_html += '<li><a href="detailsView/'+ rows[i].booking_order_id +'">Views</a></li>';
        book_html += ((typeof(rows[i].booking_status) != "undefined" && rows[i].booking_status !=null && rows[i].booking_status != "Process") ? '<li><a href="'+baseURL+'/booking/details/cancel/'+rows[i].booking_order_id+'" class="deleteButton">Cancel</a></li>' :'');
        book_html += '<li><a href="download/file/'+rows[i].booking_order_id+'" class="btn btn-info">Download Files</a></li>';
        book_html += '</ul>';        
        book_html += '</div>';
        book_html += '</td>';
        book_html += '</tr>';
        sl++;
    }
    $('#booking_list_tbody').append(book_html);
    setPagination(results, position);

    $('.pagination li').on('click',(function () {

        var begin = $(this).attr("data-page");
        addBookingListRow(results, begin-1);
    }));
    $('.popoverOption').popover({
        trigger: "hover",
        container: 'body',
        html: true,
        content: function () {
            return $(this).next('.popper-content').html();
        }
    });

    $('.deleteButton').on('click',function(){
                var confirmValue = confirm("Are you sure!");
                if (confirmValue == true) {
                    return true;
                }else{
                    return false;
                }
            });
}

$(document).ready(function(){
	simple_search.init();
});