/* Default procedures to run*/
$(function(){
//	setEmphasis();
	$('div [class^=tabsholder]').each( function(){initTabs($(this).attr('id'));});/*Initialize all clickable tabs*/
	$('textarea.tinymce').each( function(){initTmce(this);});/*Initialize all tinyMCEs*/
	
	initClickables();
	// Enable auto hiding for sections that declare it.
	$('.autohide').sectionAutohide();
	
	// Floating banner
	$('#lace_').floatingbanner();
	
	/* Facebook stuff */
	(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
});

//-------GLOBALS------
var xid, el, cont;
//--------------------
function submitconts(elem)
{
// Get handle to ajax
var l, e, f, xmlDoc,perser, pm, da, c;
var form = elem;/*$("#"+id).closest("form");*/
var id = $($(elem).find('textarea').get(0)).attr('id');
cont= c = $("#"+id).tinymce().getContent();//content
el = $("#"+id).tinymce();
h = $(form).find('input[name$="hash"]').attr('value');
n = $(form).find('input[type="checkbox"][name^="new"]').attr('checked')=== false ? '0':'1';

$.ajax({
  	url: $(form).attr('action'),
  	type: 'post',
  	data: {
  				cont: c,
  				req : 'cms',
  				id	 : id,
  				hash: h,
  				new : n},
  	success: function(data){
  		try{
  			var _id = id.substring(4), div,
  				err = $(data).find('error').text(),
  				msg = $(data).find('message').text();
  			el.setProgressState(0);
  			if (err)
  			{
  				alert("Error occurred!\n\nDescription: \n"+ msg + ". ("+ err +")");
				return false;
  			}
  			da = '#'+_id+"_d";
  			div = '#'+_id+'_div';
  			$(div).html(c);
  			$(da).dialog("close");
  		}catch(e){	alert(e);	}
  		
  		return false; // return peacefully ;)
  	},
	});
el.setProgressState(1);

return false;
}

//set emphasis
function setEmphasis()
{
	$("#ftr ul li li").hover(
		function() { $(this).addClass('emphasize'); }, 
		function() { $(this).removeClass('emphasize'); }
	);
}

function initTabs(id)
{
	var tbUl = $('#'+id + ' ul:first'),
		 tbDiv = $('#'+id + ' div:first');
	var allD = $(tbDiv).children().get();
			$(allD).hide();
	var f = $(tbUl).children().get(0);
	$(f).addClass('active').show();
	var d = $(tbDiv).children().get(0);
	$(d).show();
	var ch = $(tbUl).children().get();
	$(ch).click(function(){
			$(ch).removeClass('active');
			$(this).addClass('active');
			var allD = $(tbDiv).children().get();
			$(allD).hide();
			/* Find tinymce textarea and show if tab 2 */
			if ($(this).find('a').text() == 'Raw Html')
			{
				//alert($($($(this).closest("div.tabsholder").get(0)).find('textarea[id^="tmce"]')).html());
				var pDiv = $(this).closest("div.tabsholder"),
					 tx = $(pDiv).find('textarea[id^="tmce"]').get(),
					 rhtDiv = $(pDiv).find('div[id^="tab-2"]');
					 $(rhtDiv).append($(tx));
					 $(tx).tinymce().hide();
			}
			else if($(this).find('a').text() == 'Editor')
			{
				var pDiv = $(this).closest("div.tabsholder"),
					 tx = $(pDiv).find('textarea[id^="tmce"]').get();
					 $(tx).tinymce().show();
			}
			
			var at = $(this).find('a').attr('href');
			$(at).fadeIn();
			return;
		}
	);			
}
function initTmce(tx)
{

	$(tx).tinymce({
		// Location of TinyMCE script
		script_url : '/js/tiny_mce/tiny_mce.js',
		// Location of Image list
		external_image_list_url : '/images/imageslist',
		// General options
		theme : "advanced",
		width	: "400",
		height: "400",
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor",
//		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
//		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		//                       content_css : "css/tinymce.css"
		content_css : "/css/main.css",
		theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px",
		font_size_style_values : "10px,12px,13px,14px,16px,18px,20px",
			});
}
function initClickables()
{
}