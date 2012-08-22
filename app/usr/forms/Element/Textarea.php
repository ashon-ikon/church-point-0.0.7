<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 25, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */

/**
 * Text element
 */
class Point_Form_Element_Textarea extends Point_Form_Element
{
	function __construct($name , $options = array() )
	{
		$this->setElementType('textarea');
		parent::__construct($name, $options);
		return $this;
	}
	
	protected function getElementHtml()
	{
		$html = '<textarea type="'. $this->getElementType() . '" ';
		if (!array_key_exists( 'rows' , $this->_elementOptions)){
			$this->_elementOptions['rows'] = 3;
		}
		if (!array_key_exists(  'cols' , $this->_elementOptions)){
			$this->_elementOptions['cols'] = 20;
		}
		
		if(!array_key_exists('name', $this->_elementOptions))
			$this->_elementOptions['name'] = $this->getElementName();
			
		if(!array_key_exists('value', $this->_elementOptions))
			$this->_elementOptions['value'] = '';
			
		
		foreach ( $this->_elementOptions as $attr => $val )
			$html .= $attr . '="'. $val . '" ';
		$html .= '>';
		
		if (!isset($content))
			$content = '';
			
		$html .= $content .'</textarea>'; 
		
		return $html;
	}
}

