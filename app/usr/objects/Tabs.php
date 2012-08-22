<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 29, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Object_Tabs
{
	/**
	 * @param associative array of content
	 * 		array('tab-name' =>
	 * 							array(id 		=>
	 * 								  content	=> ))
	 * return String => html of tabs
	 */
	public function makeTabs(array $contents = array(), $id = null )
	{
		if (!empty($contents))
		{
			$lis = $divs = '';
			if (null === $id) $id = 'id'. substr(time(), 3,4);
			
			foreach($contents as $tabName => $options)
			{
				if(is_array($options))
				{
					$a 		 = wrapHtml($tabName, 'a', array('href'		=>'#'.$options['id'],
															 'onclick'	=> 'return false;'));
					$lis 	.= wrapHtml($a, 'li');
					
					$divs 	.= wrapHtml($options['content'], 'div', array(
																	'id' 	=> $options['id'],
																	'class' => 'tab_content' ));
				}
			}
			$parent_ul 	  = wrapHtml($lis, 'ul', array('class' => 'xtabs'));
			$parent_div	  = wrapHtml($divs, 'div', array('class' => 'tabs_container'));
			$tabsHtml 	  = wrapHtml($parent_ul . $parent_div, 'div', array(
																	'id' 	=> 'tabs'.$id,
																	'class'	=> 'tabsholder'	)); 
			
			return $tabsHtml;
		}
		
	}
}
