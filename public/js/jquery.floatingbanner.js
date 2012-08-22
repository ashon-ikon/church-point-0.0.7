/*
 * Floating Banner jQuery Plugin v1.0
 * http://code.google.com/p/floatingbanner/
 *
 * Copyright 2010, Funke Web Solutions
 * Released under the GPL Version 3 license.
 *
 * $Id: jquery.floatingbanner.js 4172 2010-04-05 05:57:21Z rob $
 */
(function($) {
	jQuery.fn.floatingbanner = function (options) {
		
		var settings = jQuery.extend({
	     ie6: true
	  }, options);
		
	  var banners = new Array();
	  this.each(function(){
	    var placeholder = $('<div></div>').height($(this).outerHeight()).width($(this).outerWidth()).hide().insertBefore(this);
	    var posTop = $(this).position().top;
	    banners.push({'banner':this,'placeholder':placeholder,'posTop':posTop});
			var scrollTop = $(window).scrollTop();
	    if(pastView(posTop, scrollTop))
	      floatBanner(this, placeholder);
	  });
	
	  function floatBanner(element, placeholder){
	    $(element).addClass('floating-banner');
	    $(placeholder).show();
	    if (!settings.ie6 && $.browser.msie && parseInt(jQuery.browser.version) == 6) {
	      $(element).css({'top': $(window).scrollTop(), 'position': 'absolute'});
	    }
	  }
	
	  function unFloatBanner(element, placeholder){
	    $(element).removeClass('floating-banner');
	    $(placeholder).hide();
			if (!settings.ie6 && $.browser.msie && parseInt(jQuery.browser.version) == 6) {
        $(element).css({'position': 'static'});
      }
	  }
	
	  function pastView(posTop, scrollTop){
	    if (scrollTop < posTop)
	      return false;
	    return true;
	  };
	
	  $(window).scroll(function(){
			var scrollTop = $(window).scrollTop();
	    $(banners).each(function(){
	      if(pastView(this.posTop, scrollTop))
	        floatBanner($(this.banner), $(this.placeholder));
	      else
	        unFloatBanner($(this.banner), $(this.placeholder));
	    });
	  });
	};
})(jQuery);