/* Default procedures to run*/
$(function(){
	$("#image-upload-element").change(function(){
	});
	
	$("#album-name").validate({
		expression: "if (VAL.match(/^[0-9]{1,}$/i)) return true; else return false;",
		message: "Please select an Album"
	});
	
	$("#image-upload-element1").validate({
    	expression: "if (VAL.length > 0) return true; else return false;",
        message: "Please select an image"
	});
	
});