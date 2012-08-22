<?php
/*---------------------------------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * 
 * --------------------------------------------------------------------
 * 
* Created by ashon on Aug 11, 2012
 * 
 * (c) 2010 - 2012 Copyright Ashon Associates Inc. Web Solutions 
 * 
 * This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 */
/**
 * 
 * This Model handles all the view control for getting the specific 
 * calendar views and corresponding view only logic. It's more or less a view
 * helper.
 * 
 * DEPENDS ON:
 * 
 * Point_Model_News
 * Point_Model_Events
 * ==========================================================================
 */
class Point_Model_NewsEvents{
	
	/**
	 * Table name
	 */
	protected	$_table_name 	= '';
	
	protected	$_db_table 		= null;
	
	/**
	 * @var	Point_Model_Events	Event's object instance
	 */
	protected	$_events_model	= null;
	
	/**
	 * @var	Point_Model_News	News' object instance
	 */
	protected	$_news_model	= null;
		
		
	/**
	 * ==============================
	 *  Some useful constanst
	 * ============================== 
	 * */
	/* Result contents */
	const RESULT_NEWS			= 'news';
	const RESULT_EVENTS			= 'events';
	const RESULT_BOTH			= 'both';

	/* View types */
	const VIEW_YEAR				= 'year';
	const VIEW_MONTH			= 'month';
	const VIEW_WEEK				= 'week';
	const VIEW_DAY				= 'day';
		
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	
	private function __construct()
	{
		/* Create an instance of each */		
		if (!$this->_get_events_model())
			throw new Exception('Unable to create Events Object Instances in NewsAndEvent');	 

		if (!$this->_get_news_model())
			throw new Exception('Unable to create News Object Instances in NewsAndEvent');	 
	}
		
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
	/**
	 * This method retrieves the Events' model
	 * 
	 * A new one is created if not found
	 * 
	 * @return Point_Model_Events Events Object Class
	 */
	protected function _get_events_model()
	{
		if (null === $this->_events_model)
		{
			$this->_events_model	= Point_Model_Events::getInstance();
		}	
		
		return $this->_events_model;
	}
	
	/**
	 * This method retrieves the News' model
	 * 
	 * A new one is created if not found
	 * 
	 * @return Point_Model_News News Object Class
	 */
	protected function _get_news_model()
	{
		if (null === $this->_news_model)
		{
			$this->_news_model	= Point_Model_News::getInstance();
		}	
		
		return $this->_news_model;
	}
	
	/**
	 * This retrieves the news and events within the specified period
	 * 
	 * @param array $options All the fields for retrieval
	 * 				$options	= array(
	 * 								'view'		=> month | week | day ,
	 * 								'time'		=> UNIX_TIME_STAMP,
	 * 								'result'	=> News | Events | Both
	 * 								
	 * 							) 
	 * @return array Array of all the events within period
	 * 				 array(
	 * 						'events' 	=> array()
	 * 						'news'	 	=> array()
	 * 						'side_url'	=> array()
	 * 						)
	 */
	public function getNewsEventsByPeriod($options = array())
	{
		/* Check integrety of input */
		if (empty($options))
			throw new Exception( 'The options given is empty ' );
		
		if (!array_key_exists('view', $options))
			throw new Exception('Required \'view\' argument in Option missing.');			
		
		if (!array_key_exists('time', $options))
			throw new Exception('Required \'time\' argument in Option missing.');
		
		$time	= $view	=	$result_content_type	= null;
		
		/* Retrieve values */
		$view					=	getArrayVar($options, 'view', Point_Model_NewsEvents::VIEW_MONTH);
		$time					=	getArrayVar($options, 'time', strtotime($view));		// Use current time if none is specified
		$result_content_type	=	getArrayVar($options, 'result', Point_Model_NewsEvents::RESULT_BOTH);		// Use current time if none is specified
		
		/* Get period */
		$start_time				= date('Y-m-d h:i:s',$time);
		$end_time				= date('Y-m-d h:i:s',strtotime('+1 '. $view, $time));

//		vp($start_time);
//		vp($end_time);

		$events_obj				= $this->_get_events_model();
		$news_obj				= $this->_get_news_model();
		
		/* Get the news */
		$news_result			= $news_obj->getNewsByPeriod($start_time, $end_time);	
		
		/* Get the events */
		$events_result			= $events_obj->getEventsWithinRange($start_time, $end_time);	
	
		/* Generate the Navigation */
		$view_obj				= Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
		//Monthly
		$monthly_nav			= array('href' 	=> $view_obj->fullUrl(array('controller' => 'events', 'action' => 'month', 'ojo' => strtotime($start_time))),
										'title'	=> 'View by month',
										'name'	=> 'Months',
										'action'=> 'month');
		//Weekly
		$weekly_nav				= array('href' 	=> $view_obj->fullUrl(array('controller' => 'events', 'action' => 'week', 'ojo' => strtotime($start_time))),
										'title'	=> 'View by week',
										'name'	=> 'Weeks',
										'action'=> 'week');
		//Daily
		$daily_nav				= array('href' 	=> $view_obj->fullUrl(array('controller' => 'events', 'action' => 'day', 'ojo' => strtotime($start_time))),
										'title'	=> 'View by day',
										'name'	=> 'Days',
										'action'=> 'day');
		
		$side_url_result		= array('day'	=> $daily_nav,
										'week'	=> $weekly_nav,										
										'month'	=> $monthly_nav);
		
		/* remove current view */
		unset($side_url_result[$view]);
		
		return array(
				'news'		=> $news_result,
				'events'	=> $events_result,
				'side_url'	=> $side_url_result,
				'time'		=> $time
		);
	}
	
}