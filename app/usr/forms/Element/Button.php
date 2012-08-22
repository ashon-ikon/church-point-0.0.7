<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 25, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Form_Element_Button extends Point_Form_Element
{
	function __construct($name , $options = array() )
	{
		$this->setElementType('button');
		parent::__construct($name, $options);
		$this->removeLabel();
		return $this;
	}
}
