<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 30, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Object_ArticleSelector
{
	public function makeSelector()
	{
		$content = wrapHtml('<strong>Article Selector</strong>', 'div' , array('class' => 'selector'));
		return wrapHtml($content, 'div' , array('class' => 'aselector'));
	}
}