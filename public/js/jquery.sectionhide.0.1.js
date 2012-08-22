/*
 *
 * Copyright 2012, Ashon Associates Inc Web Solutions
 * 
 * This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
(function( $ ){
	
	
	 var SectionAutohide = function(element, options){
			//Defaults are below
			var settings = $.extend({}, $.fn.sectionAutohide.defaults, options);
			

	 }
	
	$.fn.sectionAutohide = function( options ) {  

	    // Create some defaults, extending them with any options that were provided
	    var settings = $.extend( {
	      'location'         : 'top',
	      'background-color' : 'blue',
	      'method'			 : 'init'
	    }, options);
	    
	    return this.each(function() {        

	      // sectionAutohide plugin code here

	    });
	}
	
	//Default settings
	$.fn.sectionAutohide.defaults = {
		effect: 'fold',
		showText: 'Expand',
		hideText: 'Collapse',
		beforeShow: function(){},
		afterShow: function(){},
		beforeHide: function(){},
		afterHide: function(){},
	};
	
	$.fn._reverse = [].reverse;
})( jQuery );