<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 25, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Editor_Hypertext extends Point_Editor_Base implements Point_Editor_Base_Interface 
{
	protected	$_floatingDialog = null;
	
	/**
	 * This helps us to make each JQuery_UI unique
	 */
	protected	static	$_content_count = 0;
	
	public function __construct()
	{
			
	}
	
	protected function _getDialog()
	{
		if (null === $this->_floatingDialog )
		{
		
		}
	}
	
	/**
	 * Here the html form for the editor will be created
	 */
	protected function _prepareForm( $msg, $ids)
	{
		$html		= null;	/* The html to return */
		$form		= new Point_Form_XZendForm();
		// set initial params
		
		
		$form->setAttribs(array('class' 	=> 'tinymce-form',
								'onsubmit'	=>'return submitconts(this);'))
			 				->setMethod('post')
			 				->setAction('/ajax/');
		$new_article = new Zend_Form_Element_Checkbox('new', array('decorators'=> array('ViewHelper')) );
		$new_article->setOptions(array(
								'name'	=>  'new',
								'id'	=>	'new'.$ids
							));
		$submit 	= new Zend_Form_Element_Submit('submit', array('decorators'=> array('ViewHelper')));
		$submit->setValue('Save');
		$form->addElement($new_article)
			 ->addElement($submit);
				
		// Form element
		$form->setDecorators(array(
							'FormElements',
							'Form'	));
		
		$tinymce	= new Point_Form_TinyMCE($form);
		
		$html		= $form->getFormHeadTag();
		$html 		.= $tinymce->makeForm($msg, $ids, $tinymce->getDefaultOptions());
		$html		.= '<div>'.$form->getElement('new').'</div>';//wrapHtml('something', '<div>');
//		$html		.= $form->getElement('new');
		$html		.= $form->getElement('submit');
		
		$html		.= $form->formClose();
		
		return 		$html;
	}
	
	
	/**
	 * 	Treats content
	 * 
	 * 	@return String Content with Editor based on Role
	 */
	public function treat( $content, $access, $ids, $mode = 'done')
	{
		
		$admin = ''; // Admin Content
		
		//scramble the ids
		
		$content_id = makeObscureItem($ids);
		
		// Check if user is an editor or admin
		if ( Point_Model_User::GUEST != $access && Point_Model_Page_Contents::MODE_EDIT == $mode )
		{
			// Setup the edit stuff
			self::$_content_count ++;
			$form_content = $this->_prepareForm( $content, $content_id);
			$admin = $this->_prepareDialog( $form_content, $content_id, 'Content Editor');
			$admin = '<div class="editor-panel"><p>' .$admin. '</p></div>';
					
		}
		
		// do the content 
		
		$content = '<div id="'.$content_id.'_div">'.$content.'</div>';
		$content =(!empty($admin) ? $content .$admin: $content); // add admin html
		
		return $content;		
	}
	
}