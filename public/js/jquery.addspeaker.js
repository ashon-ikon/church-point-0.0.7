/* Default procedures to run*/
$(function(){
	$("#firstname").validate({
		expression: "if (VAL.match(/^[a-zA-Z0-9\\.\ \\_\\-]{3,}$/i)) return true; else return false;",
		message: "Please enter speaker's firstname"
	});
	
	$("#lastname").validate({
		expression: "if (VAL.match(/^[a-zA-Z0-9\\.\ \\_\\-]{3,}$/i)) return true; else return false;",
		message: "Please enter speaker's lastname"
	});
	
	$("#sermon-author").validate({
		expression: "if (VAL.match(/^([0-9]{1,}|new|\*)$/i)) return true; else return false;",
		message: "Please enter the speaker of the sermon"
	});
    
    $("#upload-image").validate({
    	expression: "if (VAL.match(/^([0-9]{1,}|new|\*)$/i)) return true; else return false;",
        message: "Please enter the speaker of the sermon"
	});
	
});