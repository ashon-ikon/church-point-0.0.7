<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 25, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Object_Calendar
{
	protected	$_first_day_of_week	= 1;
	/**
	 * @var array $_days Days of the week
	 */
	protected	$_days				= array( 1 => 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
	
	/**
	 * @var array $_months Months of the year
	 */
	protected	$_months			= array( 1 => 'January', 'February','March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');	

	/**
	 * Calender HTML Tags
	 */	
	protected	$_html_container	= 'table';
	protected	$_html_container_attr	= array('class' => 'calendar', 'border' => 0);

	protected	$_html_top_row		= 'thead';
	protected	$_html_top_row_attr	= array('class' => 'wkrow wktitle');
	
	protected	$_html_weeks		= 'tr';
	protected	$_html_weeks_attr	= array('class' => 'wkrow');
	
	protected	$_html_days			= 'td';
	protected	$_html_days_attr	= array('class' => 'wkn hd');

	protected	$_viewObject		= null;
		
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	
	private function __construct()
	{	}
		
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
	/**
	 * This function sets the options for the Calendar object
	 * 
	 * @options	array info for all the options
	 */
	public function setupCalendar($options = array())
	{
		
	}
	
	/**
	 * This function gets last day of the month
	 * @param int $m Month
	 * @param int $y Year
	 * 
	 * @return int lastday of the month
	 */
	public function getLastDayOfMonth($m = 1, $y = 1970)
	{
		if (checkdate($m, 31, $y)){
			$last_date = 31;
		}elseif (checkdate($m, 30, $y)){
			$last_date = 30;
		}elseif (checkdate($m, 29, $y)){
			$last_date = 29;
		}else
			$last_date = 28;
		
		return $last_date;		
	}		
	
	/**
	 * Get the first day-name of the month
	 * 
	 * @returns the day-name
	 */
	protected function _getFirstDayName($date = array())
	{
		$month		= getArrayVar($date, 'month',	1);
		
		$year 		= getArrayVar($date, 'year' ,	1970);
		
		/* Get the first day name */
		$date_name 	= date('D', mktime(0,0,0, $month, 1, $year));
		
		$date_index	= array_search($date_name, $this->_days);
		
		return $date_index;
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
	
	protected function _addScript()
	{
		if (!defined('CALENDER_CSS_DEFINED'))
		{
			/* Get the view */	
			$view		= $this->_getView();
			
			$view->headLink()->appendStylesheet($view->fullBaseUrl() .'/css/calendar.css');;
			
			define('CALENDER_CSS_DEFINED', 343 );
		}
	}
	
	
	/**
	 * Get the calendar view script
	 * @param array $date	Month and year
	 * 		
	 * 					=> month
	 * 					=> year
	 * 
	 * @param array $days_url	Url of days.
	 * 
	 *		 				=>date	string	 date format				
	 * 						=>data	Array
	 * 									=> title	String
	 * 									=> href		String
	 * 									=> ...
	 * 
	 * @param array $week_url	Url of days.
	 * 
	 *		 				=>week	int	 	week number				
	 * 						=>data	Array
	 * 									=> title	String
	 * 									=> href		String
	 * 									=> ...
	 * @param boolean show_navs
	 */
	public function getCalendar( $date , $days_urls = array() , $week_urls = array(),  $short = false )
	{
		if (!empty($date))
		{
			$this->_addScript();
			
			/* Get all the date parameters */
			
			$month	= getArrayVar($date, 'month',	1);
			
			$year 	= getArrayVar($date, 'year' ,	1970);
			
			/* get last day of month */
			$last_day_of_month 	= $this->getLastDayOfMonth( $month, $year);
			
			/* get first day of the month position */
			$first_day_pos 	= $this->_getFirstDayName(array('month' => $month, 'year' => $year));
			
			/* get empty days of first week */
			/**
			 * 	Wk	Sun | Mon | Tue | Wed | Thur | Fri | Sat
			 * 	##	 1	   2	 3	   4	 5	    6	  7
			 */
			$first_week_pad 	= $first_day_pos - 1;
			
			/* Get the number of weeks */
			$no_of_weeks 		= ceil(( $first_week_pad + $last_day_of_month ) / 7);
			
			$day				= 1; // First day of the year.
			
			$h_wk				= $this->_html_weeks;
			$h_wk_attr			= $this->_html_weeks_attr;
			
			
			$h_top_wk			= $this->_html_top_row;
			$h_top_wk_attr		= $this->_html_top_row_attr;

			$h_dy				= $this->_html_days;
			$h_dy_attr			= $this->_html_days_attr;
			
			$weeks_header		= $short? 'Wk' : 'Week';
			
			$padding			= 0;
			
			$ret				= null;		// Return buffer 
			
			
			for($row = 0; $row <= $no_of_weeks; $row ++)
			{
				if ($row == 0 )
				{
					/**
					 * First row : Titles
					 * ========================= 
					 */
					
					
					/* Create the headers */
					for($wk_day = 0; $wk_day <= 7; $wk_day ++)
					{
						if ($wk_day == 0) 
						{
							// Weeks header
							$ret .= wrapHtml($weeks_header, $h_dy, $h_dy_attr);
														
						}
						else
						{
							/**/
							$ret .= wrapHtml($this->_days[$wk_day], $h_dy, $h_dy_attr); 
							
						}
					}
					
					/*wrap content */
					$ret = wrapHtml($ret, $h_top_wk, $h_top_wk_attr); 
					
					/*Wrap the header row*/
//					$ret = wrapHtml($ret, 'span', array('class' => 'wktitle'));
				}
				else
				{
										
					for($wk_day = 0; $wk_day <= 7; $wk_day ++)
					{
						/* Print the week: wk_day == 0 should be week number */
						if ($wk_day == 0)
						{
							$week_no		= $this->getWeek($month, $day, $year);
							
							if (!empty($week_urls) && is_array($week_urls))
							{
								
								foreach ($week_urls as $week_url)
								{
									if ($week_no	== getArrayVar($week_url, 'week'))
									{
//										echo '<pre>', print_r($week_url['data'], true),'</pre>'; exit;
										$ret .= wrapHtml( wrapHtml($week_no, 'a', $week_url['data'] ), $h_dy, $h_dy_attr );
										
									}
								}
							}
							else
							{
								$ret .= wrapHtml($week_no, $h_dy, $h_dy_attr);
							}
								
							
						}
						else
						{
							$this_url 	= null;
							/* Get url for this day */
							if (is_array($days_urls) && !empty($days_urls))
							{
//								echo '<pre>'. print_r($days_urls, true).'</pre>'; exit;
								foreach($days_urls as $url)
								{
//									echo "Month: $month, Day: $day, Year: $year<br />";
									$url_time	= strtotime($url['date']);
									$time_diff	= abs(mktime(0,0,0, $month, $day, $year) - 
													  mktime(0,0,0, date('n', $url_time), 
													  				date('j', $url_time), 
													  				date('Y', $url_time)));
									if ($time_diff ==   0 /*ONE_DAY*/  )
									{
										
										$this_url = $url['data'];
										break;
									}
								}
							}
							
							if ($row == 1 ) /* First calendar row */
							{
								/* Keep skipping till we pass pad */
								if ($padding != $first_week_pad)
								{
									$padding++;
									$ret .= wrapHtml('&nbsp;', $h_dy, array('class' => 'wdy'));
								}
								else
								{
									/* Date Starts here */

//									echo "Month: $month, Day: $day, Year: $year";
									if ($day == date('d') && $month == date('n') && $year == date('Y'))
										$attr	= array('class' => 'wdy tday', 'title' => 'today' );
									else
										$attr	= array('class' => 'wdy' );
																			
									$ret .= wrapHtml(
										$this_url? wrapHtml($day, 'a', $this_url ) : $day
										,$h_dy, $attr);
									$day++;
								} 
								
							}/* first row */
							else
							{
								if ($day <= $last_day_of_month)
								{
									if ($day == date('d') && $month == date('n') && $year == date('Y'))
										$attr	= array('class' => 'wdy tday', 'title' => 'today' );
									else
										$attr	= array('class' => 'wdy' );
																		
									$ret .= wrapHtml(
										$this_url? wrapHtml($day, 'a', $this_url) : $day
										, $h_dy, $attr);
									$day++;
								}
								else
								{
									/* Padd the rest */
									$ret .= wrapHtml('&nbsp;', $h_dy, array('class' => 'wdy'));
								}
							}// Other rows
							
						}
						
					}
					
					/*wrap content */
					$ret .= wrapHtml('', $h_wk, $h_wk_attr); 
					
				}
			}
			
			
		}
		else
		{
			throw new Exception('Empty date request specified');
		}
		
		return wrapHtml($ret, $this->_html_container, $this->_html_container_attr);
	}
	
	/**
	 * Get Events this month
	 * 
	 * @param int 	$month	intended month
	 * @param int 	$year	intended year
	 * 
	 * @return array List of weeks
	 * 
	 */
	public function getWeeksInMonth( $month, $year = null)
	{
		if (null === $year)
			$year = date('Y');
		
		if(null != $month && isValidDateFormat(sprintf('%d-%d-%d', $year, $month, 1)))
		{
			/* month info */
			 
			$last_day 	= $this->getLastDayOfMonth($month, $year);

			$weeks		= $last_week	= null;
			
			for ($day = 1; $day <= $last_day; $day++)
			{
				$new_week		= $this->getWeek( $month, $day, $year);
//				$new_week		= date('W', mktime(0,0,0, $month, $day, $year));
				if ($last_week != $new_week)
				{
					$weeks[]	= 	$new_week;
					/* Store the week */
					$last_week	= $new_week;
				}
			}
			
			return $weeks;
		}
	}
	
	/**
	 * This gets the week to which event belong
	 */
	public function getWeek($month, $day, $year)
	{
		$week_no		= date('W', mktime(0,0,0, $month, $day+1, $year));
		
		return $week_no;
	}
	
	protected function _getCalenderHead()
	{
		
	}
}