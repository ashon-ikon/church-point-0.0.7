<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: Layout.php
 * 
 * Created by ashon
 * 
 * Created on Aug 2, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */
class Point_View_Layout extends Zend_View_Abstract
{
	public function _run($buffer = null)
	{
		echo $buffer;
	}
	
	public function render($name)
	{
		//echo 'something<br />';
//		ob_start();
//		$this->_run($this->_file);
		parent::render($name);
	}
}