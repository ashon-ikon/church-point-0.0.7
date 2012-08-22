/* Default procedures to run*/
$(function(){
	setEmphasis();
	$('div [class^=tabsholder]').each( function(){initTabs($(this).attr('id'));});/*Initialize all clickable tabs*/
	/*$('textarea.tinymce').each( function(){initTmce();});*//*Initialize all tinyMCEs*/
});

var xajax;
function loadXHR(){var r;try{r= new XMLHttpRequest();} catch (e){try{ r = new ActiveXObject("Msxml2.XMLHTTP");}catch(e){try{r= new ActiveXObject("Microsoft.XMLHTTP");}catch(e){alert("Error with browser request!"); return false;} } } return r; }

function loadXMLStr(txt) 
{
var parser, xmlDoc;
if (window.DOMParser)
  {
  parser=new DOMParser();
  xmlDoc=parser.parseFromString(txt,"text/xml");
  }
else // Internet Explorer
  {
  xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
  xmlDoc.async="false";
  xmlDoc.loadXML(txt); 
  }
return xmlDoc;
}

function getFirstChild(xmlElement, tag)
{
	if (xmlElement && typeof xmlElement != "undefined")
		if (0 != xmlElement.getElementsByTagName(tag)[0].childNodes.length)
			return xmlElement.getElementsByTagName(tag)[0].childNodes[0].nodeValue;
	return false;
}

function getFirstTag(xmlElement, tag)
{
	try{
	if (xmlElement)
		if ('undefined' != typeof xmlElement.getElementsByTagName(tag))
			return xmlElement.getElementsByTagName(tag)[0];
	return false;
	}catch (e) { alert('getFirstTag:\n'+'tag: '+tag+'\n'+e); return false;}
}

//-------GLOBALS------
var xid, el;
//--------------------
function submitconts(elem)
{
// Get handle to ajax
var l, e, f, xmlDoc,perser, pm, da, c;
var id = $($(elem).find('textarea').get(0)).attr('id');
c = $("#"+id).tinymce().getContent();//content
el = $("#"+id).tinymce();
var form1 = elem;/*$("#"+id).closest("form");*/
h = $(form1).find('input[name$="hash"]').attr('value');
n = $(form1).find('input[name="new"]').attr('checked')=== false ? '0':'1';
//h = document.getElementsByName(id+"_hash")[0].value;
//build uri
pm="req=cms&id="+id+"&cont="+c+"&hash="+h+"&new="+n;
l = "/ajax/"; m = "post";
xid = id;
xajax = loadXHR();
xajax.open(m,l,true);
xajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
xajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
xajax.onreadystatechange=hContentsSubmit;
xajax.send(pm);
el.setProgressState(1);
da = '#'+id.substring(4)+"_d";
$(da).dialog("close");

return false;
}

function hContentsSubmit()
{
	if (xajax.readyState==4 && xajax.status==200)
	{
	var xmlDoc;
//		try{
			
				if(xmlDoc=xajax.responseXML)
				{
					// Check if we have any error first
					var status, error;
					if (status = getFirstTag(xmlDoc,"status") ){

						if (error = getFirstChild(status,"error") )
						{
							alert("Error occurred!\n\nDescription: \n"+getFirstChild(status,"message") + ". ("+ error +")");
							return false;
						}
					}
				}
				var rt = getFirstChild(getFirstTag(xmlDoc,"response"),"content" ),
					 dID = '#'+xid.substring(4)+"_div";

				
				$(dID).html(rt);
				$($('#'+xid).tinymce()).setProgressState(0);
				//el.setContent(rt);
//				$('#'+xid).tinymce().setContent(rt)

//		} catch (e)	{/* Change this... */ alert("An error occured: \n" + e); }
			
		return false; // return peacefully ;)
	}

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
				var pDiv = $(this).closest("div"),
					 tx = $(pDiv).find('textarea[id^="tmce"]').get(),
					 rhtDiv = $(pDiv).find('div[id^="tab-2"]');
					 $(rhtDiv).append($(tx));
					 $(tx).tinymce().hide();
			}
			else if($(this).find('a').text() == 'Editor')
			{
				var pDiv = $(this).closest("div"),
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

                        // General options
                        theme : "advanced",
                        width	: "400",
                        height: "400",
                        plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

                        // Theme options
                        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
                        theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
//                        theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
//                        theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
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
