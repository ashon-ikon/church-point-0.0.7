<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 6, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Form_UploadImage extends Point_Form_XZendForm
{
	/**
	 * Decorators used
	 */
	public $elementDecorators = array(
			'ViewHelper',
			'Description'
	);
	
	public function init()
	{
		
		// set initial params
		$this->setAttribs(array('class' => 'form'	,
								'id'	=> 'image-upload-form',	
								'enctype', 'multipart/form-data'))
			->setMethod('post')->setAction('/images/upload');
		
		
		/*
		 * Image File
		 * Image Desciption
		 * Image Album
		 */
		 
		// Create elements:
		
		/**
		 * ---------------
		 *  Image File
		 * ---------------
		 */
		 
		$image1 = new Zend_Form_Element_File('image_upload1');
		$image1->setLabel(false)
			  ->setRequired(true)
			  ->setOptions(array(
				'size'	=>	'60',
				'class'	=>	'input',
				'id'	=>	'image-upload-element1'
			))
		->setDestination(APPLICATION_PATH . DS . 'cache')
		->setValidators(array(
			
			array('Count', 		false, 1),				/* Ensure only 1 file is sent */
			array('Size', 		false, KILO_BYTE * 10 * 1000),			/* Ensure file sent is not more than 10M */
			array('Extension', 	false, 'JPG,jpg,png,gif,JPG,PNG,GIF'),	/* Limit files to only JPEG, PNG and GIFs */
		))
		->setDecorators(array('File'));
		 
		$image2 = new Zend_Form_Element_File('image_upload2');
		$image2->setLabel(false)
			  ->setRequired(false)
			  ->setOptions(array(
				'size'	=>	'60',
				'class'	=>	'input',
				'id'	=>	'image-upload-element2'
			))
		->setDestination(APPLICATION_PATH . DS . 'cache')
		->setValidators(array(
			
			array('Count', 		false, 1),				/* Ensure only 1 file is sent */
			array('Size', 		false, KILO_BYTE * 10 * 1000),			/* Ensure file sent is not more than 10M */
			array('Extension', 	false, 'jpg,png,gif,JPG,PNG,GIF'),	/* Limit files to only JPEG, PNG and GIFs */
		))
		->setDecorators(array('File'));
		 
		$image3 = new Zend_Form_Element_File('image_upload3');
		$image3->setLabel(false)
			  ->setRequired(false)
			  ->setOptions(array(
				'size'	=>	'60',
				'class'	=>	'input',
				'id'	=>	'image-upload-element3'
			))
		->setDestination(APPLICATION_PATH . DS . 'cache')
		->setValidators(array(
			
			array('Count', 		false, 1),				/* Ensure only 1 file is sent */
			array('Size', 		false, KILO_BYTE * 10 * 1000),			/* Ensure file sent is not more than 10M */
			array('Extension', 	false, 'jpg,png,gif,JPG,PNG,GIF'),	/* Limit files to only JPEG, PNG and GIFs */
		))
		->setDecorators(array('File'));
		
		/**
		 * Image Description
		 * -------------------
		 */
		$image_desc1 = new Zend_Form_Element_Text('image_desc1');
		$image_desc1->setLabel(false)
				 ->setOptions(array(
					'size'	=>	'34',
					'class'	=>	'input',
					'id'	=>	'imagedesc'
				))
		->setDecorators($this->elementDecorators);
		
		/**
		 * Image Description
		 * -------------------
		 */
		$image_desc2 = new Zend_Form_Element_Text('image_desc2');
		$image_desc2->setLabel(false)
				 ->setOptions(array(
					'size'	=>	'34',
					'class'	=>	'input',
					'id'	=>	'imagedesc'
				))
		->setDecorators($this->elementDecorators);
		
		/**
		 * Image Description
		 * -------------------
		 */
		$image_desc3 = new Zend_Form_Element_Text('image_desc3');
		$image_desc3->setLabel(false)
				 ->setOptions(array(
					'size'	=>	'34',
					'class'	=>	'input',
					'id'	=>	'imagedesc'
				))
		->setDecorators($this->elementDecorators);
		
		/**
		 * -------------
		 * Country
		 * -------------
		 */
		$image_album = new Zend_Form_Element_Select('image_album');
		$image_album->setLabel(false)
		->addMultiOption('', '-- Select Album --');
		/* Fetch and add states */
		$db 			= new Zend_Db_Table('albums_table'); 
		$all_albums  	= $db->select()->order('album_name DESC')->query()->fetchAll();
		asort($all_albums);
		
		if (empty($all_albums)) throw new Exception('Error fetching Albums'); // No matching album
		
		foreach($all_albums as $album)
			$image_album->addMultiOption($album['album_id'], $album['album_name']);
			
		/* -----------  */
		$image_album->setOptions(array(
			'class'	=>	'input',
			'title' =>	'Choose Album',
			'id'	=>	'album-name'
		))
		->setRequired(true)
		->setDecorators($this->elementDecorators)->addValidator('NotEmpty', true);
		
		/**
		 * ---------------
		 *  Submit Button
		 * ---------------
		 */
		$submit	  = new Zend_Form_Element_Submit('submit', array(
			'label' => 'Upload Image',
			'class'	=> 'button'));
		$submit->setDecorators($this->buttonDecorators)->addValidator('NotEmpty', true);
		
		$hidden  = new Zend_Form_Element_Hidden('submitted');
		$hidden->setValue('1')
		->setDecorators(array('ViewHelper'));
		
		
		// Attach the elements... 
		$this->addElement($image1)
			 ->addElement($image_desc1)
			 ->addElement($image2)
			 ->addElement($image_desc2)
			 ->addElement($image3)
			 ->addElement($image_desc3)
			 ->addElement($image_album)
			 ->addElement($hidden)
			 ->addElement($submit);
		// Form element
					$this->setDecorators(array(
						'FormElements',
						'Form'
					));
		
		return $this;
		}
}