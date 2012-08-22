<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Sep 21, 2011
 * (c) 2011 Copyright
 * -------------------------------------------
 */
class Point_Model_FloatingDialog {
	/**
	 * @var $_front
	 * 
	 * Handle to Front Controller
	 */
	protected	$_front;
	
	/**
	 * @var $_viewObject
	 */
	protected	$_viewObject	= null;
	

	/**
	 * method	getView()
	 * @return ZendView view object that can be rendered into OR null
	 */
	public function getView()
	{	
		/* Rase an alarm if we don't have a valid view */
		if (null === $this->_viewObject)
    	{
    		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
	    	if (!$this->_viewObject = $viewRenderer->view)
	    	{
		    	throw new Exception ('Call to empty view!');
	    	}
	    	
    	}
		
		return $this->_viewObject;	
	}
	
	/**
	 * method	setView()
	 * @param	$view Zend_View object
	 * @return ZendView view object that can be rendered into
	 */
	public function setView( $view )
	{
		if (!$view instanceof Zend_View)
    	{
    		throw new Exception ('Invalid view object provided!');
    	}
	
		$this->_viewObject = $view;
		return $this;
	}
	
	private function getCssFromArray($cssArray, $default = null, $addQuotes = false)
	{
		if (is_array($cssArray))
			{
				$settings = '{';
				foreach($cssArray as $key => $value)
				{
					if ($addQuotes)	// Add quotes for DOM elements (css)
						$settings.= '\'' . $key .'\':\''. $value . '\',';
					else
						$settings.= $key .':'. $value . ',';
				}
				
				if (substr( $settings, -1) == ',')
				{ $settings = substr($settings, 0, -1);} // remove the trailing comma
				
				return $settings.= '}';
			}else{ // Use default
				return 	$settings = $default;
			}
			;
	}
	
	public function makeDialog( $content = null, array $dialogConfig = array(), array $options = array())
	{
		$baseUrl = $this->getView()->baseUrl();
		/*==========*
		 * Include the necessary  JScript and CSS files 
		 *==========*/
		
		/* Ensure that we add the Javascript and css only once */
		if (!defined('JDIALOG_UI')){
//			$this->getView()->headLink()->appendStylesheet($baseUrl . '/css/smoothness/jquery-ui-1.8.16.custom.css'/*'/css/cpall.css'*/);
			$this->getView()->headScript()->appendFile( $baseUrl.'/js/jquery-ui-1.8.16.ashon.min.js' , 'text/javascript');		
			define ('JDIALOG_UI', 100 );
		}
		/*==========*
		 * Read the incoming options
		 *==========*/
		/*
		 * title			=> Title of Dialog
		 * id				=> ID of the Dialog (must be unique)
		 * linkName 		=> Display name for the link to open the dialog
		 * ================Dialog Settings================
		 * settings.width		=> Width of dialog
		 * settings.height	=> height
		 */
		$title = $id = $linkName = $settings = $linkCSS = $linkClass = null ; // clear out the configs
		if (is_array($dialogConfig))
		{
			
			if(array_key_exists('title', $dialogConfig))
				$title = $dialogConfig['title'];
			if(array_key_exists('id', $dialogConfig))
				$id = $dialogConfig['id'];
			if(array_key_exists('linkName', $dialogConfig))
				$linkName = $dialogConfig['linkName'];
			else 	// Use the dialog title
				$linkName = $title;
			if (array_key_exists('class', $dialogConfig)){
				$linkClass   = $dialogConfig['class'];
			} else{ $linkClass = ''; }
		
			// get the Dialog info
			if (array_key_exists('settings', $dialogConfig)){
				$settings  = $this->getCssFromArray($dialogConfig['settings'], '{ autoOpen: false,	modal: true, width: 360}');
			}else{ $settings = '{ autoOpen: false,	modal: true, width: 360}';	}
			
			if (array_key_exists('linkCss', $dialogConfig)){
				$linkCSS   = $this->getCssFromArray($dialogConfig['linkCss'],null, true);
			} else{ $linkCSS = null; }	
			
			
		}

		/* Setup the dialog */
	$script = '$(function(){
		// Dialog 
		$(\'#'. $id .'\').dialog('. $settings.');' .
		
		'var styleObj =  {\'padding\': \'.2em 1em .2em 20px\', ' .
		'\'text-decoration\': \'none\', ' .
		'\'position\': \'relative\'};'.
// Dialog Link and incoming css
'$(\'#'.$id. '_link\')'.(null != $linkCSS ? '.css('.$linkCSS.')': '').	// add css
						(null != $linkClass ? '.addClass($(\'#'.$id.'_link\').attr(\'class\') + \' '. $linkClass.'\')': '' ). // add class
					'.click(function(){
	$(\'#'. $id. '\').dialog(\'open\');
	return false;
	});
	//hover states on the static widgets
	$(\'#'.$id .'_link, ul#icons li\').hover(
		function() { $(this).addClass(\'ui-state-hover\'); }, 
		function() { $(this).removeClass(\'ui-state-hover\'); }
	);
});';
		$this->getView()->headScript()->appendScript($script, $type = 'text/javascript');
		/*==========*
		 * Build content
		 *==========*/
		$dialog =  '<a href="#" id="'.$id .'_link" >' .
							'<span class="ui-icon ui-icon-newwin"></span>'. $linkName .'</a>' .
					'<div id="'. $id.'" title="' . $title .'" >' .	$content .  '</div>';
		
		return $dialog;
	}
}