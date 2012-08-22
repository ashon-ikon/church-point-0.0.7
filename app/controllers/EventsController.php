<?php

class EventsController extends Point_Controller_Action
{
	/**
	 * @var	
	 */
	protected		$_user_priviledge		= 'guest';
	
	/**
	 * @var	
	 */
	protected		$_eventsErrors			= array();

	/**
	 * @var	
	 */
	protected		$_events_group_id		= null;
	
	
	public function preDispatch()
	{
		/* 
		 * Check the type of action if the action is not public
		 * restrict access accordingly.
		 * 
		 * == User must belong to Events Contents Group
		 * 
		 * == Depending on privacy User must be either a moderator or Admin 
		 */
		
		$request 					= $this->getRequest();
    	
    	$user 						= Point_Model_User::getInstance();
    	$groups 					= Point_Model_Groups::getInstance();
		$this->_user_priviledge		= $groups->getMembership($user->getUserId());
    	$this->_user 				= $user;
   		 
   		
   		$action_name = $request->getActionName();
   		
   		    
    	if (!$user->isLoggedIn() && 
    		('index' 	!= $action_name && 
			 'events' 	!= $action_name && 
			 'search' 	!= $action_name ))
		{
	    	$this->_redirect('error/noaccess');
		}
	}

    public function init()
    {
        /* Initialize action controller here */
        
         /* Retrieve the sermon ID */
	    $contentGroups	= Point_Model_ContentGroups::getInstance();
	    
        $eventsGroupId	= $contentGroups->getGroupIdByKeyword('events');
        if (!is_numeric($eventsGroupId))
	        throw new Exception('Unable to find a matching events table');
		    
		$this->_events_group_id = $eventsGroupId;
    }
    
    public function weekAction()
    {
    	
    }

    public function dayAction()
    {
    	$request			= $this->getRequest();
    	$events_obj			= Point_Model_Events::getInstance();
    	$calendar_obj		= Point_Object_Calendar::getInstance();
    	
    	$view_date			= $request->getParam('ojo'	, time());
    	
    	$month				= date('n', $view_date);
    	$day				= date('j', $view_date);
    	$year				= date('Y', $view_date);
    	
    	$date				= array('month' => $month, 'year' => $year);
    	$weeks_no			= $calendar_obj->getWeek($month, $day, $year);
    	$this_day_event		= $events_obj->getEventsByDate($month, $day, $year);
    }
    
    public function monthAction()
    {
    	$request			= $this->getRequest();
    	$news_event_obj		= Point_Model_NewsEvents::getInstance();
		
		/* get the requested period */
		$view_date			= $request->getParam('ojo'	, time());
		
		$options			= array('view' => Point_Model_NewsEvents::VIEW_MONTH,
									'time' => $view_date);
		$news_events_result	= $news_event_obj->getNewsEventsByPeriod($options);
		
		/* Pass response to view */    	
		$this->view->news_events 	= $news_events_result;
		$this->view->view_date		= $view_date;
		
		/* Set the view title */
		$this->_setTitle('Monthly view');
    }
    
    public function month2Action()
    {
    	$request			= $this->getRequest();
    	$events_obj			= Point_Model_Events::getInstance();
    	$news_event_obj		= Point_Model_NewsEvents::getInstance();
    	$calendar_obj		= Point_Object_Calendar::getInstance();
    	
    	$view_date			= $request->getParam('ojo'	, time());
    	$event_id			= $request->getParam('e_id'	, null);
    	
    	$month				= date('n', $view_date);
    	$year				= date('Y', $view_date);
    	
    	$date				= array('month' => $month, 'year' => $year);
    	$weeks_in_month		= $calendar_obj->getWeeksInMonth($month, $year);
    	
    	
    	/* Make the week url */
    	$weeks_url			= array();
    	if (!empty($weeks_in_month))
    	{
	    	foreach ($weeks_in_month as $week)
	    	{
	    		$weeks_url[]	= array('week' => $week, 
										'data' => array(	'title'=> 'View activies of week #'. $week, 
															'href' => '#',
															'class'=> 'weekurl'));
	    	}
    	}
    	
    	/* Inject the event dates */
    	$event_dates			= array();
    	$top_event_dates		= array();
    	$all_months_events		= $events_obj->getEventsInMonth($month, $year);
    	$top_events				= $events_obj->getTopEvents(12);
    	
//    	echo '<pre>',print_r($events_obj->getEventsByDate(), true),'</pre>';
    	
    	if (!empty($all_months_events))
    	{
//    		echo '<pre>', print_r($all_events, true), '</pre>'; exit;
	    	foreach ($all_months_events as $this_event)
			{
//				$this_d				= date('j',strtotime($this_event['event_start_date']));
//				$this_m				= date('n',strtotime($this_event['event_start_date']));
//				$this_y				= date('Y',strtotime($this_event['event_start_date']));
				$event_url			= $this->_getBaseUrl() . '/'. $request->getControllerName() . '/day/?ojo='.urlencode(strtotime($this_event['event_start_date']));
		    	$event_dates[]		= array('date' => $this_event['event_start_date'],
											'data' => array( 'title' => $this_event['event_title'],
															 'href'  => $event_url,
															 'class' => 'event-day'	));	
			}
    	}

    	if (!empty($top_events))
    	{
	    	foreach ($top_events as &$this_event)
			{
				$event_url			= $this->_getBaseUrl() . '/'. $request->getControllerName() . '/day/?ojo='.urlencode(strtotime($this_event['event_start_date']));
		    	$this_event['url']	= $event_url;	
			}
    	}
    	$this->view->top_events		= $top_events;
    	$this->view->calendar_name	= date('F', mktime(0,0,0,$month, 1, $year));
    	
    	$this->view->calendar		= $calendar_obj->getCalendar($date, $event_dates, $weeks_url);
    	
    	/**
    	 * --------
    	 * previous month
    	 */
    	$prev_month					= $month - 1;
    	$prev_year					= $year ;
    	
    	if( $prev_month < 1 )
    	{
    		$prev_month				= 12;
    		$prev_year				= $year - 1;
    	}
    	
    	$this->view->prev_month		= array('month' => date('F', mktime(0,0,0,$prev_month, 1, $prev_year)),
    										'link'	=> '#');
    	/**
    	 * --------
    	 * next month
    	 */
    	$next_month					= $month + 1;
    	$next_year					= $year ;
    	
    	if( $next_month > 12 )
    	{
    		$next_month				= 1;
    		$next_year				= $year + 1;
    	}
    	
    	$this->view->next_month		= array('month' => date('F', mktime(0,0,0,$next_month, 1, $next_year)),
    										'link'	=> '#');
    										
    	/**
    	 * ====================
    	 * Handle request if AJAX
    	 */
    	if ($request->isXmlHttpRequest())
    	{
    		exit;	
    	}
    }
    
    public function yearAction()
    {
    	
    }

	public function detailAction()
	{
		$request			= $this->getRequest();
    	$events_obj			= Point_Model_Events::getInstance();
    	
    	$event_id			= $request->getParam( 'e_id' , null );

    	$event				= $events_obj->getEvent($event_id);
    	
    	if ($event)
    	{
    		$this->_setTitle('Event Detail');
    		/* make correct image filepath */
    		$image_root				= $events_obj->getEventsImageRoot();
    		
    		$app_root				= realpath(APPLICATION_PATH . '/../public');
    		$base_path				= $this->_getBaseUrl(). substr($image_root, strlen($app_root) );
    		$event['event_image']	= $base_path . $event['event_image'];
    		
    		/* Allow editing if required */
    		
    		
    		$this->view->event		= $event;
    	}
	}
	
    public function indexAction()
    {
        $this->_forward('events');
    }

    public function calendarAction()
    {
        // action body
    }
    
    public function eventsAction()
    {
    	$request			= $this->getRequest();
    	$events_obj			= Point_Model_Events::getInstance();
    	$calendar_obj		= Point_Object_Calendar::getInstance();
    	
    	
    	$this->_setTitle('Events and Activities');
    	
    	/**
    	 * -----------------------
    	 * View shall be at different levels
    	 * 
    	 * depth			= d | w | m | y
    	 */
    	 
    	 $view_depth		= $request->getParam('depth', 'm');
    	 
    	 $this->view->event_calendar	= null;
    	 
    	 switch($view_depth)
    	 {
    	 	case 'd':
    	 	{
    	 		$this->_forward('day');
    	 	}break;

    	 	case 'w':
    	 	{
    	 		$this->_forward('week');
    	 	}break;

    	 	case 'm':
    	 	{
    	 		$this->_forward('month');
    	 	}break;

    	 	case 'y':
    	 	{
    	 		$this->_forward('year');
    	 	}break;
    	 }
    	 
    }
    

    public function addeventAction()
    {
    	$request			= $this->getRequest();
    	
    	$event_form			= new Point_Form_EventsForm();
    	$events_obj			= Point_Model_Events::getInstance();
    	
    	$this->view->form	= $event_form;
    	
    	$this->_setTitle('Add Event');
    	
    	if ($request->isPost())
    	{
    		$raw_data	= $request->getPost();
    		if ($event_form->isValid($raw_data))
    		{
    			
    			/* Extract data */
    			$clean_data			= $event_form->getValues();
    			
    			$event_title		= getArrayVar($clean_data, 	'event_title');
    			$event_desc			= getArrayVar($clean_data,	'event_desc');
				$event_start_date	= getArrayVar($clean_data,	'event_start_date');
				$event_stop_date	= getArrayVar($clean_data,	'event_stop_date');
				$event_group_id		= getArrayVar($clean_data,	'group_id');
				$event_image		= null;
				
				if ($event_form->event_image->receive())
        		{
        			if ($file_name = $event_form->event_image->getFileName())
        			{
	        			if ($file_name)
	        			{
	        				$event_image		= $file_name;
	        				/*
		        			$pixObject			= Point_Model_Picture::getInstance();
		        			
		        			$description 		= $event_desc == '' ? 'No Description' : $event_desc;
		        			$destination_fname	= makeSeoString(snipByWords($description , 200)); 
		        			
		        			$newSize 	= array();
		        			$info		= array('source' 			=> $file_name,
							        			'destination'		=> $destination_fname,
												'dest_path'			=> $events_obj->getEventsImageRoot(),
												'description'		=> $description,
												'store_in_db'		=> false,
												'keep_ext'			=> false);
		        			
		        			list($result, $msg) = $pixObject->saveImage($info, $newSize);
		        			if (!$result)
			        			$this->_uploadErrors[] = $msg;
		        			else
		        			{
		        				$event_image 	= $msg;	// Store the return path.
		        				/* Use relative path * / 
		        				$event_image	= substr( $event_image , strlen($events_obj->getEventsImageRoot()) );
			        			/* Remove temporary image * /
			        			unlink($file_name);
		        			}
		        			*/
	        			}
        			}
        		}
    			
    			if ($events_obj->addEvent($event_title, $event_desc, $event_start_date, $event_stop_date, $event_group_id, $event_image))
    			{
    				/* Event added successfully */
    				$this->view->successmsg = 'Event added <strong>successfully!</strong>';
    				
    				/* Remove the form */
    				$this->view->form		= null;
    			}
    			else
    			{
    				$this->_eventsErrors[]	= 'Sorry!<br />There was a <strong>problem</strong> adding the event!';
    			}
    			
    		}// form is valid
    		else
    		{
    			/* Get form errors */
    			$event_form->populate($raw_data);
    			
    			$this->_eventsErrors[]	= 'Some fields of this form appears not to be valid!';
    			
    			
    		}// form not valid
    		
    		$errors = $event_form->getMessages();
    		$preparedErrors = array();
    		if (is_array($errors))
    		{
	    		foreach($errors as $element => $msg)
				{
		    		if (is_array($msg))
		    		{
			    		foreach($msg as $msg_this)
						{
				    		$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg_this;
						}
		    		}
		    		else
			    		$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg; 	
				}
    		}
    		
    		$this->_eventsErrors =  array_merge($this->_eventsErrors, $preparedErrors);
    		
    		$this->view->errormsgs	= $this->_eventsErrors;
    		    		
    	}// Is POST request	
    	
    }

    public function editeventAction()
    {
	    $request			= $this->getRequest();
    	
    	$event_form			= new Point_Form_EventsForm();
    	$events_obj			= Point_Model_Events::getInstance();
    	
    	$this->_setTitle('Edit Event');
    	
    	$event_id			= $request->getParam('e_id', null);
    	
    	/* We must have a valid event to edit */
    	if(null == $event_id && !is_numeric($event_id))
    	{
	    	$errors[] 		= 'No event selected for editing!';
	    	$this->view->errormsgs = $errors;
	    	return;
    	} 
    	
    	$retrieved_event	= $events_obj->getEvent($event_id);
    		
    	if ($request->isPost())
    	{
    		
    		$raw_data	= $request->getPost();
    		if ($event_form->isValid($raw_data))
    		{
    			/* Extract data */
    			$clean_data			= $event_form->getValues();
    			
    			$event_title		= getArrayVar($clean_data, 	'event_title');
    			$event_desc			= getArrayVar($clean_data,	'event_desc');
				$event_start_date	= getArrayVar($clean_data,	'event_start_date');
				$event_stop_date	= getArrayVar($clean_data,	'event_stop_date');
				$event_group_id		= getArrayVar($clean_data,	'group_id');
				$event_image		= null;
				
				if ($event_form->event_image->receive())
        		{
        			if ($file_name = $event_form->event_image->getFileName())
        			{
	        			if ($file_name)
	        			{
	        				$event_image		= $file_name;
	        				
	        			}
        			}
        		}
    			
    			if ($events_obj->editEvent($event_id, $event_title, $event_desc, $event_start_date, $event_stop_date, $event_group_id, $event_image))
    			{
    				/* Event added successfully */
    				$this->view->successmsg = 'Event updated <strong>successfully!</strong>';
    				
    				/* Remove the form */
    				$this->view->form		= null;
    			}
    			else
    			{
    				$this->_eventsErrors[]	= 'Sorry!<br />There was a <strong>problem</strong> editing the event!';
    			}
    			
    		}// form is valid
    		else
    		{
    			/* Get form errors */
    			$event_form->populate($raw_data);
    			
    			$this->_eventsErrors[]	= 'Some fields of this form appears not to be valid!';
    			
    			
    		}// form not valid
    		
    		$errors = $event_form->getMessages();
    		$preparedErrors = array();
    		if (is_array($errors))
    		{
	    		foreach($errors as $element => $msg)
				{
		    		if (is_array($msg))
		    		{
			    		foreach($msg as $msg_this)
						{
				    		$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg_this;
						}
		    		}
		    		else
			    		$preparedErrors[] = '<strong><em>' . $element . '</em></strong>: ' . $msg; 	
				}
    		}
    		
    		$this->_eventsErrors =  array_merge($this->_eventsErrors, $preparedErrors);
    		
    	}// Is POST request
    	else
    	{
    		
    		$event_form->getElement('event_title')->setValue();
    	}	
    	
    	$this->view->form	= $event_form;
    	
    }

    public function removeeventAction()
    {
    	
    }
    
    
	protected function _getRecentEvents()
	{
		
	}

}



