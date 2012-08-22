<?php

class Application_Model_AppMenus 
{
	/**
	 * @var xmlObject description
	 */
	protected $_xml;
	/**
	 * @var menuList
	 */
	protected $_menuList;
	
	protected $_menuListParentClass;
	
	protected $_menuListChildClass;
	
	public function __construct($filename, $section = null, $options = false)
	{
		/* Read the content of the XML */
		$this->_xml 	= new Zend_Config_Xml($filename, $section, $options);
		//Get the parent and child CSS class if we have
		$this->_menu	= $this->_menuListParentClass = $this->_menuListChildClass = null; 
		
	}
	
	public function toArray()
	{
		return $this->_xml->toArray();
	}
	
	public function getMenus($options = null)
	{
		if (is_array($options))
		{
			if (array_key_exists('parentClass', $options))
				$this->_menuListParentClass = $options['parentClass'];
			if (array_key_exists('childClass', $options))
				$this->_menuListChildClass = $options['childClass'];
		}
		if (null == $this->_menuList)
		{
			$this->_menuList = $this->arrayToMenu($this->_xml->toArray());
		}
		return $this->_menuList;
	}
		
	private function arrayToMenu(array $menus)
	{
		$ret_html	= '<ul>';
		/* Loop through and create the link */
		foreach ($menus['menu'] as $menu)
		{
			$ret_html	.=	$this->genLink($menu, $this->_menuListParentClass);
		}
		$ret_html	.= '</ul>';

		return $ret_html;
	}
	
	private function genLink($menu, $cssClass = null)
	{
			$link		= 	'<li><a href="';
			/* Check if we have href or mvc*/
			if(!empty($menu['href']))
			{
				// use href...
				$link .=	$menu['href'];
			}
			else if (is_array($menu['mvc']))
			{
				// use mvc
				$link .= 
				Zend_Controller_Front::getInstance()->getBaseUrl() . 
							DIRECTORY_SEPARATOR	. $menu['mvc']['controller'] .	
							DIRECTORY_SEPARATOR . $menu['mvc']['action'];
			}
			else // use a null href '#'
				$link .= '#';
			/* Add the class if there exist */
			$link .= '" ' . (isset($cssClass)? ' class="'. $cssClass. '" ': '') . '>';
			
			$link		.= $menu['name']. '</a>';
			
			/* Append the child menu */ 
			if( array_key_exists('menu', $menu) && is_array($menu['menu']))
					$link	.='<ul>' . $this->genLink($menu['menu'], $this->_menuListChildClass) .'</ul>';
			/* Close the list */
			$link		.= 	'</li>';
		return $link;
	}
}

