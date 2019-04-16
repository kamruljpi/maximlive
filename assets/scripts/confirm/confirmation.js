var check = (function(){
	return {
        init: function () {
			$('.deleteButton').on('click',function(){
				var confirmValue = confirm("Are you sure!");
				if (confirmValue == true) {
					return true;
				}else{
					return false;
				}
			});
		}
	};
})();

$(document).ready(function(){
	check.init();
});