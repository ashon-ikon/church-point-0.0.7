/* Default procedures to run*/
$(function(){
	$("#article-title").validate({
    	expression: "if (VAL.match(/^[a-zA-Z0-9\\s\\w\\S\\W\\.\ \\_\\-]{3,}$/i)) return true; else return false;",
        message: "Please enter a title for the article"
	});
	$("#tmcearticle").validate({
		expression: "if (VAL.match(/^[a-zA-Z0-9\\s\\w\\S\\W\\.\ \\_\\-]{6,}$/i)) return true; else return false;",
		message: "The article appears to be empty or too little to be saved as an article"
	});

	$("#articletags").validate({
    	expression: "if (VAL.match(/^[a-zA-Z0-9,\\w\\S\\W\\.\ \\_\\-]{4,}$/i)) return true; else return false;",
        message: "Please give some comma sperated tags for this article"
	});
});