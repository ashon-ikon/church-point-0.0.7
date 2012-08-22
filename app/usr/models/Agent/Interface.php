<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 18, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
interface Point_Model_Agent_Interface
{
	/**
	 * 	Creates content
	 * 
	 * 	All agents must implement this.
	 */
	public function makeContent( $params = null);
	
		
}