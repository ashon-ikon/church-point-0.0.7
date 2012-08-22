<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 29, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Object_ImageSelector
{
	protected 	$_albums	= array();
	protected	$_jDialog	= null;
	
	protected	function _getDefaultOptions()
	{
		return array(
        						'title' 		=> 'Image Selector Dialog',
        						'id'			=> genRandomString(3, true) . '_d',
        						'linkName'		=> 'Select Image',
        						'class'			=> '',
        						
        						/* Button Style for the css */
        						//style="padding: .4em 1em .4em 20px; text-decoration: none; position: relative;"
        						'linkCss'		=> array( 
        													'font-size' 	=> '0.85em',
        													'font-weight'	=> 'bold',
        													'line-height'	=> '1.2em',
        													'text-decoration'=>'none',
        													'color'			=> '#fff',
        													'background'	=> '#aaa',
        													//'border'		=> '1px solid #88BBE4',
        													'padding'		=> '.4em 1em .4em 20px',
        													'position'		=> 'relative'),
        						/* Dialog settings */
        						'settings'		=> array(
															'width' 	=> 600,
															'height'	=> 600,
															'draggable'	=> 'false',
															'modal'		=> 'true',
															'resizable'	=> 'false',
															'autoOpen'	=> 'false'),	
								'others'		=> array(
															'class' => '.edit-content'));  
	}
	
	protected function _getDialog($msg, $options = array())
	{
		$dialog = new Point_Model_FloatingDialog();
		
		if (null !== $options) // Use defualt options
		{
			$dialogOptions = $this->_getDefaultOptions();
			$options = array_merge($options, $dialogOptions);	
		}
		
															
        return $dialog->makeDialog($msg, $options);

	}
	
	public function makeSelector($useDialog = true)
	{
		$ret 				= null;
		$selector_content 	= 'Image selector content';
		 
		
		if ($useDialog)
		{
			$ret = $this->_getDialog($selector_content);	
		}else
		{
			$ret = $selector_content;
		}
		
		return '<div>'. $ret.'</div>';	
	}
}