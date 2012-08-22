<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 26, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Form_XZendForm extends Zend_Form
{
	protected	$_xDecorators	= array();
	protected	$_class_view_vars	= array();
	
	public $elementDecorators = array(
			'ViewHelper',
			'Description',
			array(array('elemDiv' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
			array('label', array('tag' => 'td')),
			array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
			);
		
	public $buttonDecorators	= array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
			array(array('label'=> 'HtmlTag'), array('tag' => 'td' , 'placement' => 'prepend')),
			array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
			);
	
	public $selectDecorators	= array(
			'ViewHelper',
			);
	
	public function getFormHeadTag()
	{
		//<form enctype="application/x-www-form-urlencoded" action="" method="post"><dl class="zend_form"></dl></form>
		$hack = 'nothing'.$this . $this->getEnctype() . $this->getId() ;
		
		$tag = '<form ';
		foreach ($this->getAttribs() as $attr => $val )
			$tag .= $attr . '="' . $val. '" ';
		
		// Trim the string right
		if (substr($tag , -1, 1) == ' ')
			$tag = substr($tag, 0, strlen($tag) - 1);
	
		$tag .= '>';
		
		return $tag;
	}
	
	public function setXDecorators( $decorators = array())
	{
		if (!empty($decorators))
		{
			$this->_xDecorators = $decorators;
		}
		return $this;
	}
	
	public function getXDecorators()
	{
		return $this->_xDecorators;
	}

	protected function wrapForm_($form, $tag, array $attrs = array())
	{
		$ret =  '<'.$tag;
		if (!empty($attrs))
		{
			$ret .= ' ';
			foreach($attrs as $attr => $val)
			$ret .= $attr .'="'.$val .'" ';
		}
		$ret .= '>'. $form. '</'.$tag.'>';
		return $ret;
	}
	
	public function formClose()
	{
		return '</form>';
	} 
	
	public function getCloseTag()
	{
		return $this->formClose();
	}
	 
}
