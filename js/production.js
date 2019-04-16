$('#booking_advanc_search').on('click',function (ev)
{
    // displaySetup("#booking_simple_search_form", "#advance_search_form");
    $('.advance_form').removeClass('hidden');
    $('#booking_simple_search_btn').removeClass('hidden');
    $('#booking_advanc_search').hide();
    $('#booking_simple_search_form').hide();
});

$('#booking_simple_search_btn').on('click',function (ev)
{
    // displaySetup("#advance_search_form", "#booking_simple_search_form");
    $('.advance_form').addClass('hidden');
    $('#booking_simple_search_btn').addClass('hidden');
    $('#booking_advanc_search').show();
    $('#booking_simple_search_form').show();
});

$("#booking_simple_search").click(function ()
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
            EmptyValueView('.pagination', '#booking_list_tbody', "#booking_list_pagination", 9);
        }
    }
});

$("#booking_simple_search_report").click(function ()
{
    var booking_id = $('#booking_id_search').val();

    if(booking_id == ''){
        alert("The search field cannot be empty");
        return;
    }
    else
    {
        var results = ajaxFunc("/booking_report_list_by_book_id", "GET", "booking_id="+booking_id);
        if((results.responseJSON != '') && (results.responseJSON != null))
            addbookingRow(results.responseJSON, 0);
        else {
            EmptyValueView('.pagination', '#booking_list_tbody', "#booking_list_pagination", 9);
        }
    }
});

// $('#advance_search_form').on('submit',function (ev)
// {
//     ev.preventDefault();
//     var  data = $('#advance_search_form').serialize();
//     var results = ajaxFunc("/booking_list_by_search/", "POST", data);

//     if((results.responseJSON != '') && (results.responseJSON != null))
//         addRow(results.responseJSON, 0);
//     else {
//         EmptyValueView('.pagination', '#booking_list_tbody', "#booking_list_pagination", 9);
//     }
// });

$('#advance_search_form').on('submit',function (ev)
{
    ev.preventDefault();
    var  data = $('#advance_search_form').serialize();
    var results = ajaxFunc("/booking_list_book_search", "POST", data);

    if((results.responseJSON != '') && (results.responseJSON != null))
        addbookingRow(results.responseJSON, 0);
    else {
        EmptyValueView('.pagination', '#booking_list_tbody', "#booking_list_pagination", 9);
    }
});

function EmptyValueView(pagination, table, jspatioantion, colspanVal){
    $(pagination).empty();
    $(table).empty();
    $(jspatioantion).css('display','none');
    $(table).append('<tr><td colspan=" '+ 6 + colspanVal+'" style="text-align: center">Empty Value</center></td></tr>');
    // console.log(colspanVal);
}

$('#booking_reset_btn').on('click',function () {
    location.reload();
    // resetAllInputs('#booking_id_search','#advance_search_form');
})

function ajaxFunc(url, type, data)
{
    return $.ajax({
        url:baseURL+url,
        type:type,
        data:data,
        cache: false,
        async: false,
    });
}

function displaySetup(disNone, disBlock)
{
    $(disNone).css('display','none');
    $(disBlock).css('display','block');
}

function resetAllInputs(searchFld, form)
{
    $(searchFld).val('');
    $(form).each(function(){
        $(this).find(':input:text').val('');
        $("input[type='date']").val('');
    });
}

function setPagination(results, position) {
    // if(results.length > end)
    // {
    var pageNum = Math.ceil(results.length/15);
    var previous = (position-1);
    var next = (position+1);
    if(position == 1)
        previous = 1;
    if(position == pageNum)
        next = pageNum;
    $('.pagination').append('<li data-page="'+ previous +'"><span>&laquo;<span class="sr-only">(current)</span></span></li>').show();
    for (i = 1; i <= pageNum;)
    {
        $('.pagination').append('<li data-page="'+i+'">\<span>'+ i++ +'<span class="sr-only">(current)</span></span>\</li>').show();
    }
    $('.pagination').append('<li data-page="'+ next +'"><span>&raquo;<span class="sr-only">(current)</span></span></li>').show();
    // $('.pagination').append('<li><a href="http://127.0.0.1:8000/view/challan/list?page=2" rel="next">&raquo;</a></li>').show();

    $('.pagination li:nth-child('+ (position+1) +')').addClass('active');

    if(position == 1)
        $('.pagination li:first-child').addClass('disabled');
    if(position == pageNum)
        $('.pagination li:last-child').addClass('disabled');
    // }
}

function addRow(results, start)
{
    $('.pagination').empty();
    $('#booking_list_tbody').empty();
    $("#booking_list_pagination").css('display','none');

    var sl = 1;

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
        $('#booking_list_tbody').append('<tr class="booking_list_table"><td>'+sl+
            '</td><td>'+rows[i].buyer_name+
            '</td><td>'+rows[i].Company_name+
            '</td><td>'+rows[i].attention_invoice+
            '</td><td>'+rows[i].booking_order_id+
            '</td><td>'+rows[i].created_at+
            '</td><td>'+
            '</td><td>'+rows[i].booking_status+
            '</td><td>' +
                '<form action="./view/"  target="_blank">' +
                    '<input type="hidden" name="bid" value="'+ rows[i].booking_order_id+'">' +
                    '<button class="btn btn-success b1">Report</button>' +
                '</form>' +
                '<button type="button" class="btn btn-success dropdown-toggle b2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                    '<span class="caret"></span>' +
                    '<span class="sr-only">Toggle Dropdown</span>' +
                '</button>' +
                '<ul class="dropdown-menu">' +
                    '<li>' +
                        '<a href="./detailsView/'+ rows[i].booking_order_id +'">Views</a>' +
                    '</li>' +
                    '<li>' +
                        '<a href="./createIpo/' + rows[i].booking_order_id +'">IPO</a>' +
                    '</li>' +
                    '<li>' +
                        '<a href="./createMrf/' + rows[i].booking_order_id +'">MRF</a>' +
                    '</li>' +
                    '<li>' +
                        '<a href="./download/file/'+rows[i].booking_order_id+'" class="btn btn-info">Download Files</a>' +
                    '</li>' +
                '</ul>' +
            '</td></tr>');
        sl++;
    }
// <a href="./createIpo/'+rows[i].booking_order_id+
//     '" class="btn btn-info">IPO</a><a href="./createMrf/'+rows[i].booking_order_id+
//     '" class="btn btn-warning">MRF</a>
    setPagination(results, position);

    $('.pagination li').on('click',(function () {

        var begin = $(this).attr("data-page");
        addRow(results, begin-1);
    }));
}

function addbookingRow(results, start)
{   
    $('.pagination').empty();
    $('#booking_list_tbody').empty();
    $("#booking_list_pagination").css('display','none');

    var sl = 1;
    var zeroc = '0';

    var position = start+1;
    start = start*15;

    if(results.length <start+15)
        end = results.length;
    else
        end = start+15;

    var rows = $.map(results, function(value, index) {
        return [value];
    });

    var fullTotalAmount = 0;
    var book_html = '';
    for (var i = start; i < end; i++)
    {
        var itemLists = $.map(rows[i].itemLists, function(value, index) {
            return [value];
        });
        var itemListsi = 0;
        var itemListse = itemLists.length;
        for (var ij = itemListsi; ij < itemListse; ij++)
        {
            var idstrcount = (8 - itemLists[ij].job_number.toString().length);
            var jobnumber = zeroc.repeat(idstrcount)+''+itemLists[ij].job_number;
            var ilc = 1;
            var TotalAmount = 0;
            book_html += '<tr class="booking_list_table">';
            book_html += '<td><input type="hidden" name="job_id[]" value="'+jobnumber+'">'+jobnumber+'</td>';
            book_html += '<td><input type="hidden" name="buyer_name[]" value="'+rows[i].buyer_name+'">'+rows[i].buyer_name+'</td>';
            book_html += '<td><input type="hidden" name="vendor_name[]" value="'+rows[i].Company_name+'">'+rows[i].Company_name+'</td>';
            book_html += '<td><input type="hidden" name="attention_invoice[]" value="'+rows[i].attention_invoice+'">'+rows[i].attention_invoice+'</td>';
            book_html += '<td><input type="hidden" name="booking_order_id[]" value="'+rows[i].booking_order_id+'">'+rows[i].booking_order_id+'</td>';
            book_html += '<td><input type="hidden" name="po_cat_no[]" value="'+itemLists[ij].poCatNo+'">'+itemLists[ij].poCatNo+'</td>';
            book_html += '<td><input type="hidden" name="p_ids[]" value="'+((itemLists[ij].pi.length != 0)? itemLists[ij].pi[0].p_ids : '')+'">'+((itemLists[ij].pi.length != 0)? itemLists[ij].pi[0].p_ids : '')+'</td>';
            book_html += '<td><input type="hidden" name="challan_ids[]" value="'+((itemLists[ij].challan.length != 0)? itemLists[ij].challan[0].challan_ids : '')+'">'+((itemLists[ij].challan.length != 0)? itemLists[ij].challan[0].challan_ids : '')+'</td>';
            book_html += '<td><input type="hidden" name="ipo_ids[]" value="'+((itemLists[ij].ipo.length != 0)? itemLists[ij].ipo[0].ipo_ids : '')+'">'+((itemLists[ij].ipo.length != 0)? itemLists[ij].ipo[0].ipo_ids : '')+'</td>';
            book_html += '<td><input type="hidden" name="mrf_ids[]" value="'+((itemLists[ij].mrf.length != 0)? itemLists[ij].mrf[0].mrf_ids : '')+'">'+((itemLists[ij].mrf.length != 0)? itemLists[ij].mrf[0].mrf_ids : '')+'</td>';
            book_html += '<td><input type="hidden" name="order_date[]" value="'+rows[i].created_at+'">'+rows[i].created_at+'</td>';
            book_html += '<td><input type="hidden" name="requested_date[]" value="'+rows[i].shipmentDate+'">'+rows[i].shipmentDate+'</td>';
            book_html += '<td><input type="hidden" name="item_code[]" value="'+itemLists[ij].item_code+'">'+itemLists[ij].item_code+'</td>';
            book_html += '<td><input type="hidden" name="erp_code[]" value="'+itemLists[ij].erp_code+'">'+itemLists[ij].erp_code+'</td>';
            book_html += '<td><input type="hidden" name="item_size[]" value="'+itemLists[ij].item_size+'">'+itemLists[ij].item_size+'</td>';
            book_html += '<td><input type="hidden" name="item_description[]" value="'+itemLists[ij].item_description+'">'+itemLists[ij].item_description+'</td>';
            book_html += '<td><input type="hidden" name="sku[]" value="'+itemLists[ij].sku+'">'+itemLists[ij].sku+'</td>';
            book_html += '<td><input type="hidden" name="item_quantity[]" value="'+itemLists[ij].item_quantity+'">'+itemLists[ij].item_quantity+'</td>';
            book_html += '<td> <input type="hidden" name="item_price[]" value="$'+itemLists[ij].item_price+'">$'+itemLists[ij].item_price+'</td>';
            book_html += '<td> <input type="hidden" name="item_total_price[]" value="$'+Number((itemLists[ij].item_quantity*itemLists[ij].item_price).toFixed(2))+'">$'+Number((itemLists[ij].item_quantity*itemLists[ij].item_price).toFixed(2))+'</td>';
            fullTotalAmount += itemLists[ij].item_quantity*itemLists[ij].item_price;
            TotalAmount += itemLists[ij].item_quantity*itemLists[ij].item_price;
            book_html += '</tr>';
        }
        sl++;
    }
    book_html += '<tr>';
    book_html += '<td colspan="17"></td>';
    book_html += '<td colspan="2"><strong>Total Price:</strong></td>';
    book_html += '<td> <input type="hidden" name="total_price" value="'+Number((fullTotalAmount).toFixed(2))+'"><strong>$'+Number((fullTotalAmount).toFixed(2))+'</strong></td>';
    book_html += '</tr>';
    $('#booking_list_tbody').append(book_html);
    setPagination(results, position);
    $('.pagination li').on('click',(function () {
        var begin = $(this).attr("data-page");
        addbookingRow(results, begin-1);
    }));
}

// challan list Search option

$('#challan_advanc_search').on('click',function (ev)
{
    displaySetup("#challan_simple_search_form", "#challan_advance_search_form");
});

$('#challan_simple_search_btn').on('click',function (ev)
{
    displaySetup("#challan_advance_search_form", "#challan_simple_search_form");
});

$("#challan_simple_search").click(function ()
{
    var challan_id = $('#challan_id_search').val();

    if(challan_id == ''){
        alert("The search field cannot be empty");
        return;
    }
    else
    {
        var results = ajaxFunc("/challan_list_by_challan_id", "GET", "challan_id="+challan_id);
        if((results.responseJSON != '') && (results.responseJSON != null))
            addRowInChallanList(results.responseJSON, 0);
        else {
            EmptyValueView('.pagination', '#challan_list_tbody', "#challan_list_pagination", 5);
            // alert("No data  found");
        }
    }
});

$('#challan_advance_search_form').on('submit',function (ev)
{
    ev.preventDefault();
    var  data = $('#challan_advance_search_form').serialize();
    var results = ajaxFunc("/challan_list_by_search", "POST", data);

    if((results.responseJSON != '') && (results.responseJSON != null))
        addRowInChallanList(results.responseJSON, 0);
    else {
        EmptyValueView('.pagination', '#challan_list_tbody', "#challan_list_pagination", 5);
        // alert("No data  found");
    }
});

function addRowInChallanList(results, start)
{
    $('.pagination').empty();
    $('#challan_list_tbody').empty();
    $("#challan_list_pagination").css('display','none');

    var getUrl = document.URL;
    var setUrl = getUrl.replace("view/challan/list","challan/list/action/task")

    var sl = 1;

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
        $('#challan_list_tbody').append('<tr class="challan_list_table"><td>'+sl+
                '</td><td>'+rows[i].checking_id+
                '</td><td>'+rows[i].challan_id+
                '</td><td>'+rows[i].created_at+
                '</td><td><form action='+setUrl+' target="_blank"><input type="hidden" name="cid" value="'+ rows[i].challan_id+
                '"><input type="hidden" name="bid" value="'+ rows[i].checking_id+
                '"><button class="btn btn-success">View</button></form></td></tr>');
            sl++;
    }

    setPagination(results, position);

    $('.pagination li').on('click',(function () {

        var begin = $(this).attr("data-page");
        addRowInChallanList(results, begin-1);

    }));
}

$('#challan_reset_btn').on('click',function () {
    location.reload();
    // resetAllInputs('#challan_id_search','#challan_advance_search_form');
})

// MRF search List

$('#mrf_advanc_search').on('click',function (ev)
{
    displaySetup("#mrf_simple_search_form", "#mrf_advance_search_form");
});

$('#mrf_simple_search_btn').on('click',function (ev)
{
    displaySetup("#mrf_advance_search_form", "#mrf_simple_search_form");
});

$("#mrf_simple_search").click(function ()
{
    var mrf_id = $('#mrf_id_search').val();

    if(mrf_id == ''){
        alert("The search field cannot be empty");
        return;
    }
    else
    {
        var results = ajaxFunc("/mrf_list_by_mrf_id", "GET", "mrf_id="+mrf_id);
        if((results.responseJSON != '') && (results.responseJSON != null))
            addRowInMrfanList(results.responseJSON, 0);
        else {
            EmptyValueView('.pagination', '#mrf_list_tbody', "#mrf_list_pagination", 6);
        }
    }
});

$('#mrf_advance_search_form').on('submit',function (ev)
{
    ev.preventDefault();
    var  data = $('#mrf_advance_search_form').serialize();
    var results = ajaxFunc("/mrf_list_by_search", "POST", data);

    if((results.responseJSON != '') && (results.responseJSON != null))
        addRowInMrfanList(results.responseJSON, 0);
    else {
        EmptyValueView('.pagination', '#mrf_list_tbody', "#mrf_list_pagination", 6);
    }
});

function addRowInMrfanList(results, start)
{
    $('.pagination').empty();
    $('#mrf_list_tbody').empty();
    $('.mrf_list_table').remove();
    $("#mrf_list_pagination").css('display','none');

    var getUrl = document.URL;
    var setUrl = getUrl.replace("mrf/list/list","task/mrf/task/list")
    var sl = 1;

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
        $('#mrf_list_tbody').append('<tr class="mrf_list_table"><td>'+sl+
            '</td><td>'+rows[i].booking_order_id+
            '</td><td>'+rows[i].mrf_id+
            '</td><td>'+rows[i].created_at+
            '</td><td>'+rows[i].shipmentDate+
            '</td><td><form action='+setUrl+' target="_blank"><input type="hidden" name="mid" value="'+ rows[i].mrf_id+
            '"><input type="hidden" name="bid" value="'+ rows[i].booking_order_id+
            '"><button class="btn btn-success">View</button></form></td></tr>');
        sl++;
    }

    setPagination(results, position);

    $('.pagination li').on('click',(function () {

        // $('.pagination li').removeClass('active');
        // $(this).addClass('active');
        var begin = $(this).attr("data-page");
        addRowInMrfanList(results, begin-1);

    }));
}

$('#mrf_reset_btn').on('click',function () {
    location.reload();
})

$('#purchase_order_list_search_form').on('submit', function (ev) {

    ev.preventDefault();
    var  data = $('#purchase_order_list_search_form').serialize();
    console.log(data);
    var supplier_id = $('#supplier_id').val();
    if (supplier_id != '')
    {
        var results = ajaxFunc("/po_list_by_search", "POST", data);

        if((results.responseJSON != '') && (results.responseJSON != null))
        {
            // console.log(results.responseJSON);
            addRowInPOList(results.responseJSON[1]/*, 0*/);
            $('.polistResetBtnAndNo').css('display','block');
            $('.poTableList').css('display','block');
            $('.PONoInList').text(' PO no: '+results.responseJSON[0]);
            $('.report_all_data').val(results.responseJSON[0]);
        }
        else {
            $('.polistResetBtnAndNo').css('display','none');
            $('.poTableList').css('display','none');
            EmptyValueView('.pagination', '#po_list_tbody', "", 12);
        }
    }
    else {
        alert("Please Select any Supplier");
    }
});

function addRowInPOList(results/*, start*/)
{
    $('.pagination').empty();
    $('#po_list_tbody').empty();
    $('.po_list_table').remove();

    var getUrl = document.URL;
    var setUrl = getUrl.replace("mrf/list/list","task/mrf/task/list")
    var sl = 1;

    var rows = $.map(results, function(value, index) {
        return [value];
    });

    var finalTotalQnty = 0;
    var finalTotalAmnt = 0;
    var defaultValue = '';
    var defaultNumber = 0.00;

    // var position = start+1;
    // start = start*15;
    //
    // if(results.length <start+15)
    //     end = results.length;
    // else
    //     end = start+15;
    //
    // var rows = $.map(results, function(value, index) {
    //     return [value];
    // });

    if(rows.length == 0)
        EmptyValueView('.pagination', '#po_list_tbody', "", 12);

    for (var i = 0; i < rows.length; i++)
    {
        if(rows[i].item_size != null)
            var sizes = rows[i].item_size.split(',');
        else
            var sizes = [];

        if(rows[i].gmts_color != null)
            var colors = rows[i].gmts_color.split(',');
        else
            var colors = [];

        if(rows[i].item_quantity != null)
            var quantities = rows[i].item_quantity.split(',');
        else
            var quantities = [];

        if(rows[i].supplier_price != null)
            var unit_prices = rows[i].supplier_price.split(',');
        else
            var unit_prices = rows[i].item_price.split(',');

        if(rows[i].erp_code != null)
            var erp_codes = rows[i].erp_code.split(',');
        else
            var erp_codes = [];

        if(rows[i].item_code != null)
            var item_codes = rows[i].item_code.split(',');
        else
            var item_codes = [];

        if(rows[i].mrf_id != null)
            var mrf_ids = rows[i].mrf_id.split(',');
        else
            var mrf_ids = [];

        var spanLength = quantities.length;

        $('#po_list_tbody').append('<tr class="po_list_table"><td>'+sl+
            '</b></td><td rowspan="'+spanLength+'" style="vertical-align: middle; horiz-align: middle;" class="booking_order_id_'+i+'_0"><b>'+((rows[i].booking_order_id)? rows[i].booking_order_id:defaultValue)+
            '</b></td><td rowspan="'+spanLength+'" style="vertical-align: middle; horiz-align: middle;" class="shipmentDate_'+i+'_0"><b>'+((rows[i].shipmentDate)? rows[i].shipmentDate:defaultValue)+
            '</td><td class="erp_code_'+i+'_0">'+((erp_codes[0])? erp_codes[0]:defaultValue)+
            '</td><td class="item_code_'+i+'_0">'+((item_codes[0])? item_codes[0]:defaultValue)+
            '</td><td class="sizes_'+i+'_0">'+((sizes[0])? sizes[0]:defaultValue)+
            '</td><td class="matarial_'+i+'_0">'+((rows[i].matarial)? rows[i].matarial:defaultValue)+
            '</td><td class="gmts_color_'+i+'_0">'+((colors[0])? colors[0]:defaultValue)+
            '</td><td class="unit_'+i+'_0">'+''+
            '</td><td class="quantities_'+i+'_0">'+((quantities[0])? quantities[0]:defaultNumber)+
            '</td><td class="unitPrice_'+i+'_0">$'+((unit_prices[0])? unit_prices[0]:defaultNumber)+
            '</td><td class="totalPrice_'+i+'_0">$'+((((quantities[0])? quantities[0]:defaultNumber)*((unit_prices[0]? unit_prices[0]:defaultNumber)))).toFixed(2)+
            '</td><td class="hidden mrf_id_'+i+'_0">'+((mrf_ids[0])? mrf_ids[0]:defaultValue)+
            '</td></tr>');
        sl++;

        var totalQnty = parseFloat(quantities[0]);
        var totalAmount = parseFloat(quantities[0]*unit_prices[0]);

        for (var j = 1; j < spanLength; j++)
        {
            var qnt = (quantities[j])? quantities[j]:defaultNumber;
            var amt = (unit_prices[j])? unit_prices[j]:defaultNumber;
            totalQnty += parseFloat((quantities[j])? quantities[j]:defaultNumber);
            totalAmount += parseFloat(qnt*amt);

            $('#po_list_tbody').append('<tr class="po_list_table"><td>'+sl+
                '</td><td class="erp_code_'+i+'_'+j+'">'+((erp_codes[j])? erp_codes[j]:defaultValue)+
                '</td><td class="item_code_'+i+'_'+j+'">'+((item_codes[j])? item_codes[j]:defaultValue)+
                '</td><td class="sizes_'+i+'_'+j+'">'+((sizes[j])? sizes[j]:defaultValue)+
                '</td><td class="matarial_'+i+'_'+j+'">'+((rows[i].matarial)? rows[i].matarial:defaultValue)+
                '</td><td class="gmts_color_'+i+'_'+j+'">'+((colors[j])? colors[j]:defaultValue)+
                '</td><td class="unit_'+i+'_'+j+'">'+''+
                '</td><td class="quantities_'+i+'_'+j+'">'+((quantities[j])? quantities[j]:defaultNumber)+
                '</td><td class="unitPrice_'+i+'_'+j+'">$'+((unit_prices[j])? unit_prices[j]:defaultNumber)+
                '</td><td class="totalPrice_'+i+'_'+j+'">$'+(qnt*amt).toFixed(2)+
                '</td><td class="hidden mrf_id_'+i+'_'+j+'">'+((mrf_ids[j])? mrf_ids[j]:defaultValue)+
                '</td></tr>');
            sl++;
        }
        $('#po_list_tbody').append('<tr class="po_list_table"><td colspan="9" style="vertical-align: middle;"><b>Total</b></td><td class="totalQnty_'+i+'_l"><b>'+totalQnty.toFixed(2)+
            '</b></td><td class="totalUnitPirce_'+i+'_l"><b>'+''+
            '</b></td><td class="totalAmount_'+i+'_l"><b>$'+totalAmount.toFixed(2)+
            '</b></td></tr>');

        finalTotalQnty += totalQnty;
        finalTotalAmnt += totalAmount;
    }

    // $('#save_purcahe_order_form').on('submit', function (ev) {
    //     alert('submit');
    // });

    $('#po_list_tbody').append('<tr class="po_list_table"><td colspan="9" style="vertical-align: middle;"><b> Final Total</b></td><td class="totalQnty_'+i+'_l"><b>'+finalTotalQnty.toFixed(2)+
        '</b></td><td class="totalUnitPirce_'+i+'_l"><b>'+''+
        '</b></td><td class="totalAmount_'+i+'_l"><b>$'+finalTotalAmnt.toFixed(2)+
        '</b></td></tr>');
}

$('.save_purcahe_order').on('click', function (ev) {
    var poId = $('.report_all_data').val();
    // alert($('.report_all_data').val());
    var it1 = 0;
    var po_no = $('.PONoInList').html().replace('PO no: ','');
    var datas = [['po_no','booking_order_no', 'shipment_date', 'erp_code', 'item_code', 'size', 'material', 'color', 'unit', 'qnty', 'unit_price', 'total_amnt']];
    while (true)
    {
        if($('.quantities_'+it1+'_0').length > 0)
        {
            var it2 = 0;
            var booking_order_id = $('.booking_order_id_'+it1+'_0').html().replace('<b>','').replace('</b>','');
            var shipmentDate = $('.shipmentDate_'+it1+'_0').html().replace('<b>','').replace('</b>','');

            while (true)
            {
                if ($('.quantities_'+it1+'_'+it2).length > 0)
                {
                    datas.push([po_no,
                        booking_order_id,
                        shipmentDate,
                        $('.erp_code_'+it1+'_'+it2).html(),
                        $('.item_code_'+it1+'_'+it2).html(),
                        $('.sizes_'+it1+'_'+it2).html(),
                        $('.matarial_'+it1+'_'+it2).html(),
                        $('.gmts_color_'+it1+'_'+it2).html(),
                        $('.unit_'+it1+'_'+it2).html(),
                        $('.quantities_'+it1+'_'+it2).html(),
                        $('.unitPrice_'+it1+'_'+it2).html(),
                        $('.totalPrice_'+it1+'_'+it2).html(),
                        $('.mrf_id_'+it1+'_'+it2).html()]);
                    it2++;
                }
                else
                    break;
            }
            it1++;
        }
        else
            break;
    }
    // console.log(datas);
    var poDatajs = JSON.stringify(datas);
    var saveData = ajaxFunc("/save_purcahse_order", "GET", "data="+poDatajs);

    // console.log(saveData.responseText);

    var getUrl = document.URL;
    var Supplier_id = $('#supplier_id').val();
    var data = po_no+','+Supplier_id;
    var setUrl = getUrl.replace("/list","/report?data="+data);
    window.location.assign(setUrl);
});


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
        book_html += '<form action="'+baseURL+'/view"  target="_blank">';
        book_html += '<input type="hidden" name="bid" value="'+ rows[i].booking_order_id+'">';
        book_html += '<button class="btn btn-success b1">Report</button>';
        book_html += '</form>';
        book_html += '<button type="button" class="btn btn-success dropdown-toggle b2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        book_html += '<span class="caret"></span>';
        book_html += '<span class="sr-only">Toggle Dropdown</span>';
        book_html += '</button>';
        book_html += '<ul class="dropdown-menu" style="left:-45px !important;">';
        book_html += '<li><a href="detailsView/'+ rows[i].booking_order_id +'">Views</a></li>';
        // book_html += ;
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
