<?php

class WordController extends Point_Controller_Action
{
	
	/**
	 * @var	
	 */
	protected		$_user_priviledge	= 'guest';
	
	/**
	 * @var	
	 */
	protected		$_wordErrors		= array();

	/**
	 * @var	
	 */
	protected		$_sermons_group_id		= null;

	/**
	 * @var	
	 */
	protected		$_sermons_author_images_root = APP_SERMON_AUTHORS_IMAGES_DIRECTORY;

    public function init()
    {
        /* Initialize action controller here */
        
        /* Retrieve the sermon ID */
	    $contentGroups	= Point_Model_ContentGroups::getInstance();
	    
        $sermonGroupId	= $contentGroups->getGroupIdByKeyword('sermon');
        if (!is_numeric($sermonGroupId))
	        throw new Exception('Unable to find a matching sermon table');
		    
		$this->_sermons_group_id = $sermonGroupId;
    }

	protected function _getBaseUrl()
	{
		$request			= $this->getRequest();
		
		return $request->getScheme() . '://' . $request->getHttpHost();	
	}
	
	protected function _getSermonUrl($sermon)
	{
		return 		$this->_getBaseUrl() . '/word/sermons?f='. $this->view->escape($sermon['sermon_id'] . '/' . date('Y/m/d', strtotime( $sermon['sermon_date'] ) ) . '/'.$sermon['sermon_seo_title']);
	}
	
    public function indexAction()
    {
		$request 		= $this->getRequest();
		
		$sermons_obj	= Point_Model_Sermons::getInstance();
		if ($file 		= $request->getParam('f', null))
		{
			$this->_redirect('/word/latestsermon'. $sermons_obj->getLatestUrl());
//			echo '/word/latestsermon'. $sermons_obj->getLatestUrl();
//			exit;
		}			
			
		/* Retrieve all the sermons */
		$start 			= $request->getParam('s', 0);
		
		$amount			= $request->getParam('m', 10);
		
		$found_sermons	= $sermons_obj->getSermons($start, $amount);
		
		$baseUrl		= $this->_getBaseUrl();
		
		$this->_setTitle('All Sermons');
		
		if (!empty($found_sermons))
		{
			/* For each found sermons make the view link and remove link */
			foreach($found_sermons as &$sermon)
			{
				$sermon_url 			= $this->_getSermonUrl($sermon);
				$sermon_remove_url 		= $baseUrl.'/word/removesermon?s_id='.$sermon['sermon_id'];
				$sermon['sermon_url'] 	= $sermon_url;	
				$sermon['sermon_remove_url'] 	= $sermon_remove_url;	
			}
			
			$this->view->sermons 		= $found_sermons;
		
		
			/* Allow editing if user is a within sermon editors group */
//			$contentGroups	= Point_Model_ContentGroups::getInstance();
//			
//			$user_id	 	= Point_Model_User::getInstance()->getUserId();
//			
//			$sermonGroupId 	= $this->_sermons_group_id;
//			
			$user_access 	= $this->_user_priviledge;
			
			$this->view->edit_access	= $user_access != Point_Model_ContentGroups::GROUP_GUEST ? true : false;
			
			$user_access 	= $this->_user_priviledge; // Defined in preDispatch()
		    
		    /* As long as you are permitted to moderate sermon */
		    if (Point_Model_ContentGroups::GROUP_EDITOR == $user_access || 
		    	Point_Model_ContentGroups::GROUP_MODERATOR == $user_access  || 
		    	Point_Model_ContentGroups::GROUP_ADMIN == $user_access )
		    {
//			    $this->view->edit_link = $this->view->url(array('controller'=> 'word', 'action'=>'editsermon', 's_id'=>$sermon['sermon_id']), false, null);
		    }
		    else
		    {
//		    	$this->_redirect('/word/');	
		    }
		}
		
		  
	    
    }
    
    public function preDispatch()
    {
    	/* Allow editing if user is a within sermon editors group */
	    $contentGroups	= Point_Model_ContentGroups::getInstance();
	    
	    $user_id	 	= Point_Model_User::getInstance()->getUserId();
	    
	    $sermonGroupId 	= $this->_sermons_group_id;
	    
	    $user_access 	= $contentGroups->getMembership($user_id, $sermonGroupId);
	    
	    $this->_user_priviledge	= $user_access;
    }
    
    public function sermonsAction()
    {
    	$file  	= null;	
	    
	    $sermons 	= Point_Model_Sermons::getInstance();
	    //		$this->getResponse()->setUri('testing/test');
	    /**
	     *  Get incoming parameters 
	     *---------------------------*/
	    $request = $this->getRequest();
	    
	    $file 			= $request->getParam('f', null);
	    
//	    $this->_disableLayout();
	    
	    /* Redirect user to latest known article */
	    if (!$file)
		    $this->_redirect('word/sermons'. $sermons->getLatestUrl());
	    $fullUrl = $this->_getBaseUrl() . $this->view->url().'/?f='.$file;
	    
	    $sermon =	$sermons->getSermon(array('file'=> $file), true);
	    
		
		
		if (!$sermon)
	    {
	    	$all_parts	= array_map('trim', explode('/', $file));
	    	if (is_array($all_parts))
		    {
		    	if (null == ($sermon = $sermons->getSermonById($file[0]))) /*$file[0] is supposed to be sermon id*/
		    	{
			    	$this->_redirect('word/sermons'. $sermons->getLatestUrl());	
		    	}
		    }
	    }
	    else
	    {
		    $this->_scrubBibleReferences($sermon);
		    
		    /* setup view stuff */
		    $image_path  =  APP_PUBLIC_DIRECTORY . APP_IMAGES_DIRECTORY . $sermon['author_image'];
		    
		    $w = $h = $t = $at = $new_width = $new_height = null;
		    $image_obj				= Point_Model_Picture::getInstance();
		    list($w, $h, $t, $at) 	= $image_obj->getImageInfo($image_path);
		    $w = $w == null ? APP_IMAGES_THUMB_WIDTH 	: $w;
		    $h = $h == null ? APP_IMAGES_THUMB_HEIGHT 	: $h;
		    
		    list($new_width, $new_height) =  $image_obj->getResizeDimension($w, $h);

		    $image_path					= '/'. APP_IMAGES_DIRECTORY . $sermon['author_image'];
		    if (is_file(remove_trailing_slash(APP_PUBLIC_DIRECTORY) . $image_path))
		    {
		    	$sermon['speaker_image']	= $this->_getBaseUrl(). $image_path;
		    	
		    	$sermon['speaker_image_w']	= $new_width;
		    	$sermon['speaker_image_h']	= $new_height;
		    }
		    
		    $sermon['sermon_author_view'] 	= $this->_getBaseUrl(). '/word/speakerprofile/?sp='.$sermon['sermon_author_id']; 
		    $this->view->author 		= $sermon['sermon_author']; 
		    $this->view->fullUrl 		= $fullUrl; 
		    
		    
		    $user_access 	= $this->_user_priviledge; // Defined in preDispatch()
		    
		    /* As long as you are permitted to edit sermon */
		    if (Point_Model_ContentGroups::GROUP_EDITOR == $user_access || 
		    	Point_Model_ContentGroups::GROUP_MODERATOR == $user_access  || 
		    	Point_Model_ContentGroups::GROUP_ADMIN == $user_access )
		    {
			    $this->view->edit_link = $this->view->url(array('controller'=> 'word', 'action'=>'editsermon', 's_id'=>$sermon['sermon_id']), false, null);
		    }
		    
		    /* Get navigation calendar */
//		    $date			= array('month' => date('n', strtotime($sermon['sermon_date']) ),
//		    						'year'	=> date('Y', strtotime($sermon['sermon_date'])) );
//		    						
		    $date			= array('month' => date('n', strtotime($sermon['sermon_date']) ),
		    						'year'	=> date('Y', strtotime($sermon['sermon_date']) ));
		    						
		    $this->view->calendar 	= $this->_getCalendarNav($date);
		    
		    $this->view->sermon_prev = $this->_getPrevSermonLink($sermon['sermon_id']);
		    $this->view->sermon_next = $this->_getNextSermonLink($sermon['sermon_id']);
		    
		    $this->_setPageDescription(getShowcaseText(remove_html_tags($sermon['sermon_highlight'])), 400);
		    
		    /**
		     * Get the recent sermons
		     */
		    if ($recent_sermons				= $sermons->getSermons(0, 6))
		    {
		    	/*Append URL*/
		    	foreach ($recent_sermons as $key => &$recent_sermon)
		    	{
		    		/* Remove current sermon if in recent */
		    		if ($recent_sermon['sermon_id'] == $sermon['sermon_id'])
		    		{
		    			unset($recent_sermons[$key]);
		    			continue; /* Skip this */
		    		}
		    		else
		    		{
			    		$recent_sermon['sermon_url']	=  $this->_getSermonUrl($recent_sermon);		
		    		}
		    		
		    	}
		    		
		    	$this->view->recent_sermons	= $recent_sermons; 
		    } 
		    
		    /**
		     * Handle the comments
		     */
		    $sermons_comment_obj	= Point_Model_Comments_SermonComments::getInstance();
		    $sermon_comment_form	= $sermons_comment_obj->getCommentForm();
		    
		    if($request->isXmlHttpRequest())
		    {
			    /* we have recieved Ajax response */
		    }
		    else
		    {
			    $this->view->sermon = $sermon;
			    if (!empty($sermon) && array_key_exists('sermon_title', $sermon))
			    {
				    $this->_setTitle('Sermons');
				    $this->_enableSocialSharing();
			    }
		    }
	    }
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
	    $sermons_obj	= Point_Model_Sermons::getInstance();
	    
	    $month			= getArrayVar($date, 'month', date('n'));
	    $year			= getArrayVar($date, 'year', date('Y'));
	    
	    
	    /* get */
	    $url			= array();
	    $month_info		= array('month' => $month,
								'year'  => $year);
	    
	    if ($month_messages	= $sermons_obj->getSermonsInMonth($month))
	    {
	    	/* Construct the calendar array from result */
	    	
	    	foreach ($month_messages as $this_sermon)
	    	{
	    		
	    		$url[] 		= array('date' => $this_sermon['sermon_date'], 
									'data' => array( 'title'	=> $this_sermon['sermon_title'],
													 'href'		=> $this->_getSermonUrl($this_sermon), 
													 'class'	=> 'sday')); 
	    	}
	    	
			
//	    	echo $calender->getCalendar($date, $url);
	    } 
//	    echo '<pre>'.print_r($month_info , true).'</pre>';
	    /* Add the navigation */
	    $cal_nav_top			= null;
	    $calendar_name_html		= wrapHtml(date('F Y', mktime(0, 0, 0, $month, 1, $year)), 'span', array('class' => 'month-name'));
	    $cal_prev				= wrapHtml('&lt;&lt;', 'a', array('class'=>'mon-nav'/*, 'href' => $this->_getPrevSermonUrl()*/));
	    $cal_next				= wrapHtml('&gt;&gt;', 'a', array('class'=>'mon-nav'/*, 'href' => $this->_getPrevSermonUrl()*/));
	    $cal_nav_top		 	= wrapHtml($cal_prev . $calendar_name_html . $cal_next, 'div', array('class' => 'cal-top'));
	    
	    $calendar_html			= $cal_nav_top . $calendar->getCalendar($month_info, $url, array(), true);
	    
	    return $calendar_html;
		
    }
    
    /**
     * This gets the link to previous sermon
     */
    protected function _getPrevSermonLink( $sermon_id )
    {
    	$sermons_obj	= Point_Model_Sermons::getInstance();
	 
	    $prev_sermon	= $sermons_obj->getPrevSermon($sermon_id);
    	    	
    	if ($prev_sermon)
    	{
    		$url 		= '?f='. $prev_sermon['sermon_id']. '/'. date('Y/m/d', strtotime($prev_sermon['sermon_date']));
			$url		.= '/'. $prev_sermon['sermon_seo_title'];
    		
    		return wrapHtml('&lt;&lt; ' . $prev_sermon['sermon_title'], 'a', array('href' => $this->view->fullUrl(array('action'=>'sermons'), false, null) . $url , 
											   'title'=> $prev_sermon['sermon_highlight']) );
    	}
    }
    
    /**
     * This gets the link to next sermon
     */
    protected function _getNextSermonLink( $sermon_id )
    {
	    $sermons_obj	= Point_Model_Sermons::getInstance();
	 
	    $next_sermon	= $sermons_obj->getNextSermon($sermon_id);
	    
    	if ($next_sermon)
    	{
    		$url 		= '?f='. $next_sermon['sermon_id']. '/'. date('Y/m/d', strtotime($next_sermon['sermon_date']));
			$url		.= '/'. $next_sermon['sermon_seo_title'];

    		return wrapHtml($next_sermon['sermon_title'] .  ' &gt;&gt;', 'a', array('href' => $this->view->fullUrl(array('action'=>'sermons'), false, null) . $url , 
											   	  'title'=> $next_sermon['sermon_highlight']) );
    	}
    }

    public function devotionalAction()
    {
        // action body
    }

    public function sermonsummaryAction()
    {
        // action body
    }

    public function addsermonAction()
    {
        /* Prepare the form */
        $sermonForm		= new Point_Form_SermonForm();
        $tinymce 		= new Point_Form_TinyMCE($sermonForm);
        
        /* Get the request */
        $request 		= $this->getRequest();
    	$sermon_obj		= Point_Model_Sermons::getInstance();
    	$user			= Point_Model_User::getInstance();
    	
    	/* Check if user is either an editor or admin */
    	
    	$user_access	= $this->_user_priviledge;
    	
 
   		$this->_setTitle('Add New Sermon');
   		
   		$editor_raw_content	= $request->getParam('tinymcetarea',null);
   		
   		
   		$raw_data			= $request->getPost();
	    
	    $this->view->form	= $sermonForm;
   		/* We received a post call */
   		if ($request->isPost())
   		{
	   		/* check if form is valid */
	   		if($sermonForm->isValid($raw_data))
	   		{
		   		$clean_data = $sermonForm->getValues();		
		   		
		   		$sermon_speaker1 		= $clean_data['speaker'];
		   		$sermon_title 			= $clean_data['sermon_title'];
		   		$sermon_date 			= date('Y-m-d H:i:s', strtotime($clean_data['sermon_date']));
		   		$sermon_highlights 		= $clean_data['sermon_highlights'];
		   		$sermon_content 		= $clean_data['tinymcetarea'];
		   		
//		   		echo '<pre>', print_r($clean_data, true),'</pre>';exit;

//		   		/* Clean up tags */
//		   		$article_tags 		= cleanReplace($article_tags, null, array(' ') /* Allow spaces */); /* Remove funystuff */
//		   		$article_tags 		= explode(',', $article_tags); /* make array form */
		   		
		   		/* Check if we have a post */
		   		if ($request->getParam('submit', null))
		   		{
			   		/* check if form is valid */
			   		/**
			   		 * addNew($title, $content , $author_id , $sermon_date ,$sermon_highlight = null,  $audio_id = null , $video_id = null )
			   		 */
			   		/* Fetch sermon speaker from DB */
			   		$sermon_speaker_id		= null;
			   		if (is_numeric($sermon_speaker1))
			   		{
			   			$sermon_speaker		= $sermon_obj->getSermonSpeaker($sermon_speaker1);
			   			$sermon_speaker_id	= $sermon_speaker['sermon_author_id'];
			   		}

			   		$result			=  	$sermon_obj->addNew(
				   							$sermon_title,
									   		$sermon_content,
											$sermon_speaker_id,
											$sermon_date, 
											$sermon_highlights
											
										/* TODO: Audio ID   */  
			   							/* TODO: Video ID */ );
			   		
			   		/* If valid attempt to add new article */
			   		if (false !==$result)
			   		{
				   		$msg 		= 'Sermon added successfully';
				   		$url		= $sermon_obj->getSermonUrl((int)$result);
				   		$fullUrl 	= $request->getScheme().'://'.
				   					  $request->getHttpHost() . 
				   					  $request->getControllerName().'/'.
				   					  $request->getActionName(). $url;
				   		
				   		$this->view->fullUrl = $fullUrl;
				   		$this->view->successmsg = $msg;
				   		
				   		/**
				   		 * =====================
				   		 * POST to SOCIAL NETWORK
				   		 * e.g facebook
				   		 */
				   		$social_agent	= Point_Model_Robots_SocialUpdate::getInstance();
				   		
				   		$this->_redirect('word/sermons' . $url);
			   		}else
			   		{
				   		$this->_wordErrors[] = 'Unable to add new sermon.';
			   		}
		   		}
		   		else if ( $request->getParam('preview', null))
		   		{
			   		
			   		/* setup view stuff */
			   		$sermonForm->populate($clean_data);
			   		$this->view->preview_speaker 		= $clean_data['speaker'];
			   		
			   		$this->view->preview_title 			= $clean_data['sermon_title'];
			   		$raw_sermon['sermon_content']		= $raw_data['tinymcetarea'];
	    			$this->view->preview_sermon 		= $this->_scrubBibleReferences($raw_sermon['tinymcetarea']);
			   		$this->view->preview_time 			= strtotime($clean_data['sermon_date']);
			   		$this->view->preview_hightlights	= $clean_data['sermon_highlights'];
		   		}
	   		}
	   		else{
		   		/* Invalid form */
		   		
		   		
		   		$errors = $sermonForm->getMessages();
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
		   		/* Add the prepared errors */
		   		$this->_wordErrors =  $preparedErrors;
		   		
		   		$this->_wordErrors[] = 'Invalid or Incomplete form received.';
	   		}
	   		
	    	
	    	/* Prepare editing form */
	    	
	    	$sermonForm->populate($request->getPost());
	    	$article_textarea	= $sermonForm->getElement('tinymcetarea');
	    	$article_textarea->setValue($editor_raw_content);
	    
	    	
	    	
	    	    	
    	if (!empty($this->_wordErrors))
    		$this->view->errormsgs = $this->_wordErrors;    
    	}
    	$this->view->form 	=	$sermonForm;
    	$this->view->sermon_editor =	$tinymce->makeForm($editor_raw_content, 'sermon-'. genRandomNumString(2) );
    	
    }
    
    public function editsermonAction()
    {
    	$request 		= $this->getRequest();
    	$sermons_obj	= Point_Model_Sermons::getInstance();
    	
    	/* Allow editing if user is a within sermon editors group */
    	
    	$user_access = $this->_user_priviledge;
    	
    	if ($sermon_id = $request->getParam('s_id', null))
    	{
    		/* As long as you are permitted to edit sermon */
		    
		    if (Point_Model_ContentGroups::GROUP_EDITOR == $user_access || 
		    	Point_Model_ContentGroups::GROUP_MODERATOR == $user_access  || 
		    	Point_Model_ContentGroups::GROUP_ADMIN == $user_access )
		    {
    			$this->_setTitle('Edit Sermon');
    			/* retrieve form */
    			$sermon 			= 	$sermons_obj->getSermonById($sermon_id);	
    			/* Prepare editing form */
    			$sermonForm 	 	= new Point_Form_SermonForm();
    			/* Customize Form */
    			$sermonForm->submit->setLabel('Update Sermon');
    			$tinymce 			= new Point_Form_TinyMCE($sermonForm);

				/* Fill default */
    			$sermonForm->getElement('sermon_title')->setValue($sermon['sermon_title']);
    			$article_textarea	= $sermonForm->getElement('tinymcetarea');
    			$article_textarea->setValue($sermon['sermon_content']);
     			
     			$sermonForm->getElement('speaker')->setValue($sermon['sermon_author_id']);
     			$sermonForm->getElement('sermon_date')->setValue($sermon['sermon_date']);
     			$sermonForm->getElement('sermon_highlights')->setValue($sermon['sermon_highlight']);
     			$this->view->form 			=	$sermonForm;
     			$sermon['sermon_url']		=	$this->_getSermonUrl($sermon);
    			$this->view->sermon			=	$sermon;
    			$this->view->sermon_editor 	=	$tinymce->makeForm($sermon['sermon_content'], 'sermon-'.$sermon_id );
    		
    			
    			/* Check if it's Preview */
    			if ($request->isPost() && $request->getParam('preview', null))
    			{
    				/* Submit coming through */
    				$raw_data = $request->getPost();
    				/* setup view stuff */
	    			$sermonForm->populate($request->getPost());
	    			$this->view->preview_speaker 		= $raw_data['speaker'];
	    			
	    			$this->view->preview_title 			= $raw_data['sermon_title'];
	    			$raw_sermon['sermon_content']		= $raw_data['tinymcetarea'];
	    			$this->view->preview_sermon 		= $this->_scrubBibleReferences($raw_sermon['tinymcetarea']);
	    			
	    			$article_textarea->setValue($raw_data['tinymcetarea']);
	    			$this->view->sermon_editor 			=	$tinymce->makeForm($raw_data['tinymcetarea'], 'sermon-'.$sermon_id );
	    			
	    			$this->view->preview_time 			= strtotime($raw_data['sermon_date']);
	    			$this->view->preview_hightlights	= $raw_data['sermon_highlights'];
	    			
    			}
    			/* Handle a change of mind */
    			else if ($request->isPost() && $request->getParam('cancel', null))
    			{
					/* Redirect to post */
					$file		= $sermons_obj->getSermonUrl((int)$sermon_id);
					$fullUrl 	= $this->_getBaseUrl() . '/' 
									. $request->getControllerName().'/'
									. 'sermons'. $file;
//					echo $fullUrl; exit;
	    			$this->_redirect($fullUrl);
	    			
    			}
    			else if ($request->isPost() && $request->getParam('submit', null))
    			{
    				/* Submit coming through */
    				$raw_data = $request->getPost();
    				if ($sermonForm->isValid($raw_data))
    				{
	    				
	    				$clean_data = $sermonForm->getValues();		
		   		
	    				$sermon_speaker 		= $clean_data['speaker'];
	    				$sermon_title 			= $clean_data['sermon_title'];
	    				$sermon_date 			= date('Y-m-d H:i:s', strtotime($clean_data['sermon_date']));
	    				$sermon_highlights 		= $clean_data['sermon_highlights'];
	    				$sermon_content 		= $clean_data['tinymcetarea'];
	    				
	    				
	    				
//	    				/* Clean up tags */
//	    				$article_tags 		= cleanReplace($article_tags, null, array(' ') /* Allow spaces */); /* Remove funystuff */
//	    				$article_tags 		= explode(',', $article_tags); /* make array form */
//	    				
						//updateSermon($sermon_id, $title, $content ,  $author_id, $sermon_date, $sermon_highlight = null ,$audio_id = null , $video_id = null )

/* TODO: Seems to be a waste!!! */
//	    				/* Fetch sermon speaker from DB */
//	    				$sermon_speaker_id		= null;
//	    				if (is_numeric($sermon_speaker))
//	    				{
//		    				$ret_sermon_speaker	= $sermons_obj->getSermonSpeaker($sermon_speaker);
//		    				$sermon_speaker_id	= $ret_sermon_speaker['sermon_author_id'];
//	    				}
//	    				
	    				$result = $sermons_obj->updateSermon(
	    									$sermon_id,
	    									$sermon_title,
	    									$sermon_content,
	    									(int)$sermon_speaker, 
	    									$sermon_date,
	    									$sermon_highlights 
	    									/* TODO: Audio ID   */
	    									/* TODO: Video ID */ );
	    				if (false !==$result)
	    				{
	    					$msg = 'Sermon updated successfully';
	    					if ($result > 0)
	    						$url		= $sermons_obj->getSermonUrl((int)$result);
	    					else
	    						$url		= $sermons_obj->getSermonUrl((int)$sermon_id);
	    						
	    					$fullUrl 	= $this->_getBaseUrl() . '/' .
											$request->getControllerName().'/'.
											'sermons'. $url;
	    					$this->view->fullUrl = $fullUrl;
	    					$this->view->successmsg = $msg;
	    			
	    					/* Redirect to new article*/
//	    					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
//	    					$redirector->gotoUrl($uri->incoming_uri)->redirectAndExist();
//	    					$this->_redirect('word/' . $url);
		    				//$this->_redirect('word/' . $sermons_obj->getSermonUrl($sermon_id));
	    				}else
	    				{
	    					$this->_wordErrors[] = 'Unable to edit Sermon.';
	    				}
    				}
    				else
    				{
	    				
	    				/* setup view stuff */
	    				
	    				$errors = $sermonForm->getMessages();
	    				$preparedErrors = array();
	    				
	    				$sermonForm->populate($raw_data);
	    				
	    				
	    				
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
	    				//$preparedErrors [] = $msgs;
	    				$this->_wordErrors =  $preparedErrors;
	    				$this->_wordErrors[] = 'Invalid or incomplete form submitted';
    				}
    				
    				
    			}
    			
    		}
    		else
    		{
    			/* User is not an editor !!! */
    			$this->_forward('index', null,array('f' => $sermons_obj->getSermonUrl()));
    		}
    	}else
    		$this->view->error_message = 'Unknown Sermon';
    	if (!empty($this->_wordErrors))
    		$this->view->errormsgs = $this->_wordErrors;
    }

	public function removespeakerAction()
	{
		$request 	= $this->getRequest();
		
		/* Get incoming param */
		$speaker_id 	= $request->getParam('sp' , null);
		
		if ($speaker_id)
		{
			$this->_setTitle('Remove Speaker');
			
			$sermons_obj	= Point_Model_Sermons::getInstance();
			
			$found_speaker	= $sermons_obj->getSermonSpeaker($speaker_id);
			
					
			if ($found_speaker)
			{
				/* Create a remove form */
				$remove_form		= new Point_Form_SpeakerRemove();
				$remove_form->setAction('/word/removespeaker/');
				$remove_form->getElement('sp')->setValue($speaker_id);
		
				/* Make url just for extra confirmation */
				$baseUrl		= $this->_getBaseUrl();
				
				$speaker_url	= $this->_getSpeakerViewUrl($speaker_id);
									
				$found_speaker['speaker_url'] 		= $speaker_url;
				$found_speaker['speaker_fullname'] 	= $found_speaker['author_firstname'] . ' ' . $found_speaker['author_lastname'];
				
				$this->view->speaker = $found_speaker;
				
				$this->view->form	= $remove_form;
				
				
				if ($request->isPost() && $request->getParam('remove', false))
				{
					/* We have a valid removal request... so let's do it :) */
					if ($sermons_obj->removeSpeaker($speaker_id))
					{
						$successmsg 	= 'Speaker\'s profile removed <strong>successfully</strong>';
						
						$this->view->successmsg = $successmsg;
						
						/* Remove form from view */
						$this->view->form		= false; 
					}
				}
				else if ($request->isPost() && $request->getParam('cancel', false))
				{
					/* Editor changed his/mind */
					
					/* Redirect to speaker's profile */
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($speaker_url)->redirectAndExist();
					
				}
				else
				{
					/* Add sermon id to param and re-show */
					$remove_form->getElement('sp')->setValue($speaker_id);	
				}
				
				
				
			}
		}
		else
		{
			$this->_wordErrors[] = 'No Speaker\'s profile was selected for removal.<br /><br />Please select a Speaker\'s profile to remove';	
		} 
		
		if (!empty($this->_wordErrors))
    		$this->view->errormsgs = $this->_wordErrors;
	}
	
	public function removesermonAction()
	{
		$request 	= $this->getRequest();
		
		/* Get incoming param */
		$sermon_id 	= $request->getParam('s_id' , null);
		
		if ($sermon_id)
		{
			$this->_setTitle('Remove Sermon');
			
			$sermons_obj	= Point_Model_Sermons::getInstance();
			
			$found_sermon	= $sermons_obj->getSermonById($sermon_id);
			
					
			if ($found_sermon)
			{
				/* Create a remove form */
				$remove_form		= new Point_Form_SermonRemove();
		
				/* Make url just for extra confirmation */
				$baseUrl		= $this->_getBaseUrl();
				
				$sermon_url		= $baseUrl.'/word/sermons?f='. $this->view->escape( 
										$found_sermon['sermon_id'] . '/' .
										date('Y/m/d', strtotime( $found_sermon['sermon_date'] ) ) . '/'.$found_sermon['sermon_seo_title']
									);
									
				$found_sermon['sermon_url'] = $sermon_url;
				
				$this->view->sermon = $found_sermon;
				
				$this->view->form	= $remove_form;
				
				
				if ($request->isPost() && $request->getParam('remove', false))
				{
					/* We have a valid removal request... so let's do it :) */
					if ($sermons_obj->remove($sermon_id))
					{
						$successmsg 	= 'Sermon removed <strong>successfully</strong>';
						
						$this->view->successmsg = $successmsg;
						
						/* Remove form from view */
						$this->view->form		= false; 
					}
				}
				else if ($request->isPost() && $request->getParam('cancel', false))
				{
					/* Editor changed his/mind */
					
					/* Redirect to new article*/
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl('/word/')->redirectAndExist();
					
				}
				else
				{
					/* Add sermon id to param and re-show */
					$remove_form->getElement('s_id')->setValue($sermon_id);	
				}
				
				
				
			}
		}
		else
		{
			$this->_wordErrors[] = 'No valid sermon selected for removal.<br /><br />Please select a sermon to remove';	
		} 
		
		if (!empty($this->_wordErrors))
    		$this->view->errormsgs = $this->_wordErrors;
	}
	
    public function audioAction()
    {
        // action body
    }

    public function videoAction()
    {
        // action body
    }
	
    public function speakerprofileAction()
    {
        $request 						= $this->getRequest();
		
		/* Get incoming param */
		$speaker_id 					= $request->getParam('sp' , null);
		
		if (is_numeric($speaker_id))
		{
			$sermons_obj				= Point_Model_Sermons::getInstance();
			$speaker_info				= $sermons_obj->getSermonSpeaker($speaker_id);
			/* If Speaker found */
			if ($speaker_info)
			{
				$speaker_name 			= $speaker_info['author_firstname'] . ' ' . $speaker_info['author_lastname'];
				$speaker_info['speaker_name'] = $speaker_name;
				$this->_setTitle($speaker_name . '\'s Profile');
				/**
        		 * ------------------------------------
        		 * Add the url for the image 
        		 */
        		$author_image				= $this->_getSpeakerImage($speaker_info['author_image'], 200, 200);
        		$speaker_info				= array_merge($speaker_info, $author_image);
        		$last_sermon				= $sermons_obj->getLastSermonBySpeaker($speaker_info['sermon_author_id']);
        		$last_sermon['sermon_url']	= $this->_getSermonUrl($last_sermon);
        		$speaker_info['last_sermon']= $last_sermon;
        		$speaker_info['all_sermons']= $sermons_obj->getAllSermonBySpeaker($speaker_info['sermon_author_id']);
        		$speaker_info['speaker_edit_url']	= $this->_getSpeakerEditUrl($speaker_id);
        		$speaker_info['speaker_remove_url']	= $this->_getSpeakerRemoveUrl($speaker_id);
        		
        		if (!empty($speaker_info['all_sermons']))
        		{
	        		foreach($speaker_info['all_sermons'] as &$this_sermon)
					{
		        		$this_sermon['sermon_url']	=$this->_getSermonUrl($this_sermon);
					}
        		}
        		$user			= Point_Model_User::getInstance();
        		$contentGroups	= Point_Model_ContentGroups::getInstance();
        		
        		/* Check if user is either an editor or admin */
        		$user_access	= $contentGroups->getMembership($user->getUserId(), $sermons_obj->getContentGroupId());
        		
        		if (Point_Model_ContentGroups::GROUP_EDITOR 	== $user_access	|| 	
	        		Point_Model_ContentGroups::GROUP_ADMIN 		== $user_access)
        		{
	        		$this->view->edit_access	=  $user_access;
        		}
        		
				$this->view->speaker 	= $speaker_info;
			}
		}
    }
	
	public function getmonthAction()
	{
		
	}
	
	public function editspeakerAction()
	{
		/* Prepare the form */
//       $speakerForm		= new Point_Form_SpeakerForm();
       
        /* Get the request */
        $request 		= $this->getRequest();
    	
    	/* Check if user is either an editor or admin */
    	$user_access	= $this->_user_priviledge;
//    	$contentGroups	= Point_Model_ContentGroups::getInstance();
    	 
		if (Point_Model_ContentGroups::GROUP_GUEST == $user_access)
			$this->_redirect('error/noaccess');    	
 
   		$this->_setTitle('Edit Speaker\'s Profile');
   		
   		if ($speaker_id = $request->getParam('sp', null))
    	{
    		/* As long as you are permitted to edit speaker */
		    
		    if (Point_Model_ContentGroups::GROUP_EDITOR == $user_access || 
		    	Point_Model_ContentGroups::GROUP_MODERATOR == $user_access  || 
		    	Point_Model_ContentGroups::GROUP_ADMIN == $user_access )
		    {
    			/* retrieve speaker info */
    			$sermon_obj		= Point_Model_Sermons::getInstance();
    			$speaker 			= 	$sermon_obj->getSermonSpeaker($speaker_id);	
    			/* Prepare editing form */
    			$speakerForm 	 	= new Point_Form_SpeakerForm();
    			/* Customize Form */
    			$speakerForm->submit->setLabel('Update Speaker');
    			$speakerForm->setAction('/word/editspeaker');
        
    			 /* Add Remove image to form */
    			 $remove_image		= new Zend_Form_Element_Checkbox('removeimage');
    			 $remove_image->setLabel(false)
							 ->setRequired(false)
							 ->setOptions(array(
								 'size'	=>	'50',
								 'class'	=>	'input',
								 'title'	=>	'Remove Speaker\'s image',
								 'id'	=>	'remove-speaker-image'
							 ))
							 ->setDecorators($speakerForm->elementDecorators);
    			 
    			 $speakerForm->addElement($remove_image);
    			 
				
				/* Fill default */
    			$speakerForm->getElement('firstname')->setValue($speaker['author_firstname']);
    			$speakerForm->getElement('lastname')->setValue($speaker['author_lastname']);
    			$speakerForm->getElement('email')->setValue($speaker['author_email']);
    			$speakerForm->getElement('sp')->setValue($speaker_id);
    			
    			/* Prepare for redirect */
    			$this->view->speakerUrl	= $this->_getSpeakerViewUrl((int)$speaker_id);
    			
    			$this->view->speaker_image		= $this->_getSpeakerImage($speaker['author_image'], 130, 130);
    			$this->view->speaker_firstname	= $speaker['author_firstname'];
    			$this->view->speaker_lastname	= $speaker['author_lastname'];
    			
     			$this->view->form 	=	$speakerForm;
    			
    			
    			if ($request->isPost() && $request->getParam('submit', null))
    			{
    				/* Submit coming through */
    				$raw_data = $request->getPost();
    				
    				/* check if form is valid */
    				if($speakerForm->isValid($raw_data))
    				{
	    				$clean_data = $speakerForm->getValues();		
	    				
	    				$speaker_firstname 		= $clean_data['firstname'];
	    				$speaker_lastname 		= $clean_data['lastname'];
	    				$speaker_email 			= $clean_data['email'];
	    				$speaker_image			= null;
	    				/* If image already exists and is the same as incoming image-filename, ignore */
	    				
	    				if (!$clean_data['removeimage'] && $speaker['author_image'])
	    				{
	    					/* Restore old image */
	    					$speaker_image			= $speaker['author_image'];
	    				}
	    				elseif ($clean_data['removeimage'] && $speaker['author_image'])
	    				{
	    					/* Remove old image first */
	    					$img_root			= APP_PUBLIC_DIRECTORY.  APP_IMAGES_DIRECTORY ;
	    					$image_path			= $img_root . $speaker['author_image'];
	    					echo $image_path, 'part 2'; exit;
	    				}
	    				
	    				
	    				if (!$clean_data['removeimage'] && $speakerForm->image->receive())
	    				{
		    				if ($file_name = $speakerForm->image->getFileName())
		    				{
			    				if (is_array($file_name) && !empty($file_name))
			    				{
				    				/* Take the topmost */
				    				$file_name 	= $file_name[0]; 
			    				}
			    				
			    				/* Do the storage; etc */
			    				$pixObject	= Point_Model_Picture::getInstance();
			    				
			    				$description 		= $speaker_firstname . ' ' . $speaker_lastname;
			    				$destination_fname	= makeSeoString(snipByWords($description , 50)); 
			    				$img_root			= APP_PUBLIC_DIRECTORY.  APP_IMAGES_DIRECTORY ;
			    				$newSize 	= array();
			    				$info		= array('source' 		=> $file_name,
				    				'destination'	=> $destination_fname,
									'description'	=> $description,
									'keep_ext'		=> false,
									'skip_db'		=> true,
									'dest_path'		=> $img_root . $this->_sermons_author_images_root			);
			    				
			    				list($result, $msg) = $pixObject->saveImage($info, $newSize);
			    				if (!$result)
				    				$this->_uploadErrors[] = $msg;
			    				else
			    				{	
				    				$speaker_image	= substr($msg , strlen($img_root));;
				    				/* Remove temporary image */	
				    				unlink($file_name);
			    				}
		    				}
	    				}
	    				
	    				$result			=  	$sermon_obj->updateSpeaker($speaker_id, $speaker_firstname, $speaker_lastname, $speaker_email, $speaker_image);
	    				
	    				/* If valid, attempt to add new article */
	    				if (false !==$result)
	    				{
		    				$msg 		= $speaker_firstname . '\'s profile was updated successfully';
		    				$this->view->successmsg = $msg;
		    				$this->view->form 		= null;
		    				
	    				}else
	    				{
		    				$this->_wordErrors[] = '<strong>Unable</strong> to update ' . $speaker_firstname . '\'s profile';
	    				}
	    				
    				}
    				else{
	    				/* Invalid form */
	    				
	    				
	    				$errors = $speakerForm->getMessages();
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
	    				/* Add the prepared errors */
	    				$this->_wordErrors =  $preparedErrors;
	    				
	    				$this->_wordErrors[] = 'Invalid or Incomplete form received.';
	    				
	    				/* Prepare editing form */
	    				
	    				$speakerForm->populate($request->getPost());
	    				
    				}
    				
    				
    			}
    			/* Handle a change of mind */
    			else if ($request->isPost() && $request->getParam('cancel', null))
    			{
	    			/* Redirect to post */
	    			$speaker_url		= $this->_getSpeakerViewUrl((int)$speaker_id);
	    			
	    			$this->_redirect($speaker_url);
	    			
    			}
    			
		    }
//    	}
   		
	    	
    	if (!empty($this->_wordErrors))
    		$this->view->errormsgs = $this->_wordErrors;    
    	}
    	
//    	$this->view->form 	=	$speakerForm;
	}
	
	public function addspeakerAction()
	{
		/* Prepare the form */
        $speakerForm		= new Point_Form_SpeakerForm();
        
        /* Get the request */
        $request 		= $this->getRequest();
        
    	$user			= Point_Model_User::getInstance();

    	$sermon_obj		= Point_Model_Sermons::getInstance();
    	
    	/* Check if user is either an editor or admin */
    	$user_access	= $this->_user_priviledge;
    	$contentGroups	= Point_Model_ContentGroups::getInstance();
    	 
		if (Point_Model_ContentGroups::GROUP_GUEST == $contentGroups->getMembership($user->getUserId(), $this->_sermons_group_id))
			$this->_redirect('error/noaccess');    	
 
   		$this->_setTitle('Add New Speaker');
   		
   		
   		$raw_data			= $request->getPost();
	    
	    $this->view->form	= $speakerForm;
	    
   		/* We received a post call */
   		if ($request->isPost())
   		{
	   		/* check if form is valid */
	   		if($speakerForm->isValid($raw_data))
	   		{
		   		$clean_data = $speakerForm->getValues();		
		   		
		   		$speaker_firstname 		= $clean_data['firstname'];
		   		$speaker_lastname 		= $clean_data['lastname'];
		   		$speaker_email 			= $clean_data['email'];
		   		$speaker_image			= null;
		   		
		   		if ($speakerForm->image->receive())
        		{
        			if ($file_name = $speakerForm->image->getFileName())
        			{
	        			if (is_array($file_name) && !empty($file_name))
	        			{
	        				/* Take the topmost */
	        				$file_name 	= $file_name[0]; 
	        			}
	        			
	        			/* Do the storage; etc */
	        			$pixObject	= Point_Model_Picture::getInstance();
	        			
	        			$description 		= $speaker_firstname . ' ' . $speaker_lastname;
	        			$destination_fname	= makeSeoString(snipByWords($description , 50)); 
	        			$root				= APP_PUBLIC_DIRECTORY.  APP_IMAGES_DIRECTORY ;
	        			$newSize 	= array();
	        			$info		= array('source' 		=> $file_name,
		        			'destination'	=> $destination_fname,
							'description'	=> $description,
							'keep_ext'		=> false,
							'skip_db'		=> true,
							'dest_path'		=> $root . $this->_sermons_author_images_root			);

	        			list($result, $msg) = $pixObject->saveImage($info, $newSize);
	        			if (!$result)
		        			$this->_uploadErrors[] = $msg;
	        			else
	        			{	
		        			$speaker_image	= substr($msg , strlen($root));;
		        			/* Remove temporary image */	
		        			unlink($file_name);
	        			}
        			}
        		}
        		
						   		
		   			$result			=  	$sermon_obj->addSpeaker( $speaker_firstname, $speaker_lastname, $speaker_email, $speaker_image);
			   		
			   		/* If valid, attempt to add new article */
			   		if (false !==$result)
			   		{
			   			$new_speaker_url	= $this->_getSpeakerViewUrl((int)$result);
				   		$msg 		= 'New speaker added successfully';
				   		$this->view->speaker_view_url = $new_speaker_url;
				   		$this->view->successmsg = $msg;
				   		$this->view->form 		= null;
				   		
				   		
			   		}else
			   		{
				   		$this->_wordErrors[] = 'Unable to add new speaker.';
			   		}
		   		
	   		}
	   		else{
		   		/* Invalid form */
		   		
		   		
		   		$errors = $speakerForm->getMessages();
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
		   		/* Add the prepared errors */
		   		$this->_wordErrors =  $preparedErrors;
		   		
		   		$this->_wordErrors[] = 'Invalid or Incomplete form received.';
		   		
		   		/* Prepare editing form */
		   		
		   		$speakerForm->populate($request->getPost());
	    	    	
	   		}
	   		
	    	
    	if (!empty($this->_wordErrors))
    		$this->view->errormsgs = $this->_wordErrors;    
    	}
    	$this->view->form 	=	$speakerForm;
	}
	
	public function managespeakersAction()
    {
        $sermon_obj					= Point_Model_Sermons::getInstance();
        
        $all_authors				= $sermon_obj->getSpeakers(0, 200); // Assuming Sermon authors will never be up to 200 ;)
        																//TODO: That technically is a BUG in future
        
        $user_access 	= $this->_user_priviledge;
        $this->view->edit_access	= $user_access != Point_Model_ContentGroups::GROUP_GUEST ? true : false;
        
        $user_access 	= $this->_user_priviledge; // Defined in preDispatch()
        
        if (is_array($all_authors) && !empty($all_authors))
        {
        	foreach ($all_authors as &$author)
        	{
        		/**
        		 * ------------------------------------
        		 * Add the url for the image 
        		 */
        		$author_image	= $this->_getSpeakerImage($author['author_image']);
        		$author			= array_merge($author, $author_image);
	        	/**
	        	 * ------------------------------------
	        	 * Add Admin links
	        	 */
	        	$author['speaker_view_url']		= $this->_getSpeakerViewUrl($author['sermon_author_id']);
	        	$author['speaker_remove_url']	= $this->_getSpeakerRemoveUrl($author['sermon_author_id']);
        	}
        	
        	$this->view->all_authors	= $all_authors;	
        }
        
    }
    
    /**
     * @return array returns speaker's image info
     */
    protected function _getSpeakerImage($image, $width = APP_IMAGES_THUMB_WIDTH, $height = APP_IMAGES_THUMB_HEIGHT)
    {
    	$author						= null;
    	$image_path  =  remove_trailing_slash(APP_PUBLIC_DIRECTORY) . DS. APP_IMAGES_DIRECTORY . $image;
//    	echo $image_path;
    	$w = $h = $t = $at = $new_width = $new_height = null;
    	$image_obj				= Point_Model_Picture::getInstance();
    	list($w, $h, $t, $at) 	= $image_obj->getImageInfo($image_path);
    	$w = $w == null ? $width 	: $w;
    	$h = $h == null ? $height 	: $h;
  
    	list($new_width, $new_height) =  $image_obj->getResizeDimension($w, $h, $width, $height);
//  		echo '<br />',$w, '<br />', $h,'<br />',$new_width,'<br />',$new_height; exit;  	
    	$rel_image_path					= '/'. APP_IMAGES_DIRECTORY . $image;
//    	echo '<br />',$image_path; exit;
    	if (is_file( remove_trailing_slash(APP_PUBLIC_DIRECTORY) . DS. $rel_image_path))
    	{
	    	$author['speaker_image']	= $this->_getBaseUrl(). $rel_image_path;
	    	
	    	$author['speaker_image_w']	= $new_width;
	    	$author['speaker_image_h']	= $new_height;
    	}else
    	{
	    	/* Use a no face image */
	    	$image_path	= '/'. APP_IMAGES_DIRECTORY . APP_NO_PROFILE_IMAGE;
	    	$author['speaker_image']	= $this->_getBaseUrl(). $image_path;
	    	
	    	$author['speaker_image_w']	= $w;
	    	$author['speaker_image_h']	= $h;
    	}
    	
	    return $author;    	
    }
    
	/**
	 * Helper function to create speaker remove url
	 */
	protected function _getSpeakerRemoveUrl($id)
	{
		if (!is_numeric($id))
			throw new Exception('Speaker ID must be numeric');
		
		return $this->_getBaseUrl() . '/word/removespeaker/?sp='. $id;
	}
	
	/**
	 * Helper function to create speaker view url
	 */
	protected function _getSpeakerViewUrl($id)
	{
		if (!is_numeric($id))
			throw new Exception('Speaker ID must be numeric');
		
		return $this->_getBaseUrl() . '/word/speakerprofile/?sp='. $id;
	}
	
	/**
	 * Helper function to edit speaker's profile
	 */
	protected function _getSpeakerEditUrl($id)
	{
		if (!is_numeric($id))
			throw new Exception('Speaker ID must be numeric');
		
		return $this->_getBaseUrl() . '/word/editspeaker/?sp='. $id;
	}
	
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







