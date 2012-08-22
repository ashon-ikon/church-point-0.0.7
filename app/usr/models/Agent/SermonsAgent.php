<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 18, 2012
 * (c) 2010 - 2012 Copyright
 * 
 * Depends on Point_Model_Sermons
 * -------------------------------------------
 */
class Point_Model_Agent_SermonsAgent extends Point_Model_Agent_Abstract implements Point_Model_Agent_Interface 
{
	/**
	 * @var	int	num_of_sermons
	 */
	protected		$_no_of_sermons			= 6;

	/**
	 * @var	string	section_title
	 */
	protected		$_section_title			= 'Last Sermon';
	
	
	/**
	 * @var	int	num_of_characters
	 */
	protected		$_no_of_characters		= 900;


	/**
	 * 	Creates content
	 * 
	 * 	All agents must implement this.
	 */
	
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
	
	public function makeContent( $params = null)
	{
		$this->_getView();
		/* Sermons Object */
		$sermons_obj	= Point_Model_Sermons::getInstance();
		
		/* Retrieve the topmost sermons */
		$latest	= $sermons_obj->getLatestSermon();
		$this->_scrubBibleReferences($latest);
		
		/* append url to it */
		$request 		= Zend_Controller_Front::getInstance()->getRequest();
		$base_url		= $request->getScheme(). '://'. $request->getHttpHost();
		
		$sermon_url		= $base_url . '/word/sermons/' .$sermons_obj->getSermonUrl($latest['sermon_id']);
		$latest['sermon_url'] 	= $sermon_url;
		
		return $this->_makeHtml($latest);
	}
	
	
	protected	function _makeHtml($sermon)
	{
		$content		= null;
		
		$sectionHeader	= wrapHtml($this->getSectionTitle(), 'h2');
		$sectionHeader 	= wrapHtml($sectionHeader, 'div', array('class'=>'sermons-section') );
		
		$title_link		= wrapHtml(wrapHtml($sermon['sermon_title'], 'span'), 'a', array(
								'href' 	=> $sermon['sermon_url'],
								'class' => 'title-link',
								'title' => $sermon['sermon_highlight'] 
							));
		$content		.= wrapHtml($title_link, 'h3', array('id'=>'title'));
		
		/* Append speaker */
		
		$image_path  =  APP_PUBLIC_DIRECTORY . APP_IMAGES_DIRECTORY . $sermon['author_image'];
		    
		$w = $h = $t = $at = $new_width = $new_height = null;
		$image_obj				= Point_Model_Picture::getInstance();
		list($w, $h, $t, $at) 	= $image_obj->getImageInfo($image_path);
		$w = $w == null ? APP_IMAGES_THUMB_WIDTH 	: $w;
		$h = $h == null ? APP_IMAGES_THUMB_HEIGHT 	: $h;
		
		list($new_width, $new_height) =  $image_obj->getResizeDimension($w, $h);
		
		$image_path					=	'/'. APP_IMAGES_DIRECTORY . $sermon['author_image'];
		if (is_file(remove_trailing_slash(APP_PUBLIC_DIRECTORY) . $image_path))
		{
			$sermon['speaker_image']	= $image_path; 
				
			$sermon['speaker_image_w']	= $new_width;
			$sermon['speaker_image_h']	= $new_height;
		}
		
		$sermon['sermon_author_view'] 	= $this->_getBaseUrl(). '/word/speakerprofile/?sp='.$sermon['sermon_author_id'];
		
		$author_name 	= $sermon['sermon_author'] == '' ? $sermon['author_firstname']. ' ' . $sermon['author_lastname'] : $sermon['sermon_author'];
		
		$speaker_info	= $speaker_image = null;
		$date			= wrapHtml(	date('F ', 	strtotime($sermon['sermon_date'])), 'time', array('class'	=> 'month'));
		$date			.= wrapHtml(date('jS ', 	strtotime($sermon['sermon_date'])), 'time', array('class'	=> 'day'));
		$date			.= wrapHtml(date('Y ', 	strtotime($sermon['sermon_date'])), 'time', array('class'	=> 'year'));
		$date			.= ' '. wrapHtml(date('H:ia', strtotime($sermon['sermon_date'])), 'time', array('class'	=> 'time'));
		$date			= wrapHtml($date, 'date');
		$author_name_cont = wrapHtml(wrapHtml($author_name, 'span', array('class' => 'author-name')), 'a', array('class' => 'author-profile-link','href' => $sermon['sermon_author_view'], 'title'=>'View '.$author_name.'\'s profile')) ;
		$speaker_info	= wrapHtml(wrapHtml($author_name_cont. '<br />' . $date  , 'span',array('class' => 'author')),
														'td', array('class' 	=> 'contentpad'));
		$speaker_image	= (!isset($sermon['speaker_image']) ? '' : 
	
									wrapHtml(wrapHtml('','img', array('src' 		=> $sermon['speaker_image'], 
									  					     'title'	=> $author_name, 
															 'width'	=> $sermon['speaker_image_w'], 
															 'height'	=> $sermon['speaker_image_h'],
															 'alt' 		=> 'image of ' . $author_name), true)
												, 'a', array('class' => 'author-profile-link','href' => $sermon['sermon_author_view'], 'title'=>'View '.$author_name.'\'s profile'))) ; 
		$speaker_image	= wrapHtml($speaker_image, 'td'); /* Enclose inside td tags */
		
		$speaker_table	= wrapHtml(wrapHtml($speaker_image. $speaker_info, 'tr'),'table');
		$speaker_div	= wrapHtml($speaker_table, 'div', array('class' => 'speaker-info fl'));
		$speaker_div	.= wrapHtml('', 'div', array('class' => 'clr'));
//		$speaker		= wrapHtml('<date>'. date('F jS Y', strtotime($sermon['sermon_date'])). ' <time>' . date('H:i a', strtotime($sermon['sermon_date'])).'</time></date> | By '. 
//								$author_name . '<img src="'. $sermon['speaker_image'].'" title="Speaker '. $author_name.'" width="'. $sermon['speaker_image_w'].'" height="'. $sermon['speaker_image_h'].'" />'  , 'p', array('class'=>'sermon-author'));
		$content		.= $speaker_div;
		
		$content		.= getShowcaseText($sermon['sermon_content'], $this->_no_of_characters) .'<div class="clr"></div>';
		
		/* Add Inset */
		$sermon_inset	= wrapHtml($sermon['sermon_highlight'], 'blockquote', array('class'=>'sermon-highlight'));
		$content		.= $sermon_inset;
		
		
		$readMoreLink	= 	wrapHtml('continue reading &#187;', 'a', array(
								'href' 	=> $sermon['sermon_url'],
								'title' => 'Read more about '. $sermon['sermon_title'] 
							));
		//<TAG\b[^>]*>(.*?)</TAG>
		$content		.= wrapHtml($readMoreLink, 'div', array('class'=>'more'));
		$content		= wrapHtml($content, 'div');
		
		$content		= $sectionHeader . $content;
		$viewOptions	= array('class' => 'section');
		$ret			= wrapHtml($content,'div', $viewOptions);
		
		return $ret;
	}
	
	/**
	 * This function smartly inject clickable links to qualified Bible references
	 */	
	protected function _scrubBibleReferences(&$sermon)
	{
		/**
		 * Scrub the sermon and replace Bible reference 
		 */		
		if (is_array($sermon) && !empty($sermon))
		{
			$sermon['sermon_content']	= scrubBibleRef($sermon['sermon_content']);
		}
		return $sermon;
	}
}