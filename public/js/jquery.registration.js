$(function(){
	$("#firstname").validate({
    	expression: "if (VAL.match(/^[a-zA-Z0-9\\.\ \\_\\-]{3,40}$/i)) return true; else return false;",
        message: "Firstname should be at least 3 characters long and not more than 40 characters"
	});
	$("#firstname").validate({
    	expression: "if (VAL.length > 0) return true; else return false;",
        message: "Must contain a value"
	});
	$("#lastname").validate({
		expression: "if (VAL.match(/^[a-zA-Z0-9'\\.\ \\_\\-]{3,40}$/i)) return true; else return false;",
		message: "Lastname should be at least 3 characters long and not mroe than 40 characters"
	});
    $("#gender").validate({
    	expression: "if (VAL.match(/^[12]$/)) return true; else return false;",
    	message: "Should be a valid gender",
    });
    
	$("#ValidNumber").validate({
    	expression: "if (!isNaN(VAL) && VAL) return true; else return false;",
        message: "Should be a number"
	});
    $("#remail").validate({
     	expression: "if (VAL.match(/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$/i)) return true; else return false;",
        message: "Should be a valid Email id"
	});
    $("#password1").validate({
    	expression: "if (VAL.match(/^(?=.*\\d)(?=.*[a-z\\p{L&}\\p{Nd}])(?=.*[A-Z\\p{L&}\\p{Nd}]).+$/i)) return true; else return false;",
    	message: "Password must contain at least:<br />1 Lowercase Character [a - z]<br />1 Uppercase Character [A - Z]<br />1 Number"
    });

    $("#password1").validate({
    	expression: "if (VAL.match(/^(?=.*[\\w\\s]).{8,16}$/i)) return true; else return false;",
    	message: "Should be between 8 ~ 16 characters"
    });
    $("#password2").validate({
    	expression: "if (VAL == ($('#password1').val()) ) return true; else return false;",
    	message: "Password and Confirm password mismatched"
    });
    
    $("#password2").validate({
     	expression: "if (VAL.match(/^(?=.*[\\w\\s]).{8,16}$/i)) return true; else return false;",
        message: "Should be between 8 ~ 16 characters"
	});    
    $("#mobile").validate({
    	expression: "if (VAL.match(/^\\(?([0-9+]{2,4})\\)?[-. ]?([0-9]{3,})[-. ]?([0-9]{4,})$/i)) return true; else return false;",
    	message: "Should be a valid Mobile Number"
    });
    
    $("#day").validate({
    	expression: "if (VAL.match(/^[0-9]{1,2}$/)) return true; else return false;",
    	message: "Should be a valid date",
    	placeholder: "dateerror"
    });
    
    $("#month").validate({
    	expression: "if (VAL.match(/^[1-9]{1,2}$/)) return true; else return false;",
    	message: "Should be a valid date",
    	placeholder: "dateerror"
    });
    
    $("#year").validate({
		expression: "if (VAL.match(/^(19|20)[0-9]{2}$/)) return true; else return false;",
        message: "Should be a valid date",
        placeholder: "dateerror"
	});
    
    $("#address1").validate({
		expression: "if (VAL.match(/^[a-zA-Z0-9\\s\, \#'\\.\\_\\-]{10,60}$/i)) return true; else return false;",
		message: "Should be at least 10 characters long"
	});
	/*$("#address2").validate({
		expression: "if (VAL.match(/^[a-zA-Z0-9 '\\.\\_\\-]{10,60}$/i)) return true; else return false;",
		message: "Should be at least 10 characters long"
	});*/
    $("#town").validate({
    	expression: "if (VAL.match(/^[a-zA-Z0-9 '\\.\\_\\-]{3,60}$/i)) return true; else return false;",
    	message: "Should be at least 3 characters long"
    });
    
    $("#state").validate({
    	expression: "if (VAL.match(/^[0-9]{1,}$/i)) return true; else return false;",
    	message: "Please select a state"
    });
    
	$("#country").validate({
    	expression: "if (VAL.match(/^[0-9]{1,}$/i)) return true; else return false;",
        message: "Please select a Country"
	});
	
	$('#country').change(function(){
		
	});
	
});