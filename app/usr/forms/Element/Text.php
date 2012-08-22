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
class Point_Form_Element_Text extends Point_Form_Element
{
	function __construct($name, $options = array() )
	{
		$this->setElementType('text');
		parent::__construct($name, $options);
		return $this;
	}
	
}
