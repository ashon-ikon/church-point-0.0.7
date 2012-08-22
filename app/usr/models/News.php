<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Jan 24, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_News
{
	/**
	 * Table name
	 */
	protected	$_table_name 	= 'news_table';
	
	protected	$_db_table 		= null;
	

	/**
	 * @var	int	group id
	 */
	protected	$_content_group_id	= null;
	
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	
	private function __construct()
	{
		$contentGroup	= Point_Model_ContentGroups::getInstance();
		
		$this->_content_group_id	= $contentGroup->getGroupIdByKeyword('News');
		
		if (!is_numeric($this->_content_group_id))
			throw new Exception('No valid group id found for News');		 
	}
		
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
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
	 * Get content group id
	 * 
	 * This retrieves news content group 
	 */
	public function getContentGroupId()
	{
		if (null === $this->_content_group_id)
		{
			$contentGroups	= Point_Model_ContentGroups::getInstance();
			
			$this->_content_group_id = $contentGroups->getGroupIdByKeyword('News');
			
			if (!$this->_content_group_id)
				throw new Exception('Unable to get content group id');
		}
		
		return $this->_content_group_id;
	}
	
	
	/**
	 * Set content group id
	 * 
	 * This will change the group content id
	 */
	public function setContentGroupId($id)
	{
		/**
		 * Check if group exists then only modify it!!!
		 */	
		$db = $this->getDbTable();
		
		if ($db->select()->where('group_id = ? ', $id)->query()->fetch())
		{
			/* The group exits already so you can add it */
			$this->_content_group_id = $id;
		}
	}
 
	    				
	public function updateNews($news_id , $title ,$content ,$news_desc , $tags = array() , $published = true )
	{
		if ('' === $content)
			throw new Exception('Content cannot be empty');
			
		if (!is_numeric($news_id))
			throw new Exception('Invalid news id supplied: ' . $news_id);
			
		$user 	= Point_Model_User::getInstance();
		array_walk($tags, 'cleanReplace');
		array_walk($tags, 'cleanSlashes');
		
		
		/**
		 * ++++++++++++++++++++++++++++++++++++++++++
		 * Clean the entire inputs from magic-slash
		 */
		$title				= cleanSlashes($title); 
		$content			= cleanSlashes($content); 
		$enws_desc			= cleanSlashes($news_desc); 
		
		
		/* Create SEO Friendly title */
		$seo_title = makeSeoString($title);

				
		/* Create the recordset */	
		$data 	= array(
				'news_content' 			=> $content,
				'news_desc' 			=> $news_desc,
				'news_title' 			=> $title,
				'news_seo_title' 		=> $seo_title,
				'news_tags' 				=> rtrim(implode(',',$tags),','),
				'news_published'			=> $published ? 1 : 0
				
			);
		$where = $this->getDbTable()->getAdapter()->quoteInto('news_id = ?', $news_id);
		
		/* Try to update */
		$db 	= $this->getDbTable();
		
		return  $db->update($data, $where);
		
	}
	
	public function remove( $news_id )
	{
		$db		= $this->getDbTable();
		
		$where = $this->getDbTable()->getAdapter()->quoteInto('news_id = ?', $news_id);
		
		return $db->delete($where);
	}
	
	public function addNew($title, $content , $content_desc, $tags = array() , $author_id = null, $published = true )
	{
		if ('' === $content)
			throw new Exception('Content cannot be empty');
			
		$user 	= Point_Model_User::getInstance();
		array_walk($tags, 'cleanReplace');
		array_walk($tags, 'cleanSlashes');
		
		
		/**
		 * ++++++++++++++++++++++++++++++++++++++++++
		 * Clean the entire inputs from magic-slash
		 */
		$title				= cleanSlashes($title); 
		$content			= cleanSlashes($content); 
		$content_desc		= cleanSlashes($content_desc); 
		
		
		/* Create SEO Friendly title */
		$seo_title = makeSeoString($title);

		if (null === $author_id)
			$author_id = $user->getUserId();
			
		$data 	= array(
				'news_content' 			=> $content,
				'news_desc' 			=> $content_desc,
				'news_title' 			=> $title,
				'news_seo_title' 		=> $seo_title,
				'news_tags' 			=> rtrim(implode(',',$tags),','),
				'news_author_id'		=> $author_id,
				'news_published'		=> $published ? 1 : 0,
				'news_date'				=> new Zend_Db_Expr('NOW()')
			);
		$insert_id = $this->getDbTable()->insert($data);
		
		return $insert_id;
	}
	
	/**
	 * Retrieve news by params
	 * @params
	 * 		file	=> '2011/12/23/blessings-of-the-Lord'
	 * 		like	=> 'year-mm-dd%'
	 * 		orderby	=> 'news_date DESC'
	 */
	public function getNews($params, $precise = false)
	{
		$like 	= $precise 	= $year = $month	= $day	= $title	=  $file  	= null;
    	
		if(array_key_exists('file', $params))
			$file 			= $params['file'];

		// 	Extract needed params...
		//  foo/bar?f=2011/12/23/blessings-of-the-Lord
		if (null !== $file )
		{			
			$file	= array_map('trim', explode('/', $file));
			if (count($file) == 4)
			{
				$precise 	= true;
				$year		= intval($file[0]);
				$month		= intval($file[1]);
				$day		= intval($file[2]);
				$title		= strval($file[3]);
				$title		= strtolower( substr($title, 0 , strpos($title, '-')) );
				
				//$time		= date(strtotime(implode('-',array($year, $month, $day))) + (6))
				$like = $this->getDbTable()->getAdapter()->quoteInto('news_date BETWEEN \'1990-01-01\' AND  ?', implode('-',array($year, $month, $day+1)));
				 
			}
			
		}
		else
			/* We need a valid news  so return*/
			return; 
			
					
		/* any of the parameter is enough to test!! */
		if (!$year)
		{
				$like = $this->getDbTable()->getAdapter()->quoteInto('news_date BETWEEN \'1990-01-01\' AND ?', date('Y-m-d') );
		}
		
		if (!$like)
			$like = $this->getDbTable()->getAdapter()->quoteInto('news_date BETWEEN \'1990-01-01\' AND ? ', date('Y-m-d') );

		if (!array_key_exists('orderby', $params))
			$params['orderby'] = 'news_date DESC';
		
		
		
		/* Retrict content to group membership */
		
		
		$query = $this->getDbTable()->getAdapter()
					  ->select()->from(array('nt'=> 'news_table'))
					  ->joinInner(array('ut' => 'users_table'), 'nt.news_author_id = ut.user_id');/* Add news author */
		
		if($year)
			$query->where($like);
		
		$query->order($params['orderby'])->limit(1);
//		echo $query; exit;
		
		$result = $query->query()->fetch();
		
		/* Sanitize result */
		unset_key($result, 'password');
		unset_key($result, 'password_reset');
		unset_key($result, 'active');
		unset_key($result, 'reg_date');
		
		
			
		return $result;
	}
	
	
	/**
	 * This screens the news to 
	 */
	protected function _privilegeParser($news = array())
	{
		if (is_array($news))
		{
			if (!array_key_exists('news_group_id', $news))
				$group_id 	= 1;
			else
				$group_id 	= $news['news_group_id'];
				
			
			
			/* Check group and get privacy level */
			$group_info		= $this->getDbTable()->getAdapter()
								   ->select()->from(array('gt' => 'groups_table'))
								   ->where('group_id = ? ', $group_id)
								   ->query()->fetch();
								   
			$userGroups		   	= Point_Model_User::getInstance()->getUserGroups();
			
			/* Check privacy level of this group */
			$bMember 		= false;
			
			if ($group_info['group_privacy_level'])
			{
				/* check if viewer is member */
				foreach($userGroups as $group)
				{
					if ($group_info['group_id'] == $group['group_id'])
					{
						/* Viewer is member */
						$bMember = true;						
						break;
					}
					
				}		
				
			}
			else
				$bMember = true;
			
			/* Viewer is member */	
			if ($bMember)
				return $news;
		} 
				
	}
	
	/**
	 * Retirieve news by ID
	 */
	public function getNewsById( $id )
	{
		if(is_numeric($id))
		{
			$order_by	= 'news_date DESC';
			
			$query = $this->getDbTable()->getAdapter()
				->select()->from(array('nt'=> 'news_table'))
				->joinInner(array('ut' => 'users_table'), 'nt.news_author_id = ut.user_id') /* Add author */
				->where('news_id = ?', $id)->order($order_by);
						
			$news 	= $query->query()->fetch();
			
			
			unset_key($news, 'password');
			unset_key($news, 'password_reset');
			unset_key($news, 'active');
			unset_key($news, 'reg_date');

			return $news;
		}
	}
	
	/*
	 * Retirieve news by date
	 */
	public function getNewsByPeriod( $start_date, $end_date, $order = 'DESC')
	{
		if (isValidDateFormat($start_date) && isValidDateFormat($end_date))
		{
			$order_by	= 'news_date ' . $order;
			
			$query = $this->getDbTable()->getAdapter()
				->select()->from(array('nt'=> 'news_table'))
				->joinInner(array('ut' => 'users_table'), 'nt.news_author_id = ut.user_id') /* Add author */
				->where('news_date >= ?', $start_date)->where('news_date <= ?' , $end_date)->order($order_by);
						
			$news 	= $query->query()->fetchAll();
			
			if (!empty($news) && is_array($news))
			{
				foreach ($news as &$this_news)
				{
					unset_key($this_news, 'password');
					unset_key($this_news, 'password_reset');
					unset_key($this_news, 'active');
					unset_key($this_news, 'reg_date');
				}
			}
			
			return $news;
		}
	}
	
	/**
	 * This returns a permanent link url based on ID
	 * 
	 * @param int | array news
	 * 
	 */
	public function getNewsUrl($news)
	{
		if (is_numeric($news))
		{
			$news 	= $this->getNewsById($news);
			// ?f=2011/12/23/blessings-of-the-Lord
			$url 		= '?n=';
			$url		.= ''.	date('Y/m/d', strtotime($news['news_date'])) . '/' .$news['news_seo_title'];
			
			return  $url;
		}
		
	}
	

	/**
	 * This gets the latest news in the database
	 */
	public function getLatestUrl()
	{
		$db_table 	= $this->getDbTable();
		
		$latest 	= $db_table->select()->order('news_date DESC')
					  		   ->limit(1)->query()->fetch();
		
		$news_id = $latest['news_id'];
		
		return $this->getNewsUrl($news_id);  
		
	}
	
	/**
	 * This retrieves the topmost news
	 */
	public function getTopNews($count = 6)
	{
		
		$top_news 	= $this->getAllNews(0, $count);
		
//		$order_by	= 'news_date DESC';
//		
//		$query = $this->getDbTable()->getAdapter()
//						->select()->from(array('nt'=> 'news_table'))
//						->joinInner(array('ut' => 'users_table'), 'nt.news_author_id = ut.user_id') /* Add author */
//						->order($order_by)->limit((int)$count);
//		
//		$top_news 	= $query->query()->fetchAll();
//		
//		/* remove unwanted info from result */
//		foreach ($top_news as &$news)
//		{
//			
//			unset_key($news, 'password');
//			unset_key($news, 'password_reset');
//			unset_key($news, 'active');
//			unset_key($news, 'reg_date');
//		}
		return $top_news;
		
	}
	
	/**
	 * This retrieves the selected number of news
	 */
	public function getAllNews($start = 0, $amount = 10, $order = 'DESC')
	{
		/* Db Object */
		$db		= $this->getDbTable();
		
		$order_by	= 'news_date ' . $order;
		
		$query = $db->getAdapter()->select()->from(array('nt'=> 'news_table'))
								  ->order('news_date ' .$order)->limit($amount, $start)
								  ->joinInner(array('ut' => 'users_table'), 'nt.news_author_id = ut.user_id') /* Add author */
								  ->order($order_by)->limit((int)$amount, (int)$start);
		
		$got_news 	= $query->query()->fetchAll();
		
		/* remove unwanted info from result */
		foreach ($got_news as &$news)
		{
			
			unset_key($news, 'password');
			unset_key($news, 'password_reset');
			unset_key($news, 'active');
			unset_key($news, 'reg_date');
		}
			
		if ($got_news)
			return $got_news;
	}
	
	/**
	 * This retrieves the latest News
	 */
	public function getLatestNews()
	{
		
		$order_by	= 'news_date DESC';
		
		$query = $this->getDbTable()->select()
						/*->getAdapter()
						->select()->from(array('nt'=> 'news_table'))
						->joinInner(array('ut' => 'users_table'), 'at.news_author_id = ut.user_id') /* Add author */
						->order($order_by)->limit(1);
		
		$sermons 	= $query->query()->fetch();
		
		
		
		return $sermons;
		
	}	
	
	/**
	 * Checks if user is priviledge to view news and what level.
	 * 
	 * returns false or priviledge level.
	 */
	public function getPriviledge($news_id, $user_id)
	{
		
		
		if (is_numeric($news_id) && is_numeric($user_id))
		{
			/* Retrieve the news */
			if($news 	= $this->getNewsById($news_id ))
			{
				
				$db 			= new Zend_Db_Table('page_content_group_members_table');
				
				$result 		= $db->select()->where('user_id = ?', $user_id)->where('page_content_group_id = ?', $this->_content_group_id)->query()->fetch();
				
				/* check if user belongs to the news group .. */								
				if ($result)
				{
					return $result['access_right'];
				}

			}
			
		}
		return Point_Model_User::GROUP_GUEST;
	}
}