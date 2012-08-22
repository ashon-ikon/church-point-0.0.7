/* Default procedures to run*/
$(function(){
	$("#sermon-title").validate({
		expression: "if (VAL.match(/^[a-zA-Z0-9\\?\\.\ \\_\\-]{3,}$/i)) return true; else return false;",
		message: "Please enter a title for the sermon"
	});
    
    $("#sermon-author").validate({
    	expression: "if (VAL.match(/^([0-9]{1,}|new|\*)$/i)) return true; else return false;",
        message: "Please enter the speaker of the sermon"
	});
	
	/* Date stuff */
	$( "#sermon-date" ).datetimepicker();
});