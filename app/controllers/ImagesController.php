<?php

class ImagesController extends Point_Controller_Action
{

    protected $_uploadErrors = array();

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->_redirect('error/');
    }

    public function imageslistAction()
    {
        $this->getResponse()
                   ->setHeader('Cache-Control',
                               'no-cache, no-store, max-age=0, must-revalidate, post-check=0, pre-check=0')
                   ->setHeader('Expires', 'Tue, 14 Aug 1997 10:00:35 GMT');

    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('request', 'html')
    				->initContext();
		// Both layout and view renderer should be disabled
		$this->_disableLayout();
//        Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
//        Zend_Layout::getMvcInstance()->disableLayout();
        
        $pixObject = Point_Model_Picture::getInstance();
        if ($images = $pixObject->getAllUserImages())
    	{
    		/*
    		 * [0] => Array
        (
            [image_id] => 29
            [album_id] => 10
            [user_id] => 5
            [image_desc] => Pastor Os Pung
            [image_rel_path] => /albums/temie/c9a8ad/320892_347063_596821.jpg
            [image_rel_thumbnail_path] => /albums/temie/c9a8ad/thumb/thmb_320892_347063_596821_50x39.jpg
            [add_date] => 2012-02-07 14:28:39
            [album_name] => General Images 2
            [album_desc] => General Images Taken in random places
            [album_uri] => albums/temie/c9a8ad
            [modification_date] => 2012-02-07 13:51:14
            [creation_date] => 2012-02-07 13:51:14
        )

    		 */
    		
    		$images_root= '/'. APP_IMAGES_DIRECTORY;
    		$delimiter 	= "\n";
    		$ret 		= 'var tinyMCEImageList = new Array(';
    		foreach($images as $result => $image)
    		{
    			
	    		$ret 		.= $delimiter
				    		. '["'
							. utf8_encode($image['image_desc'])
							. '", "'
							. utf8_encode(APP_DOMAIN . $images_root . $image['image_rel_path'])
							. '"],';
    		}
    		$ret = substr($ret, 0, -1); // remove last comma from array item list (breaks some browsers)
    		$ret .= $delimiter;

    

    		// Finish code: end of array definition. Now we have the JavaScript code ready!
    		$ret .= ');';
    		
    		header('Content-type: text/javascript'); // browser will now recognize the file as a valid JS file
    		
    		// prevent browser from caching
    		header('pragma: no-cache');
    		header('expires: 0'); // i.e. contents have already expired
    		
    		echo $ret;
    	}
    }

    public function uploadAction()
    {
    	$request 	= $this->getRequest();
    	
    	$uploadForm	= new Point_Form_UploadImage();
    	
    	
        /* We can try 2 types: Ajax and non-ajax */
        $pixObject	= Point_Model_Picture::getInstance();
        if ($request->isPost())
        {
        	/* The form has been submitted */
        	$raw_data = $request->getPost();
        	if ($uploadForm->isValid($raw_data))
        	{
        		$clean_data = $uploadForm->getValues();
        		/* Extract each file and description */
        		$files		= array();

				/**
				 * -----------------
				 * Handle th first file
				 */
       		
        		if ($uploadForm->image_upload1->receive())
        		{
        			if ($file_name = $uploadForm->image_upload1->getFileName())
        			{
	        			if (is_array($file_name) && !empty($file_name))
	        			{
	        				/* Take the topmost */
	        				$file_name = $file_name[0]; 
	        			}
	        			$files[] = array('src' => $file_name, 'desc' => $clean_data['image_desc1']);
        			}
        		}
        		
				/**
				 * ----------------
				 * Handle the second file
				 */
        		if ($uploadForm->image_upload2->receive())
        		{
        			if ($file_name = $uploadForm->image_upload2->getFileName())
        			{
	        			if (is_array($file_name) && !empty($file_name))
	        			{
	        				/* Take the topmost */
	        				$file_name = $file_name[0]; 
	        			}
	        			$files[] = array('src' => $file_name, 'desc' => $clean_data['image_desc2']);
        			}
        		}
        		
				/**
				 * ----------------
				 * Handle the third file
				 */
        		if ($uploadForm->image_upload3->receive())
        		{
        			if ($file_name = $uploadForm->image_upload3->getFileName())
        			{
	        			if (is_array($file_name) && !empty($file_name))
	        			{
	        				/* Take the topmost */
	        				$file_name = $file_name[0]; 
	        			}
	        			$files[] = array('src' => $file_name, 'desc' => $clean_data['image_desc3']);
        			}
        		}
        		
        		/* retrieve the album_id */
        		$album_id 	= $request->getParam('image_album', null);
        		
        		/* Now let's save each of the images one after the other */
        		foreach ($files as $file)
        		{
        			$description 		= $file['desc'] == '' ? 'No Description' : $file['desc'];
        			$destination_fname	= makeSeoString(snipByWords($description , 50)); 
        			
        			$newSize 	= array();
        			$info		= array('source' 		=> $file['src'],
        								'destination'	=> $destination_fname,
        								'description'	=> $description,
        								'keep_ext'		=> false,
        								'album_id'		=> $album_id);
        								
        			list($result, $msg) = $pixObject->saveImage($info, $newSize);
        			if (!$result)
        				$this->_uploadErrors[] = $msg;
        			else
        			{	
        				
        				 /* Remove temporary image */	
        				unlink($file['src']);
        			}
        		}
        		if (empty($this->_uploadErrors))
        		{
	        		$uploadForm->reset();
	        		$this->view->successmsg = 'Image uploaded Successfully';
        		}
        	}
        	else
        	{
    			$uploadForm->populate($raw_data);
    			$errors = $uploadForm->getMessages();
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
    			//$preparedErrors [] = $msgs;
    			$this->_uploadErrors =  $preparedErrors;
    			$this->_uploadErrors[] = 'Invalid or incomplete form submitted';
        	}
        }
        else if ($request->isXmlHttpRequest())
        {
        	/* We just received an AJAX quiz :) */
        }
        $this->_setTitle('Upload Image');
        /* Ensure we have album first */
        if($pixObject->getAllUserAlbums())
        {
        	$this->view->form = $uploadForm;	
        }else
        {
        	$this->_uploadErrors[] = '<p>No Album found<br /><br />Please consider adding a new Album first.</p>';
        	$this->view->form = null;
        }
        
        $this->view->errormsgs = $this->_uploadErrors;
    	
    }

    public function removeAction()
    {
        // action body
    }

    public function addalbumAction()
    {
        $this->_setTitle('Add New Album');
        
        $form = new Point_Form_NewAlbum();
        $this->view->form = $form;
        
        $request = $this->getRequest();
    	
        if ($request->isPost())
        {
        	$raw_data = $request->getPost();
        	
        	if($form->isValid($raw_data))
        	{
        		
        		$result = $msgs = null;
        		$cleaned_data 	= $form->getValues();
        		if (null == $cleaned_data['album_desc'])
        			$cleaned_data['album_desc'] = 'No description';
	        	$pixObject 		= Point_Model_Picture::getInstance();
	        	list($result, $msgs) = $pixObject->addAlbum($cleaned_data['album_name'], $cleaned_data['album_desc']);
	        	if($result)
	        		$this->view->successmsg = 'Album added successfully';
	        	else	
	        	{
	        		
		        	$form->populate($raw_data);
		        	$this->view->form = $form;
		        	$errors = $form->getMessages();
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
		        	$preparedErrors [] = $msgs;
		        	$this->view->errormsgs =  $preparedErrors;
	        	}
        	}
        	else
        	{
        		$form->populate($raw_data);
        		$this->view->form = $form;
        		$errors = $form->getMessages();
        		$preparedErrors = array();
        		if (is_array($errors))
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
        			
        		$this->view->errormsgs =  $preparedErrors;
        	}
        }
        else
    		$this->view->form = $form;
    }

    public function removealbumAction()
    {
        // action body
    }

    public function editalbumAction()
    {
        // action body
    }

    public function viewalbumAction()
    {
        // action body
    }

    public function viewalbumsAction()
    {
        // action body
    }


}













