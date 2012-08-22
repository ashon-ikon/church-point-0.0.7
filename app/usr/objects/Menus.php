<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	Point_Object_Menu
 * Created by ashon on Feb 12, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 * 
 * Depends on Point_Model_ContentGroups
 * Depends on Point_Acl_Views
 */
/**
 * This class handles the retrieval and writing of menus to database
 * 
 * Menus are retrieved and created based on user groups found in
 * 	>>	Point_Model_ContentGroups
 * 
 * Menus are retrieved and stored from 
 *  >> 	pages_table 
 * DB table
 * 
 */
class Point_Object_Menus
{
	
	/**
	 * Menus Table
	 */
	protected			$_db_table		= null;
	/**
	
	/**
	 * Groups Table name
	 */
	protected			$_table_name 	= 'pages_table';
	
	
	protected			$_top_menu_id		= TOP_PAGE_ID;
	
	/**
	 * This saves us from going to the DB to often
	 * @var array Stores all content groups within application
	 */
	protected			$_allContentGroups = null;
	
	/**
	 * View Renderer
	 */
	protected			$_viewObject		= null;
	
	/**
	 * Singleton instance holder
	 */
	private static		$_instance;
	
	
	private function __construct()
	{
		 
	}
	
	/**
	 * Get the singleton instance to Menu Object
	 */	
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
	/**
	 * Get Database table
	 * 
	 * This method retrieves the 
	 */
	public function getDbTable()
	{
		if (null === $this->_db_table)
		{
			$db	= new Point_Model_DbTable_DbCore($this->_table_name);
			$this->_db_table = $db->getDBTable();
		}
		return $this->_db_table;
	}
	
	
	
	/**
	 * Gets all top menus
	 * 
	 * This retrieves all the top menus based on the top page index (Usually 0)
	 * 
	 * @param	Integer	Optional: Top page id
	 * 
	 * @return	array	top menus
	 * 
	 * throw Exceptions
	 */
	public function getTopMenus( $top_page_id = null)
	{
		$user		= Point_Model_User::getInstance();
		$user_id 	= $user->getUserId();
		
		if (null === $top_page_id)
		{
			$top_page_id = $this->_top_menu_id;
		}
		
		
		$db = $this->getDbTable();
		
		$top_menus	= $db->select()->where('page_parent_id = ?', $top_page_id , 'INT')
						 ->order('page_rank')
		   				 ->query()->fetchAll();
		
		/**
		 * --------------------------------------------------- 
		 * Retrieve all menu that have the top page parent id 
		 * 
		 * Limit to user's group views
		 */
		$userContentGroups 	= Point_Model_ContentGroups::getInstance()->getUserGroups($user_id);
		$AllContentGroups 	= $this->_getContentGroups();
		
		$screened_menus 	= array();
		
		/*
		 *  Scan through all menus and add them based on 
		 * their groups privacy level and members association 
		 */
		foreach($top_menus as $top_menu)
		{
			/* Ensure we are dealing with a published page */
			if (!$top_menu['page_published'])
				continue; // Skip this menu
				
			/* Get info of this menu's group */
			foreach($AllContentGroups as $contentGroup)
			{
				
				/* Check if we have a match */
				if ($contentGroup['group_id'] == $top_menu['page_group_id'] )
				{
					$menu_group_data	= $contentGroup;
					
					/* Check to see if user belongs to this group */
					
					/* User may see this menu if privacy level is not 'privileged' 
					 * Use user's role as well.
					 */
					
					if( $contentGroup['group_privacy_level'] == Point_Model_ContentGroups::ACCESS_PUBLIC )
					{
						if (!$user->isLoggedIn() && $top_menu['page_access_level'] != Point_Acl_Views::ACCESS_GUEST)
						{
							// Ignore
							break;
						}
						else
							$screened_menus[] = $top_menu;
					}
					else
					{
						
						foreach($userContentGroups as $userContentGroup)
						{
							if ($menu_group_data['group_id'] == $userContentGroup['group_id'])
							{
								/* We have found a group the user belongs to
								 * let's add this group to user's menus ;) 
								 */
								$screened_menus[] = $top_menu;
								break;
							}
						}
					}
					
					/* No need to continue loop */
					break;
				}
			}
		}
		
		
		if (!empty($screened_menus))
			return $screened_menus;
	}

	/**
	 * Gets a complete list of all menu that user can see
	 * 
	 * This calls the getTopMenus to get the topmost menus
	 * and uses their IDs as parent_key to other menus
	 * 
	 * @param	Boolean	To return result as array
	 * @param	Array	Parent and child tags
	 * 
	 * @return	array	nested menus
	 */
	public function getMenus($asArray = false, array $options = array())
	{
		$top_menus = $this->getTopMenus();
		
		$all_menus = array();
		/* Loop through each of the menus and get children
		 * 
		 * Each child must be denoted visible (as per 'page_access_level') 
		 */
		foreach($top_menus as $top_menu)
		{
			/* for each top menu let's retrieve their children and grand children */
			$this->_addChildMenus($top_menu);
			
			$all_menus[] = $top_menu;
		}
		
		
		if ($asArray)
			return $all_menus;
		else
		{
			
			/* extract the options */
			
			$parent_tag 	= getArrayVar($options, 'parent', 'ul');
			
			$child_tag 		= getArrayVar($options, 'child', 'li' );

			$url 			= getArrayVar($options, 'url', null );
				 
				 
					
			$html = null;
			/* Perform the html tags */
			foreach($all_menus as $menu)
			{
				$html 	.= $this->_makeMenu($menu, $parent_tag, $child_tag, $url) . "\n";
			}
			$html		= wrapHtml($html, $parent_tag);
			
			
			/* Return the prepared menu */
			return $html;
		}
	}	
	
	/**
	 * This converts array to menu html item
	 * 
	 * @param	Array	Menu item
	 * @param	String	Parent tag
	 * @param	String	Child tag
	 * 
	 * @return 	String	Unordered list of items
	 */
	protected function _makeMenu($menu, $parent_tag, $child_tag, $url = null)
	{
		$view 	= $this->_getView();
		$ret	= null;
		$active_class	= '';
		if (null !== $url)
		{
			$menu_url	= $menu['page_controller'] . '/' . $menu['page_action'];
//			
			if ($url == $menu_url)
			{
				$active_class	= 'active';
//				echo 'URL: ', $url, ' MENU_URL: ', $menu_url, '<br />';	
			}
			
		}
		
		
		$href	= $view->fullUrl(array('controller' => $menu['page_controller'], 'action' => $menu['page_action']), null , true);
		$title	= $view->escape($menu['page_title']);
		
		$menu_name	= wrapHtml($view->escape($menu['page_name']), 'span');
		
		$options	= array('href' => $href, 'title' => $title);
		
		if ($active_class)
		{
			$options['class'] = $active_class;
		}
		
		$link		= wrapHtml($menu_name, 'a', $options);
		
		if ($child_menus = getArrayVar($menu, 'children'))
		{
			$ret	.= '<' . $child_tag  . '>';
			$ret	.= $link;
			$ret 	.= '<' . $parent_tag . '>';
			
			/* Append each child */
			foreach($child_menus as $child_menu)
			{
				$ret.= $this->_makeMenu($child_menu, $parent_tag, $child_tag, $url). "\n";	
			}
			
			$ret	.= '</'. $parent_tag .'>';
			$ret	.= '</'. $child_tag .'>';
		}
		else
			$ret	.= wrapHtml($link, $child_tag);
			
		
		return $ret;
		
	}
	
	
	
	/**
	 * method	getView()
	 * @return ZendView view object that can be rendered into OR null
	 */
	protected function _getView()
	{	
		/* Rase an alarm if we don't have a valid view */
		if (null === $this->_viewObject)
    	{
    		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
	    	if (!$this->_viewObject = $viewRenderer->view)
	    	{
		    	throw new Exception ('Call to empty view!');
	    	}
	    	
    	}
		
		return $this->_viewObject;	
	}
	/**
	 * This gets all the child of the current menu
	 */
	protected function _getChildren($menu_id)
	{
		$db				= $this->getDbTable();
		
		$user			= Point_Model_User::getInstance();
		$user_id		= $user->getUserId();
		
		$childrenMenu 	= $db->select()
							 ->where('page_parent_id =  ? '		, $menu_id)
							 ->where('page_published <> ?'	, 0)
							 ->order('page_rank')
							 ->query()->fetchAll();
		
		if(!empty($childrenMenu))
		{
			/* Do the neccessary screening ! */
			
			$userContentGroups 	= Point_Model_ContentGroups::getInstance()->getUserGroups($user_id);
			$AllContentGroups 	= $this->_getContentGroups();
			
			$screened_menus 	= array();
			/*
			 *  Scan through all menus and add them based on 
			 * their groups privacy level and members association 
			 */
			foreach($childrenMenu as $key => $childMenu)
			{
				/* Get info of this menu's group */
				foreach($AllContentGroups as $contentGroup)
				{
					/* Check if we have a match */
					if ($contentGroup['group_id'] == $childMenu['page_group_id'] )
					{
						$menu_group_data	= $contentGroup;
						
						/* Check to see if user belongs to this group */
						
						/* User may see this menu if privacy level is not 'privileged' 
						 * Use user's role as well.
						 */
						if( $contentGroup['group_privacy_level'] == Point_Model_ContentGroups::ACCESS_PUBLIC )
						{
							if (!$user->isLoggedIn() && $childMenu['page_access_level'] != Point_Acl_Views::ACCESS_GUEST)
							{
								// Ignore
								break;
							}
							else
								$screened_menus[] = $childMenu;
						}
						else
						{
							foreach($userContentGroups as $userContentGroup)
							{
								if ($menu_group_data['group_id'] == $userContentGroup['group_id'])
								{
									/* We have found a group the user belongs to
									 * let's add this group to user's menus ;) 
									 */
									$screened_menus[] = $childMenu;
									break;
								}
							}
						}
						
						/* No need to continue loop */
						break;
					}
				}
				
			}
					
		
			return $screened_menus;
		}
			
	}
	
	/**
	 * This appends all the child menus recursively
	 * 
	 * @param array children menus
	 * @return	array	nested child menus
	 */
	protected function _addChildMenus(&$menu)
	{
		if (!is_array($menu))
			throw new Exception('Add child menu requires array!');
		$children = $this->_getChildren($menu['page_id']);
		
		if (!empty($children))
		{
			/* Check if any children has grandchildren */
			foreach ($children as &$child)
				$this->_addChildMenus($child);
			$menu ['children'] = $children; 
		}
	}
	
	/**
	 * Gets all content Groups from Point_Model_ContentGroups
	 * @return	Array of all groups
	 */
	protected function _getContentGroups()
	{
		if (null === $this->_allContentGroups)
		{
			$this->_allContentGroups = Point_Model_ContentGroups::getInstance()->getAllGroups();
		}
		
		return $this->_allContentGroups;
	}
}