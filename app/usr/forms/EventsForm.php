<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * 
 * Depends:		Point_Model_ContentGroups
 * 
 * Created by ashon on March 11, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Form_EventsForm extends Point_Form_XZendForm
{
	public $elementDecorators = array(
			'ViewHelper',
			'Description'
			);
	
	public function init()
	{
		
		// set initial params
		$this->setAttribs(array('class' => 'form'	,
								'id'	=> 'events-edit-form'	))
			->setMethod('post')->setAction('/events/addevent')
			->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		
		
		/*
		 * Event Group List		Textarea
		 * Event Title			Textbox
		 * Event Desc			Textarea
		 * Event Start Date		Textbox
		 * Event Stop Date		Textbox
		 * Event Image			File
		 * Event Submit			Button (Submit)
		 */
		 
		// Create elements:
		
		/**
		 * ---------------
		 *  Events Group List
		 * ---------------
		 */
		$event_groups_list = new Zend_Form_Element_Select('group_id');
		$event_groups_list->setLabel(true)
				  ->addMultiOption('', '-- Select Group --');
			/* Fetch and add groups */
		$contentGroups	= Point_Model_ContentGroups::getInstance();
		$allGroups	 	= $contentGroups->getAllGroups(null, 'group_name ASC');
		
		asort($allGroups);
			
		if (empty($allGroups)) throw new Exception('Error fetching groups'); // Empty content groups list
		
		foreach($allGroups as $cGroup)
		$event_groups_list->addMultiOption($cGroup['group_id'], $cGroup['group_name']);
		/* -----------  */
		$event_groups_list->setOptions(array(
					 		'class'	=>	'input',
							'title' =>	'Choose Content Group of Event',
					 		'id'	=>	'groupslist'
					 ))
					 ->setRequired(true)
					 ->setDecorators($this->elementDecorators)->addValidator('NotEmpty', true);
		
		/**
		 * Event Title
		 * -------------------
		 */
		$event_title = new Zend_Form_Element_Text('event_title');
		$event_title->setLabel(false)
					->setRequired(true)
				 	->setOptions(array(
					'size'	=>	'50',
					'class'	=>	'input',
					'title'	=>	'Event\'s Title',
					'id'	=>	'event-title'
				))
		->setDecorators($this->elementDecorators);
		
		/**
		 * Event Desc
		 * -------------------
		 */
		$event_desc = new Zend_Form_Element_Textarea('event_desc');
		$event_desc->setOptions(array(
			'class'	=>	'input',
			'title' =>	'Description of the event',
			'id'	=>	'event-description',
			'rows'	=>	5,
			'cols'	=>	60
		))
		->setDecorators($this->elementDecorators)->addValidator('NotEmpty', true);
		
		
		/**
		 * Event Start Date
		 * -------------------
		 */
		$event_start_date = new Zend_Form_Element_Text('event_start_date');
		$event_start_date->setLabel(false)
					->setRequired(true)
				 	->setOptions(array(
					'size'	=>	'50',
					'class'	=>	'input',
					'title'	=>	'Date and time event begins.',
					'id'	=>	'event-start-date'
				))
		->setDecorators($this->elementDecorators);
		
		/**
		 * Event Stop Date
		 * -------------------
		 */
		$event_stop_date = new Zend_Form_Element_Text('event_stop_date');
		$event_stop_date->setLabel(false)
					->setRequired(true)
				 	->setOptions(array(
					'size'	=>	'50',
					'class'	=>	'input',
					'title'	=>	'Date and time event will end.',
					'id'	=>	'event-stop-date'
				))
		->setDecorators($this->elementDecorators);
		
		
		/**
		 * -------------
		 * Event Image
		 * -------------
		 */
		$event_image = new Zend_Form_Element_File('event_image');
		$event_image->setLabel(false)
			  ->setRequired(false)
			  ->setOptions(array(
				'size'	=>	'60',
				'class'	=>	'input',
				'id'	=>	'event-image'
			))
		->setDestination(APPLICATION_PATH . DS . 'cache')
		->setValidators(array(
			
			array('Count', 		false, 1),				/* Ensure only 1 file is sent */
			array('Size', 		false, KILO_BYTE * 10 * 1000),			/* Ensure file sent is not more than 10M */
			array('Extension', 	false, 'jpg,png,gif,JPG,PNG,GIF'),	/* Limit files to only JPEG, PNG and GIFs */
		))
		->setDecorators(array('File'));
		
		/**
		 * ---------------
		 *  Submit Button
		 * ---------------
		 */
		$submit	  = new Zend_Form_Element_Submit('submit', array(
			'label' => 'Save Event',
			'class'	=> 'button'));
		$submit->setDecorators($this->buttonDecorators)->addValidator('NotEmpty', true);
		
		$hidden1  = new Zend_Form_Element_Hidden('submitted');
		$hidden1->setValue('1')
		->setDecorators(array('ViewHelper'));
		
		
		// Attach the elements... 
		$this->addElement($event_groups_list)
			 ->addElement($event_title)
			 ->addElement($event_desc)
			 ->addElement($event_start_date)
			 ->addElement($event_stop_date)
			 ->addElement($event_image)
			 ->addElement($submit);
		// Form element
					$this->setDecorators(array(
						'FormElements',
						'Form'
					));
		
		return $this;
	}
}
