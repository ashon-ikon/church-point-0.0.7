<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 11, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Form_SpeakerForm extends Point_Form_XZendForm
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
			->setMethod('post')->setAction('/word/addspeaker');
		
				
		
		/**
		 * Firstname
		 * Lastname
		 * email
		 * image
		 */
		 
		// Create elements:
		
		/**
		 * ---------------
		 *  Speaker Firstname
		 * ---------------
		 */
		$firstname = new Zend_Form_Element_Text('firstname');
		$firstname->setLabel(false)
			  ->setRequired(true)
			  ->setOptions(array(
				'size'	=>	'50',
				'class'	=>	'input',
				'title'	=>	'Enter Speaker\'s firstname',
				'id'	=>	'speaker-firstname'
			))
		->setDecorators($this->elementDecorators)
		->addValidators(array(
			array('NotEmpty', true)
		));
		
		/**
		 * ---------------
		 *  Speaker lastname
		 * ---------------
		 */
		$lastname = new Zend_Form_Element_Text('lastname');
		$lastname->setLabel(false)
			  ->setRequired(true)
			  ->setOptions(array(
				'size'	=>	'50',
				'class'	=>	'input',
				'title'	=>	'Enter Speaker\'s lastname',
				'id'	=>	'speaker-lastname'
			))
		->setDecorators($this->elementDecorators)
		->addValidators(array(
			array('NotEmpty', true)
		));
		
		/**
		 * ---------------
		 *  Email
		 * ---------------
		 */
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel(false)
				->setRequired(false)
				->addFilter('StringToLower')
				->setOptions(array(
					'size'	=>	'50',
					'class'	=>	'input',
					'title'	=>	'Speaker\'s email address',
					'id'	=>	'speaker-email'
				))
				->setDecorators($this->elementDecorators)
				->addValidators(array(
					array('NotEmpty', true),
					array('EmailAddress')
				));
	
	
		/**
		 * ---------------
		 *  Image File
		 * ---------------
		 */
		 
		$image = new Zend_Form_Element_File('image');
		$image->setLabel(false)
			  ->setRequired(false)
			  ->setOptions(array(
				'size'	=>	'60',
				'class'	=>	'input',
				'title'	=>	'Speaker\'s image',
				'id'	=>	'image-upload'
			))
			->setDestination(APPLICATION_PATH . DS . 'cache')
			->setValidators(array(
				
				array('Count', 		false, 1),				/* Ensure only 1 file is sent */
				array('Size', 		false, KILO_BYTE * 10 * 1000),			/* Ensure file sent is not more than 10M */
				array('Extension', 	false, 'JPG,jpg,png,gif,JPG,PNG,GIF'),	/* Limit files to only JPEG, PNG and GIFs */
			))
			->setDecorators(array('File'));	
	
		/**
		 * ---------------
		 *  Submit Button
		 * ---------------
		 */
		$submit	  = new Zend_Form_Element_Submit('submit', array(
			'label' => 'Add Speaker',
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
	
		$hidden2  = new Zend_Form_Element_Hidden('sp');
		$hidden2->setDecorators(array('ViewHelper'));
	
		
		// Attach the elements... 
		$this->addElement($firstname)
			 ->addElement($lastname)
			 ->addElement($email)
			 ->addElement($hidden1)
			 ->addElement($hidden2)
			 ->addElement($image)
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
