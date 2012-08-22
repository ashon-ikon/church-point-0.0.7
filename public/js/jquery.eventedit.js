/* Default procedures to run*/
$(function(){
	$("#groupslist").validate({
		expression: "if (VAL.match(/^[0-9]+$/i)) return true; else return false;",
		message: "Please enter choose a group this event belongs under"
	});
	$("#event-title").validate({
    	expression: "if (VAL.match(/^[a-zA-Z0-9\\s\\w\\S\\W\\.\ \\_\\-]{3,}$/i)) return true; else return false;",
        message: "Please enter a title for the event"
	});
	$("#event-description").validate({
		expression: "if (VAL.match(/^[a-zA-Z0-9\\s\\w\\S\\W\\.\ \\_\\-]{6,}$/i)) return true; else return false;",
		message: "The event description appears to be empty or too little to be saved as an event"
	});

	$("#event-start-date_").validate({
    	expression: "if (VAL.match(/^(0|1)?[0-9]/[0-3]?[0-9]/2[0-9]{3} ?[0-2]?[0-9]:[0-6]?[0-9]$/i) ) return true; else return false;",
        message: "Please enter the event date format 'MM/DD/YYYY hh:mm'"
	});
	/* Date stuff */
	$( "#event-start-date" ).datetimepicker();
	$( "#event-stop-date" ).datetimepicker();
});
