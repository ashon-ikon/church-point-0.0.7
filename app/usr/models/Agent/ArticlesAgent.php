<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 18, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_Agent_ArticlesAgent extends Point_Model_Agent_Abstract implements Point_Model_Agent_Interface
{
	/**
	 * @var	int	num_of_articles
	 */
	protected		$_no_of_articles		= 4;

	/**
	 * @var	int	num_of_characters
	 */
	protected		$_no_of_characters		= 500;

	/**
	 * @var	string	section_title
	 */
	protected		$_section_title		= 'Articles & Publications ...';
	
	
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
		/* Article Object */
		$article_obj	= Point_Model_Article::getInstance();
		
		/* Retrieve the topmost article */
		$latest	= $article_obj->getLatestArticle();
		
		/* append url to it */
		$request 		= Zend_Controller_Front::getInstance()->getRequest();
		$base_url		= $request->getScheme(). '://'. $request->getHttpHost();
		
		$article_url		= $base_url . '/articles/articles/' .$article_obj->getArticleUrl($latest['article_id']);
		$latest['article_url'] 	= $article_url;
		
		return $this->_makeHtml($latest);
	}
	
	
	protected	function _makeHtml($article)
	{
		if (!defined('VTICKER_DEFINED'))
		{
			$viewRenderer 		= Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
	    	if (!$viewObject 	= $viewRenderer->view)
	    	{
		    	throw new Exception ('Call to empty view!');
	    	}
	    	/* add the script to layout script */
	    	$base_url			= $viewObject->fullUrl(array('controller'=>'index', 'action' => 'home'), null, false);
	    	$script_location 	= '/js/jquery.vticker.js';
	    	$viewObject->headScript()->appendFile( $script_location , 'text/javascript');
	    	
			define('VTICKER_DEFINED' , 1000 );	
		}
		
		$content		= null;
		
		$sectionHeader	= wrapHtml($this->getSectionTitle(), 'h2');
		$sectionHeader 	= wrapHtml($sectionHeader, 'div', array('class'=>'articles-section') );

		$content		= $this->_addArticles();
		
		$content		= wrapHtml($content, 'div');
		$content		= $sectionHeader . $content;
		$viewOptions	= array('class' => 'articlebox');
		$ret			= wrapHtml($content,'div', $viewOptions);
		$ret			= wrapHtml($ret,'div', array('class' => 'section'));
		
		return $ret;
	}
	
	/**
	 * Add the other article section
	 */
	
	protected function _addArticles()
	{
		/* Article Object */
		$article_obj	= Point_Model_Article::getInstance();
		
		/* add other article */
		$top_article	= $article_obj->getTopArticles($this->_no_of_articles);
		
//		array_shift($top_article);	// Remove the most recent, it's already shown
		
		$content	= null;
		foreach ($top_article as $article ) 
		{
			/* construct the url */	
			$request 			= Zend_Controller_Front::getInstance()->getRequest();
			$base_url			= $request->getScheme(). '://'. $request->getHttpHost();
			$article_url		= $base_url . '/articles/articles/' .$article_obj->getArticleUrl($article['article_id']);
		
			$article_content 	= getShowcaseText($article['article_content'], $this->_no_of_characters);
			$readMoreLink		= 	wrapHtml('continue reading &#187;', 'a', array(
								'href' 	=> $article_url,
								'title' => 'Read more about '. $article['article_title'] 
							));
			$article_author_html= 'Written by';
			
			$article_author	  	= wrapHtml('Written by', 'p');
			
			$div_content		= wrapHtml(
							/* Article content start*/					
								wrapHtml(wrapHtml($article['article_title'], 'strong'), 'a', array('href'=>$article_url, 'class'=>'article-title'))
									
							  	. $article_content
							  	. wrapHtml('<span class="clr"></span>'.$readMoreLink, 'p', array('class'=>'more'))
							/* Article content ends */
							
						, 'div', array('class'=>'section-highlight', 'title'=>$article['article_desc']));
						
			/* wrap everything inside an li tag*/
			$content	.= wrapHtml($div_content, 'li');
		}
		
		$content	= wrapHtml($content, 'ul');
		
		$content	= wrapHtml($content, 'div', array('class'=>'article-all', 'id'=>'articles-all'));
		
		return $content;
			
	}
}