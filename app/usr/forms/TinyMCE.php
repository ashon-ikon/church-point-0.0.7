<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 2, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Form_TinyMCE //extends Point_Form_XZendForm
{
	/**
	 * @var $_front
	 */
	protected	$_front;
	
	/**
	 * @var $_viewObject
	 */
	protected	$_viewObject	= null;
	
	/**
	 * @var $_formObject
	 */
	protected	$_formObject	= null;

	
	public function __construct($Zend_form)
	{
		if (!$Zend_form instanceof Zend_Form)
			throw new Exception('A valid Zend Form must be provided');
		$this->_formObject	= $Zend_form;
	}

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
	
	public function getDefaultOptions()
	{
		               

		return $options = array('theme' 				  => '\'advanced\'',
								'plugins' 				  => '\'pagebreak,style,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras\'',
								'theme_advanced_buttons1' => '\'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect\'',
								'theme_advanced_buttons2' => '\'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime\'',//',preview,|,forecolor,backcolor\'',
//								'theme_advanced_buttons3' => '\'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen\'',
//								'theme_advanced_buttons4' => '\'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak\'',
								'theme_advanced_toolbar_location' => '\'top\'',
								'theme_advanced_toolbar_align' => '\'left\'',
                        		'theme_advanced_statusbar_location' => '\'bottom\'',
                        		'theme_advanced_resizing' => '\'false\'',

		                        // Example content CSS 
//		                        'content_css' => "css/content.css",

                        		// Drop lists for link/image/media/template dialogs
//                        		'template_external_list_url' => '\'lists/template_list.js\'',
//                        		'external_link_list_url'  => '\'lists/link_list.js\'',
//                        		'external_image_list_url' => '\'lists/image_list.js\'',
//                        		'media_external_list_url' => '\'lists/media_list.js\''
                        		);
								
	}
	
	public function makeForm( $content = null, $form_id /*, array $options */)
	{
		$baseUrl = $this->getView()->fullBaseUrl();
		/*==========*
		 * Include the necessary  JScript and CSS files 
		 *==========*/
		
		
		
		
		/* Ensure that we add the Javascript and css only once */
		if (!defined('TINYMCE')){
			$script_location = $baseUrl.'/js/tiny_mce/jquery.tinymce.js';
			$this->getView()->headScript()->appendFile( $script_location , 'text/javascript');
			/* NO NEED, WE'LL DO IT AT THE CLIENT SIDE */
//			$script = '$(function(){$(\'textarea.tinymce\').each( function(){initTmce(this);});});';
//			$this->getView()->headScript()->appendScript($script, $type = 'text/javascript');	
			define ('TINYMCE', 104 );
		}
//		/*==========*
//		 * Read the incoming options
//		 *==========*/
//		if (empty($options))
//			return;
//		
//		// ENSURE WE HAVE a form ID
//		
//		if (array_key_exists('form_id', $options))
//			$form_id = $options['form_id'];
//		
//		$opts = '';
//		foreach($options as $key => $value)
//		{
//			$opts .= ' '.$key . ': '. $value .',';
//		}
//		// remove trailing comma
//		if (substr($opts, -1, 1) == ',')
//			$opts = substr($opts, 0 , strlen($opts) - 1);
			
		/* Setup the form */

		/*==========*
		 * Build content
		 *==========*/
		//store hash in session
		$hash = makeUniqueHash($form_id) ;
		$session = Point_Object_Session::getInstance();
//		$session->$form_id = array('hash' => $hash);
//		file_put_contents('/home/ashon/something2',"\n\r[form_id]=>".$form_id ."\n\r[hash]=>". $hash, FILE_APPEND);
		$tinymce_form =  '<div>' . $this->makeFormHtml($form_id, $content). '</div>';
		
		return $tinymce_form ;
	}
	
	protected function makeFormHtml($id , $content=null)
	{

		// set initial params
		$this->_formObject->setAttribs(array('class' 	=> 'tinymce-form'));
		
		if (!$textarea = $this->_formObject->getElement('tinymcetarea'))
		{
			// Create elements:
			$testarea_id = 'tmce'.$id;
			$textarea = new Zend_Form_Element_Textarea('tinymcetarea');
			$textarea->setOptions(array(
				'size'	=>	'35',
				'rows'	=>	'20',
				'cols'	=>	'80',
				'class'	=>	'tinymce',
				'value'	=> 	$content,
				'id'	=>	$testarea_id	
			))
			->setDecorators(
				array(
					'ViewHelper'
				)
			);
		}
		
		$newArticle = new Zend_Form_Element_Checkbox('new');
		$newArticle->setOptions(array(
					 		'name'	=>  'new',
					 		'id'	=>	'new'.$id
					 	)
					 )
					 ->setDecorators(array('ViewHelper'));
			
		
		// create submit button
		$submit	  = new Zend_Form_Element_Submit('submit', array(
										'label' => 'Save'));
		$submit->setDecorators(array('ViewHelper'));

			
		$hidden1  = new Zend_Form_Element_Hidden('hash');
		$hash	  = makeUniqueHash($id);

		$hidden1->setValue($hash)
				->setDecorators(array('ViewHelper'));
			
		// Attach the elements... 
		$this->_formObject->addElement($hidden1)
			 			  ->addElement($textarea);
//			 			  ->addElement($newArticle)
//			 			  ->addElement($submit);
		
		
		/**
		 * ---------------------------------
		 * Use tabbed layout for the tinyMCE
		 * ---------------------------------
		 */
		$tab1 = $this->_formObject->getElement('tinymcetarea') .  
				$this->_formObject->getElement('hash');
				
		 $tab2 = '';
		 
		 $tabs = new Point_Object_Tabs;
		 $tabs_content = $tabs->makeTabs(array('Editor'		=>array('content'=>$tab1, 'id'=>'tab-1'.$id),
							   				   'Raw Html'	=>array('content'=>$tab2, 'id'=>'tab-2'.$id)),
							   				   'Id'.$id);
		
//		/**
//		 * ---------------------------
//		 * Insert Article selector
//		 * ---------------------------
//		 */
//		$article_selector = new Point_Object_ArticleSelector();
		
		
				
//		$form = $formBeginning . $tabs_content;
////		$form .= wrapHtml($article_selector->makeSelector(), 'div');
//		$form .= wrapHtml($this->_imageChooser($testarea_id), 'div');
//		$form .= wrapHtml( $this->getElement('new'). ' Save as new article'. $this->getElement('submit'), 'div');
//		$form .= $this->formClose();
				 
		return $tabs_content;
	}
	
	protected function _imageChooser($id)
	{
		$chooser  = null;
		/**
		 * Insert Image selector
		 * ---------------------------
		 */
		$img_selector = new Point_Object_ImageSelector();
		$chooser 	  = $img_selector->makeSelector(true);
		
		return $chooser;
	} 
	
}
