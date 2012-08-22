<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 12, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Acl_Views	extends Zend_Acl
{
	/**
	 * =========================
	 * 	USER VIEW ROLES / LEVELS
	 */
	const		ROLE_GUEST		= 'guest';
	const		ROLE_MEMBER		= 'member';
	const		ROLE_AMIN		= 'admin';
	const		ROLE_SUPERADMIN	= 'superadmin';
	
	
	const		ACCESS_GUEST		= 'guest';
	const		ACCESS_MEMBER		= 'member';
	const		ACCESS_AMIN			= 'admin';
	const		ACCESS_SUPERADMIN	= 'superadmin';
	
	
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
	/* Prevent access to 'new Point_Acl_Views' by declaring private constructor */
	private function __construct()
	{
		// Set up the access list
		$this->setRoles()->setResources()->setPrivilages();
	}

	//user_role | enum('superadmin','admin','member')
	
	public function setRoles()
	{
		$this->addRole(new Zend_Acl_Role('guest')); 			// Stands alone
		$this->addRole(new Zend_Acl_Role('member'), 'guest');	// Inherits from guest
		$this->addRole(new Zend_Acl_Role('admin', 'member'));	// Inherits from member
		$this->addRole(new Zend_Acl_Role('superadmin'));		// Stands alone
		return $this;
	}

	public function setResources()
	{
		//TODO: Try to change to foreach(Zend_Controler_Front::GetAllControllers())... instead.
		
		$menus_obj		= Point_Object_Menus::getInstance();
		
		$top_pages		= $menus_obj->getTopMenus();
		
//		echo '<pre>', print_r($top_pages, true),'</pre>';exit;

		/*--------------------------
		 * Basic ones!!!
		 * -------------------------
		 */
		$this->addResource(new Zend_Acl_Resource('index'));
		$this->addResource(new Zend_Acl_Resource('error'));
		$this->addResource(new Zend_Acl_Resource('churchpoint'));
		$this->addResource(new Zend_Acl_Resource('administration'));
		
		$this->addResource(new Zend_Acl_Resource('xpage'));
		$this->addResource(new Zend_Acl_Resource('rteam'));
		$this->addResource(new Zend_Acl_Resource('articles'));
		$this->addResource(new Zend_Acl_Resource('events'));
		$this->addResource(new Zend_Acl_Resource('biblestudy'));
		$this->addResource(new Zend_Acl_Resource('c-groups'));
		$this->addResource(new Zend_Acl_Resource('dpoint'));
		$this->addResource(new Zend_Acl_Resource('account'));
		$this->addResource(new Zend_Acl_Resource('user'));
		$this->addResource(new Zend_Acl_Resource('images'));
		$this->addResource(new Zend_Acl_Resource('photonews'));
		$this->addResource(new Zend_Acl_Resource('ajax'));
		$this->addResource(new Zend_Acl_Resource('rss'));
		$this->addResource(new Zend_Acl_Resource('content-groups'));
		$this->addResource(new Zend_Acl_Resource('messages'));
		$this->addResource(new Zend_Acl_Resource('sermons'));
		$this->addResource(new Zend_Acl_Resource('slider'));
		$this->addResource(new Zend_Acl_Resource('word'));
		
		/*
		 * =========================
		 * Others
		 * -------------------------
		 */
		foreach ($top_pages as $top_page)
		{
			if ($top_page['page_published'] && !$this->has($top_page['page_controller']))
				$this->addResource(new Zend_Acl_Resource($top_page['page_controller']));
		}
		
		/*
		$this->addResource(new Zend_Acl_Resource('about'));
		$this->addResource(new Zend_Acl_Resource('account'));
		$this->addResource(new Zend_Acl_Resource('app-config'));
		$this->addResource(new Zend_Acl_Resource('ajax'));
		$this->addResource(new Zend_Acl_Resource('articles'));
		$this->addResource(new Zend_Acl_Resource('c-groups'));
		$this->addResource(new Zend_Acl_Resource('content-groups'));
		$this->addResource(new Zend_Acl_Resource('dpoint'));
		$this->addResource(new Zend_Acl_Resource('error'));
		$this->addResource(new Zend_Acl_Resource('events'));
		$this->addResource(new Zend_Acl_Resource('index'));
		$this->addResource(new Zend_Acl_Resource('images'));
		$this->addResource(new Zend_Acl_Resource('messages'));
		$this->addResource(new Zend_Acl_Resource('rteam'));
		$this->addResource(new Zend_Acl_Resource('sermons'));
		$this->addResource(new Zend_Acl_Resource('user'));
		$this->addResource(new Zend_Acl_Resource('xpage'));
		$this->addResource(new Zend_Acl_Resource('word'));
		*/
		return $this;
	}
	
	/**
	 * Set up the Access List here (Dynamically)!!!
	 */
	public function setPrivilages()
	{
//		/* Get roles from pages
//		 * /////////////////////////////*/
//		 $topPages = Point_Model_Page::getInstance()->getTopMenus();
//		 		
//		/*
//		 * For each top 
//		 * 	* add publicly assessible ones to guest,
//		 * 
//		 * 	* add members visible to member
//		 * 
//		 *  * add admin visible to admin & superuser
//		 * /////////////////////////////////////
//		 * */
//		if (is_array($topPages))
//		{
//			foreach ($topPages as $topPage)
//			{
//				
//			}
//		}
		
		/* General Rules */
		$this->allow('guest', array('index'), array('index'));
		$this->allow('guest', array('error', 'rss', 'churchpoint'));
		$this->allow('guest', array('about','articles' ,'index','error', 'xpage', 'rteam', 'word', 'slider', 'photonews' , 'biblestudy'));
		
		/* Specific to Controller actions */
		$this->allow('guest', array('account'), array('login','logout','register', 'activate','forgotpwd','resetpassword'));
		$this->allow('guest', 'ajax',	array('index', 'request'));
		
		
		$this->allow('member',array('account','c-groups','content-groups','events','messages','user','dpoint', 'sermons','index','images'));
		$this->allow('admin');
		$this->allow('superadmin', array('administration') );
		
		/* Denies */
		$this->deny('member' , 'messages', array('edit','add'));
		
		return $this;
	}
}