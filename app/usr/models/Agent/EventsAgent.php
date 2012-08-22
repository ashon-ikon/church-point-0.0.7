<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 18, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_Agent_EventsAgent Extends Point_Model_Agent_Abstract implements Point_Model_Agent_Interface
{
	/**
	 * @var	int	num_of_events
	 */
	protected		$_no_of_events		= 6;

	/**
	 * @var	int	num_of_characters
	 */
	protected		$_no_of_characters		= 300;

	/**
	 * @var	string	section_title
	 */
	protected		$_section_title		= 'Events & Activities';
	
	
	/**
	 * Change / Set the section title
	 */
	public function setSectionTitle($title)
	{
		if (is_string($title))
			$this->_section_title = htmlentities($title);
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
		/* Events Object */
//		$events_obj		= Point_Model_Events::getInstance();
//		$calender_obj	= Point_Object_Calendar::getInstance();
		
//		/* Retrieve the topmost events */
//		$latest	= $events_obj->getLatestEvents();
//		
//		/* append url to it */
//		$request 		= Zend_Controller_Front::getInstance()->getRequest();
//		$base_url		= $request->getScheme(). '://'. $request->getHttpHost();
//		
//		$events_url		= $base_url . '/events/events/' .$events_obj->getEventsUrl($latest['events_id']);
//		$latest['events_url'] 	= $events_url;
//		
//		return $this->_makeHtml($latest);

		return $this->makeCalendar($params);
	}
	
	
	protected	function _makeHtml($events)
	{
		
		$content		= null;
		
		$sectionHeader	= wrapHtml($this->getSectionTitle(), 'h2');
		$content		.= wrapHtml(
								wrapHtml($events['events_title'], 'a', array(
														'href' 	=> $events['events_url'],
														'title' => 'Read more about '. $events['events_title']))
							, 'h4', array('id'=>'events-title'));
		$content		.= getShowcaseText($events['events_content'], $this->_no_of_characters); // Limit content to 300 characters
		
		$readMoreLink	= 	wrapHtml('continue reading &#187;', 'a', array(
								'href' 	=> $events['events_url'],
								'title' => 'Read more about '. $events['events_title'] 
							));
		//<TAG\b[^>]*>(.*?)</TAG>
		$content		.= wrapHtml('<span class="clr"></span>'.$readMoreLink, 'p', array('class'=>'more'));
		$content		 = wrapHtml($content, 'div', array('class' => 'main-section'));
		$content		.= $this->_addOtherEvents();
		
		$content		= wrapHtml($content, 'div');
		$content		= $sectionHeader . $content;
		$viewOptions	= array('class' => 'eventsbox rdcorners');
		$ret			= wrapHtml($content,'div', $viewOptions);
		$ret			= wrapHtml($ret,'div', array('class' => 'section'));
		
		return $ret;
	}
	
	protected	function makeCalendar($params = null)
	{
		/* Calendar */
		$calender_obj	= Point_Object_Calendar::getInstance();
		$events_obj		= Point_Model_Events::getInstance();
		$content		= null;
		$current_time	= time();
		$d	= $m = $y 	= null;
		$in_date		= null;
		if (is_array($params))
			$in_date	= getArrayVar($params, 'date');
			
		if (is_array($in_date) && is_array($in_date))
		{
			$d				= getArrayVar($in_date, 'd', date('j'));
			$m				= getArrayVar($in_date, 'm', date('n'));
			$y				= getArrayVar($in_date, 'y', date('Y'));
			$current_time	= mktime(0,0,0,$m, $d, $y);
		}
		
		
		$sectionHeader	= wrapHtml($this->getSectionTitle(), 'h2');
		$content		.= wrapHtml(
								date('F Y')
							, 'h4', array('id'=>'events-title'));
		$date			= array('month' => date('m', $current_time), 'year' =>$y);
		
		$events_list	= $events_obj->getEventsInMonth($date['month'], $date['year']);		
		/**
		 * =>date	string	 date format				
	 * 	   =>data			Array
	 * 					=> title	String
	 * 					=> href		String
		 * 
		 */
		$prepared_events_list	= null;
		
		if (!empty($events_list) && is_array($events_list))
		{
			foreach($events_list as $event)
			{
				$href					= '';
				$prepared_events_list[] = array('date' => $event['event_start_date'], 
												'data' => array('title' => $event['event_title'], 'href' => $href)); 
			}
		}
		
//		echo  '<pre>',print_r($events_list, true),'</pre>'; exit;
		$content		.= $this->_getCalendarNav($date);
		 
		//<TAG\b[^>]*>(.*?)</TAG>
		$content		= wrapHtml	($content, 	'div', array('class' => 'main-section'));
		$content		= wrapHtml	($content, 	'div');
		$content		= $sectionHeader . $content;
		$viewOptions	= array		('class' => 'eventsbox rdcorners lightgreybox smallboxshadow');
		$ret			= wrapHtml	($content,	'div', $viewOptions);
		$ret			= wrapHtml	($ret,		'div', array('class' => 'section', 'id' => 'event-cal'));
		
		return $ret;
		
	}
	
	/**
	 * Add the other events section
	 */
	
	protected function _addOtherEvents()
	{
		/* Events Object */
		$events_obj	= Point_Model_Events::getInstance();
		
		/* add other events */
		$top_events	= $events_obj->getTopEvents($this->_no_of_events);
		
		array_shift($top_events);	// Remove the most recent, it's already shown
		
		$content	= null;
		$request 		= Zend_Controller_Front::getInstance()->getRequest();
		$base_url		= $request->getScheme(). '://'. $request->getHttpHost();
		
		foreach ($top_events as $events ) 
		{
			/* construct the url */	
			$events_url		= $base_url . '/events/events/' .$events_obj->getEventsUrl($events['events_id']);
		
			$content	.= wrapHtml(
							/* Events content start*/
								wrapHtml(wrapHtml($events['events_title'], 'strong'), 'a', array('href'=>$events_url, 'class'=>'events-title'))	
							  	.wrapHtml($events['events_desc'] ? $events['events_desc']:'&nbsp;','p', array('class'=> 'section-desc'))
							/* Events content ends */
							
						, 'div', array('class'=>'section-highlight', 'title'=>$events['events_desc']));
		}
		
		$content	= wrapHtml($content, 'div', array('class'=>'events-others'));
		
		return $content;
			
	}
	
	/**
     * This creates a navigatable calender
     * @return String	Calendar Format in HTML stuff
     */
    protected function _getCalendarNav($date = array())
    {
	    /**
	     * ============================
	     * Create the navigation stuff
	     * ----------------------------
	     */
	    $calendar		= Point_Object_Calendar::getInstance();
	    $sermons_obj	= Point_Model_Events::getInstance();
	    
	    $month			= getArrayVar($date, 'month', date('n'));
	    $year			= getArrayVar($date, 'year', date('Y'));
	    
	    
	    /* get */
	    $url			= array();
	    $month_info		= array('month' => $month,
								'year'  => $year);
	    
	    if ($month_events	= $sermons_obj->getEventsInMonth($month))
	    {
	    	/* Construct the calendar array from result */
	    	
	    	foreach ($month_events as $this_event)
	    	{
	    		
	    		$url[] 		= array('date' => $this_event['event_start_date'], 
									'data' => array( 'title'	=> $this_event['event_title'],
													 'href'		=> $this->_getEventUrl($this_event), 
													 'class'	=> 'eday')); 
	    	}
	    	
			
//	    	echo $calender->getCalendar($date, $url);
	    }
	    
	    $calendar_html	= $calendar->getCalendar($month_info, $url, array(), true);
	    
	    return $calendar_html;
		
    }
    
    /**
     * Helper function to get qualified url for event
     */
    protected function _getEventUrl($event)
    {
    	return $this->_getBaseUrl() . '/events/detail/?e_id='. urlencode($event['event_id']);
    }
}