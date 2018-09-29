function dateFormatter(dateValue){
	var caculateDate = '';
	if(dateValue != ''){
		if((typeof dateValue.split(" ")[0] != "undefined")
			&&(typeof dateValue.split(" ")[1] != "undefined")
			&&(typeof dateValue.split(" ")[2] != "undefined")
		){
			var date = dateValue.split(" ")[0];
			var time = dateValue.split(" ")[1];
			var amPm = dateValue.split(" ")[2];

		    if((typeof date.split("-")[0] != "undefined") 
		    	&&(typeof date.split("-")[1] != "undefined") 
		    	&&(typeof date.split("-")[2] != "undefined")
		    	)
		    {
		        var day = date.split("-")[0];
		        var month = date.split("-")[1];
		        var year = date.split("-")[2];
		        var mydate = new Date();
		        caculateDate = mydate.setFullYear(year, month-1, day);
		    }
		}else{
			if((typeof dateValue.split("-")[0] != "undefined") 
		    	&&(typeof dateValue.split("-")[1] != "undefined") 
		    	&&(typeof dateValue.split("-")[2] != "undefined")
		    	)
		    {
		        var day = dateValue.split("-")[0];
		        var month = dateValue.split("-")[1];
		        var year = dateValue.split("-")[2];

		        // var fulldate = year+'-'+month+'-'+day;
		        var fulldate = day+'-'+month+'-'+year;
		      
		        caculateDate = Date.parse(fulldate);
		    }
		}
	}

	return caculateDate;
}