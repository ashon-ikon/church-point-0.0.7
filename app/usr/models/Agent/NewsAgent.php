<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 18, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_Agent_NewsAgent implements Point_Model_Agent_Interface
{
	/**
	 * @var	int	num_of_news
	 */
	protected		$_no_of_news		= 6;

	/**
	 * @var	int	num_of_characters
	 */
	protected		$_no_of_characters		= 300;

	/**
	 * @var	string	section_title
	 */
	protected		$_section_title		= 'Community Sharing ...';
	
	
	/**
	 * Change / Set the section title
	 */
	public function setSectionTitle($title)
	{
		if (is_string($title))
			$this->_section_title = $title;
	}
	
	/**
	 * Get section title
	 */
	public function getSectionTitle()
	{
		return $this->_section_title;
	}
	
	/**
	 * 	Creates content
	 * 
	 * 	All agents must implement this.
	 */
	public function makeContent( $params = null)
	{
		/* News Object */
		$news_obj	= Point_Model_News::getInstance();
		
		/* Retrieve the topmost news */
		$latest	= $news_obj->getLatestNews();
		
		/* append url to it */
		$request 		= Zend_Controller_Front::getInstance()->getRequest();
		$base_url		= $request->getScheme(). '://'. $request->getHttpHost();
		
		$news_url		= $this->_getBaseUrl('/articles/news/') .$news_obj->getNewsUrl($latest['news_id']);
		$latest['news_url'] 	= $news_url;
		
		return $this->_makeHtml($latest);
	}
	
	
	protected	function _makeHtml($news)
	{
		
		$content		= null;
		
//		$section_link 	= wrapHtml('<a href="' . $this->fullUrl(array('action' => 'allarticles'), null, false). '" title="view community articles list">Articles List</a>');
		$sectionHeader	= wrapHtml($this->getSectionTitle(), 'h2');
		$content		.= wrapHtml(
								wrapHtml($news['news_title'], 'a', array(
														'href' 	=> $news['news_url'],
														'title' => 'Read more about '. $news['news_title']))
							, 'h4', array('id'=>'news-title'));
		$content		.= getShowcaseText($news['news_content'], $this->_no_of_characters); // Limit content to 300 characters
		
		$readMoreLink	= 	wrapHtml('continue reading &#187;', 'a', array(
								'href' 	=> $news['news_url'],
								'title' => 'Read more about '. $news['news_title'] 
							));
		//<TAG\b[^>]*>(.*?)</TAG>
		$content		.= wrapHtml('<span class="clr"></span>'.$readMoreLink, 'p', array('class'=>'more'));
		$content		 = wrapHtml($content, 'div', array('class' => 'main-section'));
		$content		.= $this->_addOtherNews();
		
		$content		= wrapHtml($content, 'div');
		$content		= $sectionHeader . $content;
		$viewOptions	= array('class' => 'newsbox rdcorners lightgreybox');
		$ret			= wrapHtml($content,'div', $viewOptions);
		$ret			= wrapHtml($ret,'div', array('class' => 'section'));
		
		return $ret;
	}
	
	/**
	 * Add the other news section
	 */
	
	protected function _addOtherNews()
	{
		/* News Object */
		$news_obj	= Point_Model_News::getInstance();
		
		/* add other news */
		$top_news	= $news_obj->getTopNews($this->_no_of_news);
		
		array_shift($top_news);	// Remove the most recent, it's already shown
		
		$content	= null;
		foreach ($top_news as $news ) 
		{
			/* construct the url */	
			$news_url		= $this->_getBaseUrl('/articles/news/') .$news_obj->getNewsUrl($news['news_id']);
		
			$content	.= wrapHtml(
							/* News content start*/
								wrapHtml(wrapHtml($news['news_title'], 'strong'), 'a', array('href'=>$news_url, 'class'=>'news-title'))	
							  	.wrapHtml($news['news_desc'] ? $news['news_desc']:'&nbsp;','p', array('class'=> 'section-desc'))
							/* News content ends */
							
						, 'div', array('class'=>'section-highlight', 'title'=>$news['news_desc']));
		}
		
		$content	= wrapHtml($content, 'div', array('class'=>'news-others'));
		
		return $content;
			
	}
	
	/**
	 * 
	 */
	protected function _getBaseUrl($url)
	{
		$request 		= Zend_Controller_Front::getInstance()->getRequest();
		$base_url		= $request->getScheme(). '://'. $request->getHttpHost();
		
		return	$base_url . $url;
	}
}