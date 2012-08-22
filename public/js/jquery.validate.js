/**
 * @author GeekTantra
 * @date 20 September 2009
 */
(function($){
    var ValidationErrors = new Array();
    $.fn.clearvalidation = function (){
    	options = $.extend({
        	extra: "",
            expression: "return true;",
            message: "",
            placeholder:"",
            error_class: "validationerror",
            error_tag: "p",
            error_field_class: "ErrorField",
            live: true
        }, options);
    	$(this).next('.' + options['error_class']).fadeOut("fast", function(){
            $(this).remove();
         });
    };
    $.fn.validate = function(options){
        options = $.extend({
        	extra: "",
            expression: "return true;",
            message: "",
            placeholder:"",
            error_class: "validationerror",
            error_tag: "p",
            error_field_class: "ErrorField",
            live: true
        }, options);
        var SelfID = $(this).attr("id");
        var unix_time = new Date();
        unix_time = parseInt(unix_time.getTime() / 1000);
        if (!$(this).parents('form:first').attr("id")) {
            $(this).parents('form:first').attr("id", "Form_" + unix_time);
        }
        var FormID = $(this).parents('form:first').attr("id");
        if (!((typeof(ValidationErrors[FormID]) == 'object') && (ValidationErrors[FormID] instanceof Array))) {
            ValidationErrors[FormID] = new Array();
        }
        if (options['live']) {
            if ($(this).find('input').length > 0) {
                $(this).find('input').bind('blur', function(){
                    if (validate_field("#" + SelfID, options)) {
                        if (options.callback_success) 
                            options.callback_success(this);
                    }
                    else {
                        if (options.callback_failure) 
                            options.callback_failure(this);
                    }
                });
                $(this).find('input').bind('focus keypress click', function(){
                	$("#" + SelfID).next('.' + options['error_class']).remove();
                    $("#" + SelfID).removeClass(options['error_field_class']);
                    if("" !== options['placeholder']){
                    	var m = $('#'+options['placeholder']).html();
                    	if ( m == ('<' + options['error_tag']+' class="' + options['error_class'] + '">' + options['message'] + '</' + options['error_tag']+'>'))
                    		$('#'+options['placeholder']).html('');
                    }
                });
            }
            else {
                $(this).bind('blur', function(){
                    validate_field(this);
                });
                $(this).bind('focus keypress', function(){
                    $(this).next('.' + options['error_class']).fadeOut("fast", function(){
                       $(this).remove();
                    });
                    $(this).removeClass(options['error_field_class']);
                    if("" !== options['placeholder']){
                    	var m = $('#'+options['placeholder']).html();
                    	if ( m == ('<' + options['error_tag']+' class="' + options['error_class'] + '">' + options['message'] + '</' + options['error_tag']+'>'))
                    		$('#'+options['placeholder']).html('');
                    }
                });
            }
        }
        $(this).parents("form").submit(function(){
            if (validate_field('#' + SelfID)) 
                return true;
            else 
                return false;
        });
        function validate_field(id){
            var self = $(id).attr("id");
            var expression = 'function Validate(){' + options['expression'].replace(/VAL/g, '$(\'#' + self + '\').val()') + '} Validate()';
            var validation_state = eval(expression);
            if (!validation_state) {
                if ($(id).next('.' + options['error_class']).length == 0) {
                	if(options['placeholder'] !== ""){
                		$('#' + options['placeholder']).html('<' + options['error_tag']+' class="' + options['error_class'] + '">' + options['message'] + '</' + options['error_tag']+'>');
                	}
                	else
                		$(id).after('<' + options['error_tag']+' class="' + options['error_class'] + '">' + options['message'] + '</' + options['error_tag']+'>');
                	
                	$(id).addClass(options['error_field_class']);
            
                }
                if (ValidationErrors[FormID].join("|").search(id) == -1) 
                    ValidationErrors[FormID].push(id);
                return false;
            }
            else {
                for (var i = 0; i < ValidationErrors[FormID].length; i++) {
                    if (ValidationErrors[FormID][i] == id) 
                        ValidationErrors[FormID].splice(i, 1);
                }
                return true;
            }
        }
    };
    $.fn.validated = function(callback){
        $(this).each(function(){
            if (this.tagName == "FORM") {
                $(this).submit(function(){
                    if (ValidationErrors[$(this).attr("id")].length == 0) 
                        callback();
					return false;
                });
            }
        });
    };
})(jQuery);
$(function(){
	
})
