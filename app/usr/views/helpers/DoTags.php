<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 25, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_View_Helper_DoTags extends Zend_View_Helper_Url{
	
	
	public function doTags($tag_list)
	{
		
		$request 	= Zend_Controller_Front::getInstance()->getRequest();
		
		$base_url	= $request->getScheme(). '://'. $request->getHttpHost(). '/'.$request->getControllerName() . '/search/';
		
		/* Make tags links */
		$tags		= explode(',', $tag_list);
		
		$ret_url 	= null;
		foreach ($tags as $tag)
		{
			$ret_url .= wrapHtml($tag, 'a', array('href' => $base_url . '?q='. rawurlencode($tag))) . ' ';
		} 
		
		return $ret_url;
	}
}