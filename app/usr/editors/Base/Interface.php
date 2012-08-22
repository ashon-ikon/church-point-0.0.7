<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 25, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
interface Point_Editor_Base_Interface
{
	/**
	 * 	Parses content
	 * 
	 * 	All editors must implement this.
	 */
	public function treat( $content, $role, $ids, $mode = 'done');
	
}