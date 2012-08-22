<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 20, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Model_Page_Menus
{
	protected		$_adapter	= null;
	protected		$_select	= null;
	
	protected	$_menuParentClass	= null;
	protected	$_menuChildClass	= null;
	protected	$_topMenus			= null;
	
	const		TOP_MENU_ID		= TOP_PAGE_ID;
	
	
	public function __construct( Zend_Db_Table_Abstract $adapter )
	{
		$this->setAdapter($adapter);
	}
	
	public function setAdapter( $adapter )
	{
		
		$this->_adapter	= $adapter;
		if (! $adapter instanceof Zend_Db_Table_Abstract )
		{
			throw new Zend_Exception( 'Invalid adapter passed' );
		}
		return $this;
	}
	
	public function getAdapter ()
	{
		return $this->_adapter;
	}	
	
	
	public function getTopMenus()
	{
		if(null === $this->_topMenus)
		{
			/* Build a query */
			$this->_topMenus = $this->getAdapter()->select()
								   ->from(array('c' => 'pages_table'))
								   ->where('page_parent_id = ? ', self::TOP_MENU_ID)
								   ->order('page_rank ASC ')
								   ->query()
								   ->fetchAll();
		}
		
		return $this->_topMenus;
	}
	
	/**
	 * This function loops through all the pages seen in the 
	 * database and computes the menu based off them
	 * 
	 * @param array $options
	 * 		'parentClass'	=	Class to be used for topmost parent link
	 * 		'childClass'	=	Class to be used for sub-menu list
	 */
	
	public function	getMenus($options = null)
	{
		// Get HTML class attribs.
		if (is_array($options))
		{
			if (array_key_exists('parentClass', $options))
				$this->_menuParentClass = $options['parentClass'];
			if (array_key_exists('childClass', $options))
				$this->_menuChildClass = $options['childClass'];
		}
		
		
		$menu = null;
		//check if we can read from xml first
		$xmlMenus = null;//$this->readFromXml();
		
		if(!empty($xmlMenus))
		{
			/* Get from xml */
			$menu = $this->_getMenuContentXml($xmlMenus);
		}else{
			/* Get all top page */
			$menu = $this->_getMenuContent($this->getTopMenus());	
		}
		
		return	$menu;
	}
	
	
	private function _getMenuContent(array $pages)
	{
		$user = Point_Model_User::getInstance();
		$ret_html	= '<ul>';
	
		/* Loop through and create the link */
		foreach ($pages as $page)
		{
			// Check page acess..
			if ( $user->getRoleNum() >= $page['page_access_level'] && $page['page_published']){ 
				$ret_html	.=	$this->_genLink($page, $this->_menuParentClass);
			}
			
		}
	
		$ret_html	.= '</ul>';

		return $ret_html;
	}
	
	/**
	 * Read from readFormXml
	 * @return bool returns true if xml has content
	 */
	public function	readFromXml($uri = null, $section = 'page-menus')
	{
		$uri	= (null == $uri? /*use default file*/
								APPLICATION_PATH . '/configs/'. md5('menus').'.php' : $uri );
		if(!file_exists($uri) && !is_writable($uri))
		{	return false;	}
		
		// Read xml...
		if($xmlFile	= new Zend_Config_Xml($uri , $section))
		{
			return $xmlFile->toArray();	// return Array Alternative
		}
		
		return false;
	}
	
	
	private function _getMenuContentXml(array $menus)
	{
		$user = Point_Model_User::getInstance();
		$ret_html	= '<ul>';
		
		/* Loop through and create the link */
		foreach ($menus['menu'] as $menu)
		{
			// Check page acess..
			if ($user->getRoleNum() >= $menu['page_access_level'] && $menus['page_published'] ){ 
				$ret_html	.=	$this->_genLinkXml($menu, $this->_menuParentClass);
			}
		}
		$ret_html	.= '</ul>';

		return $ret_html;
	}
	/*
	 * [page_id] => 1
       [page_parent_id] => 0
       [page_title] => Home
       [page_menu_id] => 0
       [page_href] => /
       [page_controller] => 
       [page_action] => 
       [page_access_level] => 0
       [page_group_id] => 0
       [page_published] => 0
       [page_creation_date] => 2011-09-22 11:19:00
	 */		
	private function _genLink($page, $cssClass = null)
	{
		if (is_array($page))
		{
			$link		= 	'<li><a href="';
			
			/* Check if we have href or mvc*/
			if (!empty($page['page_controller']))
			{
				// use mvc values
				$link .= $this->_makeHref($page['page_action'], $page['page_controller'], $page['page_module']);
			}
			else // use a null href '#'
				$link .= '#';
			
				
			/* Add the title if it exists */
			$link .= '" ' . (!empty($page['page_title'])? ' title="'. $page['page_title']. '" ': '');
			
			
			/* Add the class if there exist */
			$link .=  (isset($cssClass)? ' class="'. $cssClass. '" ': '') . '>';
			$link		.= $page['page_name']. '</a>';
			
			
			
			/* Append the child menu */
			$children	= $this->fetchChildren($page['page_id'])->toArray(); // Get children
			if( !empty($children) )
			{
				$link	.='<ul>';
				foreach ($children as $child)
					if ($child['page_published'])
						$link .= $this->_genLink($child, $this->_menuChildClass);
				$link	.='</ul>';	
			}
					
			/* Close the list */
			$link		.= 	'</li>';
			
			return $link;
		}
	}

	/**
	 * Returns valid href from the action info
	 * ---------------------------------------
	 */
	private function _makeHref($action, $controller, $module = 'default')
	{
		return Zend_Controller_Front::getInstance()->getBaseUrl() . 
							'/'	. $module . '/' . $controller . '/' . $action;	
	}

	private function _genLinkXml($menu, $cssClass = null)
	{
		if (is_array($menu))
		{
			$link		= 	'<li><a href="';
			/* Check if we have href or mvc*/
			if (!empty($menu['page_controller']))
			{
				// use mvc values
				$link .= $this->_makeHref($menu['page_action'], $menu['page_controller'], $menu['page_module']);
			}
			else // use a null href '#'
				$link .= '#';
			
			/* Add the title if it exists */
			$link .= '" ' . (!empty($menu['page_title'])? ' title="'. $menu['page_title'] . '" ': '');
			
			/* Add the class if there exist */
			$link .= (isset($cssClass)? ' class="'. $cssClass. '" ': '') . '>';
				$link		.= $menu['page_name']. '</a>';
			
			
			/* Append the child menu */
			if(array_key_exists('menu', $menu))
			{
				$link	.='<ul>';
				foreach ($menu['menu'] as $child)
					if ($child['page_published'])
						$link .= $this->_genLinkXml($child, $this->_menuChildClass);
				$link	.='</ul>';	
			}
					
			/* Close the list */
			$link		.= 	'</li>';
			
			return $link;
		}
	}

	/**
	 * Wrties the menu list into the provided xml 
	 ////////////////////////////////////////////////////////////// 
	 */
	public function writeMenu2xml($uri = null)
	{
		$uri	= (null == $uri? /*use default file*/
								APPLICATION_PATH . '/configs/'. md5('menus').'.php' : $uri );
		if(!file_exists($uri) && !is_writable($uri))
		{	return false;	}
		
		$xmlWriter 	= new XMLWriter();
		$xmlWriter->openUri($uri);
		$xmlWriter->startDocument('1.0" encoding="UTF-8');	/*	Write the beginning */
		$xmlWriter->setIndent(8);
		$xmlWriter->startElement('menuxml');
			$xmlWriter->startElement('page-menus');
		
		$topMenus	= $this->getTopMenus();
		/* Write the top menus */
		$this->_writeArray($xmlWriter, $topMenus);
		
			$xmlWriter->endElement(); // page-menus
		$xmlWriter->endElement(); // menuxml
		return $xmlWriter->endDocument();						/* Write the end */
		
	}
	
	/** This method is called in a recurssive manner 
	 * 	to add each array
	 * @param array menus
	 * */
	private	function _writeArray($xmlWriter, $menus)
	{
		
		foreach ($menus as $menu)
		{
			$xmlWriter->startElement('menu');
			foreach ($menu as $key => $value)
			{
				$xmlWriter->writeElement($key, $value);
			}
			/* Append the child menu */
			$children	= $this->fetchChildren($menu['page_id'])->toArray(); // Get children
			if( !empty($children))
			{
				$this->_writeArray($xmlWriter, $children);	
			}
			
			$xmlWriter->endElement(); // menu
		}
	}
	
	public function fetchChildren($id)
	{
		$field =  'page_parent_id'; 
		$value = $id;
				
		/* Read all by the selected query */
		return $this->getAdapter()->readAll($field . ' = ?', $value);
	}
	
	private function _getById($id, $field = null)
	{
		/* Build the query for readDB */
		if (empty($field))
			$field = 'page_id';
			
		$select =  $this->_select->from(array('p' => 'pages_table' ))
					  			      ->where($field . ' = ? ' , $id);
		
		return $this->_select->query($select); 
	}
	
	private function _readDB($query)
	{
		/* Execute the query */
		if (!$query instanceof Zend_Db_Select)
		{
			// CHANGE LATER!!
			return;
		}
		return $this->_select->query($query);
	}			
}