/* Default procedures to run*/
$(function(){
	$("#news-title").validate({
    	expression: "if (VAL.match(/^[a-zA-Z0-9\\s\\w\\S\\W\\.\ \\_\\-]{3,}$/i)) return true; else return false;",
        message: "Please enter a title for the news"
	});
	$("#tmcenews").validate({
		expression: "if (VAL.match(/^[a-zA-Z0-9\\s\\w\\S\\W\\.\ \\_\\-]{6,}$/i)) return true; else return false;",
		message: "The news appears to be empty or too little to be saved as an news"
	});

	$("#newstags").validate({
    	expression: "if (VAL.match(/^[a-zA-Z0-9,\\w\\S\\W\\.\ \\_\\-]{4,}$/i)) return true; else return false;",
        message: "Please give some comma sperated tags for this news"
	});
});