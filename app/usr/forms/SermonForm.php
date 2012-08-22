<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 15, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Form_SermonForm extends Point_Form_XZendForm
{
	public $elementDecorators = array(
			'ViewHelper',
			'Description'
			);
	
	public function init()
	{
		
		// set initial params
		$this->setAttribs(array('class' => 'form'	,
								'id'	=> 'sermon-edit-form'	))
			->setMethod('post')->setAction('/word/addsermon');
		
		
		/*
		 * Sermon Speaker		Textbox
		 * Sermon Title			Textbox
		 * Sermon Date			Textbox
		 * Sermon Content		Textarea
		 * Sermon highlight		Textarea
		 * Sermon Preview		Button (Submit)
		 * Sermon Submit		Button (Submit)
		 */
		 
		// Create elements:
		
		/**
		 * ---------------
		 *  Sermon Speaker
		 * ---------------
		 */
		$sermon_speaker = new Zend_Form_Element_Select('speaker');
		$sermon_speaker->setLabel(false)
				  ->addMultiOption('', '-- Select Sermon Speaker --');
			/* Fetch and add sermon authors */
			$db = new Zend_Db_Table('sermons_authors_table'); 
			$dbauthors  = $db->select()->query()->fetchAll();
			asort($dbauthors);
//			if (empty($dbsermons)) throw new Exception('Error fetching sermons'); // No matching sermon authors
			foreach($dbauthors as $dbauthor)
				$sermon_speaker->addMultiOption($dbauthor['sermon_author_id'], $dbauthor['author_firstname'] . ' ' . $dbauthor['author_lastname']);
			/* -----------  */
			if (empty($dbauthors))
			{
//				$sermon_speaker->addMultiOption('*', '-----------------------');
				$sermon_speaker->addMultiOption('none', '-------- Empty -------');	
			}
			$sermon_speaker->setOptions(array(
					 		'class'	=>	'input',
							'title' =>	'Choose Sermon Speaker',
					 		'id'	=>	'sermon-author'
					 ))
					 ->setRequired(true)
					 ->setDecorators($this->elementDecorators)->addValidator('NotEmpty', true);
		
		/**
		 * Sermon Title
		 * -------------------
		 */
		$sermon_title = new Zend_Form_Element_Text('sermon_title');
		$sermon_title->setLabel(false)
					->setRequired(true)
				 	->setOptions(array(
					'size'	=>	'50',
					'class'	=>	'input',
					'title'	=>	'Sermon\'s Title',
					'id'	=>	'sermon-title'
				))
		->setDecorators($this->elementDecorators);
		
		
		/**
		 * Sermon date
		 * -------------------
		 */
		$sermon_date = new Zend_Form_Element_Text('sermon_date');
		$sermon_date->setLabel(false)
					->setRequired(true)
				 	->setOptions(array(
					'size'	=>	'50',
					'class'	=>	'input',
					'title'	=>	'Date and time sermon was recorded.',
					'id'	=>	'sermon-date'
				))
		->setDecorators($this->elementDecorators);
		
		
		/**
		 * -------------
		 * Sermon Content
		 * -------------
		 */
		$sermon_content = new Zend_Form_Element_Textarea('tinymcetarea');
		$sermon_content->setOptions(array(
			'class'	=>	'input tinymce',
			'title' =>	'Enter the entire sermon content',
			'id'	=>	'tmcesermon',
			'cols'	=>	90
		))
		->setRequired(true)
		->setDecorators($this->elementDecorators)->addValidator('NotEmpty', true);

		
		/**
		 * -------------
		 * Sermon Highlight
		 * -------------
		 */
		$sermon_highlights = new Zend_Form_Element_Textarea('sermon_highlights');
		$sermon_highlights->setOptions(array(
			'class'	=>	'input',
			'title' =>	'Give a simple sermon quote',
			'id'	=>	'sermon-highlights',
			'rows'	=>	3,
			'cols'	=>	50
		))
		->setDecorators($this->elementDecorators)->addValidator('NotEmpty', true);
		
		/**
		 * ---------------
		 *  Preview Button
		 * ---------------
		 */
		$preview	  = new Zend_Form_Element_Submit('preview', array(
			'label' => 'Preview Sermon',
			'class'	=> 'button'));
		$preview->setDecorators($this->buttonDecorators);
		
		
		/**
		 * ---------------
		 *  Submit Button
		 * ---------------
		 */
		$submit	  = new Zend_Form_Element_Submit('submit', array(
			'label' => 'Save Sermon',
			'class'	=> 'button'));
		$submit->setDecorators($this->buttonDecorators)->addValidator('NotEmpty', true);
				
		/**
		 * ---------------
		 *  Cancel Button
		 * ---------------
		 */
		$cancel	  = new Zend_Form_Element_Submit('cancel', array(
			'label' => 'Cancel',
			'class'	=> 'button'));
		$cancel->setDecorators($this->buttonDecorators)->addValidator('NotEmpty', true);
		
		$hidden1  = new Zend_Form_Element_Hidden('submitted');
		$hidden1->setValue('1')
		->setDecorators(array('ViewHelper'));
		
		
		// Attach the elements... 
		$this->addElement($sermon_speaker)
			 ->addElement($sermon_title)
			 ->addElement($sermon_content)
			 ->addElement($sermon_date)
			 ->addElement($sermon_highlights)
			 /*->addElement($ser)*/
			 ->addElement($preview)
			 ->addElement($cancel)
			 ->addElement($submit);
		// Form element
					$this->setDecorators(array(
						'FormElements',
						'Form'
					));
		
		return $this;
	}
}
