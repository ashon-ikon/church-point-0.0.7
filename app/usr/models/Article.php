<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Jan 24, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_Article
{
	/**
	 * Table name
	 */
	protected	$_table_name 	= 'articles_table';
	
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
		
		$this->_content_group_id	= $contentGroup->getGroupIdByKeyword('Articles');
		
		if (!is_numeric($this->_content_group_id))
			throw new Exception('No valid group id found for Articles');	 
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
	
	public function updateArticle($content , $title, $article_desc = null ,$tags = array() , $article_id , $published = true ,$group_id = 1 /* 1 => public */)
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
		$article_desc		= cleanSlashes($article_desc); 
		
		
		/* Create SEO Friendly title */
		
		$seo_title = makeSeoString($title);
				
		/* Create the recordset */	
		$data 	= array(
				'article_content' 			=> $content,
				'article_title' 			=> $title,
				'article_seo_title' 		=> $seo_title,
				'article_desc' 				=> $article_desc,
				'article_tags' 				=> rtrim(implode(',',$tags),','),
				'article_published'			=> $published ? 1 : 0,
				'article_group_id'			=> $group_id
			);
		$where = $this->getDbTable()->getAdapter()->quoteInto('article_id = ?', $article_id);
		
		/* Try to update */
		$db 	= $this->getDbTable();
		
		return  $db->update($data, $where);
		
	}
	
	/**
	 * Add the New Article
	 */
	public function addNew($content , $title, $article_desc = null, $tags = array() , $author_id = null, $published = true ,$group_id = 1 /* 1 => public */)
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
		$article_desc		= cleanSlashes($article_desc); 
		
		
		/* Create SEO Friendly title */
		$seo_title = makeSeoString($title);
		
		if (null === $author_id)
			$author_id = $user->getUserId();
			
		$data 	= array(
				'article_content' 			=> $content,
				'article_title' 			=> $title,
				'article_seo_title' 		=> $seo_title,
				'article_desc' 				=> $article_desc,
				'article_tags' 				=> rtrim(implode(',',$tags),','),
				'article_author_id'			=> $author_id,
				'article_published'			=> $published ? 1 : 0,
				'article_group_id'			=> $group_id,
				'article_date'				=> new Zend_Db_Expr('NOW()')
			);
		$insert_id = $this->getDbTable()->insert($data);
		
		return $insert_id;
	}
	
	/**
	 * This removes a article from database
	 * 
	 * @param	int	$article_id	The id of article
	 */
	public function remove( $article_id )
	{
		$db		= $this->getDbTable();
		
		$where = $this->getDbTable()->getAdapter()->quoteInto('article_id = ?', $article_id);
		
		return $db->delete($where);
	}
	
	/**
	 * Retrieve article by params
	 * @params
	 * 		file	=> '2011/12/23/blessings-of-the-Lord'
	 * 		like	=> 'year-mm-dd%'
	 * 		orderby	=> 'article_date DESC'
	 */
	public function getArticle($params, $precise = false)
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
				$like = $this->getDbTable()->getAdapter()->quoteInto('article_date BETWEEN \'1990-01-01\' AND  ?', implode('-',array($year, $month, $day+1)));
				 
			}
			
		}
		else
			/* We need a valid article  so return*/
			return; 
			
					
		/* any of the parameter is enough to test!! */
		if (!$year)
		{
				$like = $this->getDbTable()->getAdapter()->quoteInto('article_date BETWEEN \'1990-01-01\' AND ?', date('Y-m-d') );
		}
		
		if (!$like)
			$like = $this->getDbTable()->getAdapter()->quoteInto('article_date BETWEEN \'1990-01-01\' AND ? ', date('Y-m-d') );

		if (!array_key_exists('orderby', $params))
			$params['orderby'] = 'article_date DESC';
		
		
		
		/* Retrict content to group membership */
		
		
		$query = $this->getDbTable()->getAdapter()
					  ->select()->from(array('at'=> 'articles_table'))
					  ->joinInner(array('ut' => 'users_table'), 'at.article_author_id = ut.user_id');/* Add article author */
		
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
		
		
		$article = $this->_privilegeParser($result);
		
		
//		
//		$user = Point_Model_User::getInstance();
//		$author = $user->getUserById($article['article_author_id']);
//		
//		if ($author) $article['']
			
		return $result;
	}
	
	/**
	 * This gets all articles within a range
	 * @abstract	Gets all articles from within range specified
	 * 
	 * @param int 	$start	beginning of record
	 * @param int 	$amount	upper limit of record
	 * @param string 	$order	ordering clause
	 * 
	 * @return array List of articles
	 * 
	 */
	public function getArticles( $start = 0, $amount = 10, $order = 'DESC')
	{
		/* Db Object */
		$db		= $this->getDbTable();
		
		$query = $db->select()->order('article_date ' .$order)->limit($amount, $start);
						
		$articles 	= $query->query()->fetchAll();
			
		if ($articles)
			return $articles;
	}
	
	
	/**
	 * This screens the article to 
	 */
	protected function _privilegeParser($article = array())
	{
		if (is_array($article))
		{
			if (!array_key_exists('article_group_id', $article))
				$group_id 	= 1;
			else
				$group_id 	= $article['article_group_id'];
				
			
			
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
				return $article;
		} 
				
	}

	/**
	 * Get content group id
	 * 
	 * This retrieves articles content group 
	 */
	public function getContentGroupId()
	{
		if (null === $this->_content_group_id)
		{
			$contentGroups	= Point_Model_ContentGroups::getInstance();
			
			$this->_content_group_id = $contentGroups->getGroupIdByKeyword('Articles');
			
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
	
	/**
	 * Retirieve article by ID
	 */
	public function getArticleById($id, $check_access = true)
	{
		if(is_numeric($id))
		{
			$order_by	= 'article_date DESC';
			
			$query = $this->getDbTable()->getAdapter()
				->select()->from(array('at'=> 'articles_table'))
				->joinInner(array('ut' => 'users_table'), 'at.article_author_id = ut.user_id') /* Add author */
				->where('article_id = ?', $id)->order($order_by);
						
			$article 	= $query->query()->fetch();
			
			/* If required to check access */
			if ($check_access)
				$article = $this->_privilegeParser($article);
			
			unset_key($article, 'password');
			unset_key($article, 'password_reset');
			unset_key($article, 'active');
			unset_key($article, 'reg_date');

			return $article;
		}
	}
	/**
	 * Creates/gets article url
	 */
	public function getArticleUrl_depre($id, $html_request = null)
	{
		$article = $this->getArticleById($id);
		$url	 = null;
		
		if (!empty($html_request))
		{
			// Use the request object to get the full url
			$url =  $html_request->getHttpHost() . '/'.
					$html_request->_url();		
		}
		 
		
	}
	
	/**
	 * This returns a permanent link url based on ID
	 */
	public function getArticleUrl($article_id)
	{
		if (is_numeric($article_id))
		{
			$article 	= $this->getArticleById($article_id);
			// ?f=2011/12/23/blessings-of-the-Lord
			$url 		= null;
			$url		.= ''.	date('Y/m/d', strtotime($article['article_date'])). '/'. $article['article_seo_title'];
			return  '?f='. urlencode($url);
		}
	}
	
	/**
	 * Checks if user is priviledged to view article and what level.
	 * 
	 * returns false or priviledge level.
	 */
	public function getPriviledge($article_id, $user_id)
	{
		
		
		if (is_numeric($article_id) && is_numeric($user_id))
		{
			/* Retrieve the article */
			if($article 	= $this->getArticleById($article_id, false /* no need for access check */))
			{
				
				$db 			= new Zend_Db_Table('page_content_group_members_table');
				
				$result 		= $db->select()->where('user_id = ?', $user_id)->where('page_content_group_id = ?', $this->_content_group_id)->query()->fetch();

				/* check if user belongs to the article group .. */								
				if ($result)
				{
					return $result['access_right'];
				}

			}
			
		}
		return Point_Model_User::GROUP_GUEST;
	}
	
	/**
	 * This gets the latest article in the database
	 */
	public function getLatestUrl()
	{
		$db_table 	= $this->getDbTable();
		
		$latest 	= $db_table->select()->order('article_date DESC')
					  		   ->limit(1)->query()->fetch();
					  		   
		$article_id = $latest['article_id'];
	
		return $this->getArticleUrl($article_id);  
		
	}
	
	
	/**
	 * This retrieves the latest Article
	 */
	public function getLatestArticle()
	{
		
		$order_by	= 'article_date DESC';
		
		$query = $this->getDbTable()->select()
						/*->getAdapter()
						->select()->from(array('nt'=> 'article_table'))
						->joinInner(array('ut' => 'users_table'), 'at.article_author_id = ut.user_id') /* Add author */
						->order($order_by)->limit(1);
		
		$article 	= $query->query()->fetch();
		
		
		
		return $article;
		
	}	
	
	/**
	 * This retrieves the topmost articles
	 */
	public function getTopArticles($count = 6)
	{
		
		$order_by	= 'article_date DESC';
		
		$query = $this->getDbTable()->getAdapter()
						->select()->from(array('at'=> 'articles_table'))
						->joinInner(array('ut' => 'users_table'), 'at.article_author_id = ut.user_id') /* Add author */
						->order($order_by)->limit((int)$count);
		
		$articles 	= $query->query()->fetchAll();
		
		
		/* remove unwanted info from result */
		foreach ($articles as &$article)
		{
			
			unset_key($article, 'password');
			unset_key($article, 'password_reset');
			unset_key($article, 'active');
			unset_key($article, 'reg_date');
		}
		return $articles;
		
	}
}