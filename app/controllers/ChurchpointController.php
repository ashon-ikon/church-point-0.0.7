<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 19, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class churchpointController extends Point_Controller_Action 
{
	
	public function preDispatch()
	{
		
	}
	
	public function init()
	{
		
	} 
	
	public function indexAction()
	{
		$this->_setTitle('My ChurchPoint');
		
		$this->view->churchpoints = $this->_getChurchpoints();
		
	}
	
	protected function _getChurchpoints()
	{
		$churchpoints		= array();
		
		
		/**
		 * 
		 * 

<div class="wdow articles fl"><strong class="heading">News and Articles</strong>
	<p class="caption" title="Get the news and read the latest community articles">
	<a href="<?php echo $this->fullUrl(array('controller'=>'articles', 'action'=>'index'), null, false);?>" title="Get the news and read the latest community articles">Read news and community articles<br /><span class="fwd"></span></a>
	</p>
	<p class="options"><a href="<?php echo $this->fullUrl(array('controller'=>'articles', 'action'=>'allarticles'), null, false);?>">Articles</a> |
	<a href="<?php echo $this->fullUrl(array('controller'=>'articles', 'action'=>'allnews'), null, false);?>">News</a></p>
</div>
<div class="wdow cgroups fl clearfix"><strong class="heading">Care Groups</strong>
	<p class="caption" title="Catch-up with info in your care groups">
	<a href="<?php echo $this->fullUrl(array('controller'=>'c-groups', 'action'=>'index'), null, false);?>" title="Catch-up with info in your care groups">Get updates from care groups you belong to<br /><span class="fwd"></span></a>
	</p>
	<p class="options"><a href="#">Recent</a> | <a href="#">List</a></p>
</div>
<div class="wdow library fl"><strong class="heading">Library Point</strong>
	<p class="caption" title="Catch-up with info in your care groups">
	<a href="#" title="My Library">Check and monitor your library experience.<br /><span class="fwd"></span></a>
	</p>
	<p class="options"><a href="#">Recent</a> | <a href="#">List</a></p>
</div>

		 */
		
		$baseUrl	= $this->_getBaseUrl();
		
		/* Get Word Point */
		$word_point['name']		= 'Sermons';
		$word_point['caption']	= 'Read and listen to previous sermons';
		$word_point['class']	= 'word';
		$word_point['main_url']	= $this->view->fullUrl(array('controller'=>'word', 'action'=>'index'), null, false);
		$word_point['main_title'] = 'Get the latest sermon';
		$word_point['menus']	= array(
									array( 'link_name' => 'Recent', 'options' => array('href' => $baseUrl . '/word/sermons', 'class' => '', 'title' => 'Read Last sermon')),
									array( 'link_name' => 'List', 	'options' => array('href' => $baseUrl . '/word/', 'class' => '', 'title' => 'All'))
									);
		/* Add priviledge menus */
		if ($additional_menus = $this->_getAdditionalWordMenus())
		{
			foreach ($additional_menus as $additional_menu)
			$word_point['menus'][]	= $additional_menu;
		}
		
		/* Get Events Point */
		$envents_point['name']		= 'Events & Activities';
		$envents_point['caption']	= 'Check your upcoming events';
		$envents_point['class']		= 'events';
		$envents_point['main_url']	= $this->view->fullUrl(array('controller'=>'events', 'action'=>'events'), null, false);
		$envents_point['main_title']= 'Events and activities';
		$envents_point['menus']		= array(
									array( 'link_name' => 'Today', 		'options' => array('href' => $baseUrl . '/events/', 'class' => '', 'title' => 'Check today\'s events')),
									array( 'link_name' => 'This Week', 	'options' => array('href' => $baseUrl . '/events/', 'class' => '', 'title' => 'This week\'s events')),
									array( 'link_name' => 'This Month', 'options' => array('href' => $baseUrl . '/events/', 'class' => '', 'title' => 'This month\'s events'))
										);
		
		/* Get Biblestudy Point */
		$biblestudy_point['name']		= 'Devotion & Bible Study';
		$biblestudy_point['caption']	= 'Study God\'s Word';
		$biblestudy_point['class']		= 'devotion';
		$biblestudy_point['main_url']	= $this->view->fullUrl(array('controller'=>'biblestudy', 'action'=>'index'), null, false);
		$biblestudy_point['main_title']= 'Study the Word';
		$biblestudy_point['menus']		= array(
									array( 'link_name' => 'My eDevotion', 'options' => array('href' => '#', 'class' => '', 'title' => 'Read Last devotion')),
									array( 'link_name' => 'My Bible Study', 	'options' => array('href' => '#', 'class' => '', 'title' => 'All'))
										);
		/* Add priviledge menus */
		
		/* Get News Point */
		$news_point['name']		= 'News & Articles';
		$news_point['caption']	= 'Read news and community articles';
		$news_point['class']	= 'articles';
		$news_point['main_url']	= $this->view->fullUrl(array('controller'=>'articles', 'action'=>'index'), null, false);
		$news_point['main_title']= 'Get the news and read the latest community articles';
		$news_point['menus']		= array(
									array( 'link_name' => 'Articles', 		'options'	=>	array('href' => $baseUrl . '/articles/allarticles', 'class' => '', 'title' => 'Read latest news')),
									array( 'link_name' => 'News', 	'options'	=>	array('href' => $baseUrl . '/articles/allnews/', 'class' => '', 'title' => 'Read latest articles'))
										);
		
		/* Get Care groups Point */
		$cgroup_point['name']		= 'Care Groups';
		$cgroup_point['caption']	= 'Get updates from and rub minds with groups you belong to';
		$cgroup_point['class']		= 'cgroups';
		$cgroup_point['main_url']	= $this->view->fullUrl(array('controller'=>'c-group', 'action'=>'index'), null, false);
		$cgroup_point['main_title']	= 'Catch-up with info in your care groups';
		$cgroup_point['menus']		= array(
									array( 'link_name' => 'Recent', 'options'	=>	array('href' => '#', 'class' => '', 'title' => 'Get recent updates from care group')),
									array( 'link_name' => 'My Discussion Point', 	'options'	=>	array('href' => '#', 'class' => '', 'title' => 'Discuss and share with others in the group'))
										);
		
		
		/* Get Care groups Point */
		$library_point['name']		= 'My Library';
		$library_point['caption']	= 'Check and monitor your library experience';
		$library_point['class']		= 'library';
		$library_point['main_url']	= $this->view->fullUrl(array('controller'=>'c-group', 'action'=>'index'), null, false);
		$library_point['main_title']	= 'Never stay disconnected from your resources';
		$library_point['menus']		= array(
									array( 'link_name' => 'Recent', 'options'	=>	array('href' => '#', 'class' => '', 'title' => 'Library')),
									array( 'link_name' => 'List', 	'options'	=>	array('href' => '#', 'class' => '', 'title' => 'All'))
										);
		
		/* Append all Point Modules */
		$churchpoints[]			= $word_point;
		$churchpoints[]			= $news_point;
		$churchpoints[]			= $biblestudy_point;
		$churchpoints[]			= $envents_point;
		$churchpoints[]			= $cgroup_point;
		$churchpoints[]			= $library_point;
		
		return $churchpoints;
	}
	
	/**
	 *	Additional Words Menus 
	 */
	protected function _getAdditionalWordMenus()
	{
		$contentGroups	= Point_Model_ContentGroups::getInstance();
		$word_groupd_id	= $contentGroups->getGroupIdByKeyword('sermon'); 
		$user			= Point_Model_User::getInstance();
		$user_id		= $user->getUserId();
		$user_membership = $contentGroups->getMembership($user_id, $word_groupd_id);
		$ret 			= array();
		
		if ( Point_Model_ContentGroups::GROUP_GUEST != $user_membership )
		{
			/* Add new sermon note */
			$ret[] = array( 'link_name' => 'Add Sermon', 	'options'	=>	array('href' => 'word/addsermon', 'class' => '', 'title' => 'Add new sermon summary'));
		}
		
		return $ret;	
	}
}