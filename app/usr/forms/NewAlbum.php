<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 6, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Form_NewAlbum extends Point_Form_XZendForm
{
	public $elementDecorators = array(
			'ViewHelper',
			'Description'
			);
	
	public function init()
		{
		
		// set initial params
		$this->setAttribs(array('class' => 'form'	,
								'id'	=> 'article-edit-form'	))
			->setMethod('post')->setAction('/images/addalbum');
		
		
		/*
		 * Album Name
		 * Album Desc
		 */
		 
		// Create elements:
		
		/**
		 * ---------------
		 *  Album Name
		 * ---------------
		 */
		$album_name = new Zend_Form_Element_Text('album_name');
		$album_name->setLabel(false)
			  ->setRequired(true)
			  ->setOptions(array(
				'size'	=>	'50',
				'class'	=>	'input',
				'id'	=>	'album-name'
			))
		->setDecorators($this->elementDecorators)
		->addValidators(array(
			array('NotEmpty', true)
		));
		
		/**
		 * Album Desc
		 * -------------------
		 */
		$album_desc = new Zend_Form_Element_Text('album_desc');
		$album_desc->setLabel(false)
				 ->setOptions(array(
					'size'	=>	'50',
					'class'	=>	'input',
					'id'	=>	'albumdesc'
				))
		->setDecorators($this->elementDecorators);
		
		
		/**
		 * ---------------
		 *  Submit Button
		 * ---------------
		 */
		$submit	  = new Zend_Form_Element_Submit('submit', array(
			'label' => 'Create Album',
			'class'	=> 'button'));
		$submit->setDecorators($this->buttonDecorators)->addValidator('NotEmpty', true);
		
		$hidden1  = new Zend_Form_Element_Hidden('submitted');
		$hidden1->setValue('1')
		->setDecorators(array('ViewHelper'));
		
		
		// Attach the elements... 
		$this->addElement($album_name)
			 ->addElement($album_desc)
			 ->addElement($submit);
		// Form element
					$this->setDecorators(array(
						'FormElements',
						'Form'
					));
		
		return $this;
		}
	
	
	public function validate($params)
		{
		/**
		 *
		 [controller] => account
		 [action] => register
		 [module] => default
		 [firstname] => Yinka
		 [lastname] => Ashon
		 [email] => ashon@pcmc.org.my
		 [day] => 0
		 [month] => 9
		 [year] => 1982
		 [password] => 1234567889
		 [password2] => aijooaisdf0
		 [submit] => Join Now
		 */
		}
}