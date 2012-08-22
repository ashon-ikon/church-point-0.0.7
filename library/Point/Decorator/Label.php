<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Dec 23, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Decorator_Label extends Zend_Form_Decorator_Abstract
{
	protected $_placement = 'PREPEND';
	
	public function render($content)
	{
		if (null === ($element = $this->getElement())) {
			return $content;
		}
		if (!method_exists($element, 'getLabel')) {
			return $content;
		}
		
		$label = $element->getLabel() . ':';
		
		if (null === ($view = $element->getView())) {
			return $this->renderLabel($content, $label);
		}
		
		$label = $view->formLabel($element->getName(), $label);
		
		return $this->renderLabel($content, $label);
	}
	
	public function renderLabel($content, $label)
	{
		$placement = $this->getPlacement();
		$separator = $this->getSeparator();
		
		switch ($placement) {
			case 'APPEND':
				return $content . $separator . $label;
			case 'PREPEND':
			default:
				return $label . $separator . $content;
		}
	}
}