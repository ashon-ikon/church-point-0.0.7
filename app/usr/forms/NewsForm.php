<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 3, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Form_NewsForm extends Point_Form_XZendForm
{
	public $elementDecorators = array(
			'ViewHelper',
			'Description'
			);
	
	public function init()
	{
	
		// set initial params
		$this->setAttribs(array('class' => 'form'	,
								'id'	=> 'news-edit-form'	))
			->setMethod('post')->setAction('/news/edit');
		
		
		/*
		 * News Title
		 * News Content
		 * News Description
		 * News Tags 
		 * News Preview
		 * News Submit
		 */
		 
		// Create elements:
		
		/**
		 * ---------------
		 *  News Title
		 * ---------------
		 */
		$title = new Zend_Form_Element_Text('title');
		$title->setLabel(false)
			  ->setRequired(true)
			  ->setOptions(array(
				'size'	=>	'60',
				'class'	=>	'input',
				'title'	=>	'News Title',
				'id'	=>	'news-title'
			))
		->setDecorators($this->elementDecorators)
		->addValidators(array(
			array('NotEmpty', true)
		));
		
		
		/**
		 * News Description
		 * -------------------
		 */
		$content_desc = new Zend_Form_Element_Text('content_desc');
		$content_desc->setLabel(false)
				 ->setOptions(array(
					'size'	=>	'80',
					'class'	=>	'input',
					'title'	=>	'Brief News Description',
					'id'	=>	'newsdesc'
				))
		->setDecorators($this->elementDecorators);
		
		/**
		 * -------------
		 * News Content
		 * -------------
		 */
		$news_content = new Zend_Form_Element_Textarea('tinymcetarea');
		$news_content->setOptions(array(
			'class'	=>	'input tinymce',
			'title' =>	'Enter main news',
			'id'	=>	'tmcenews'
		))
		->setRequired(true)
		->setDecorators($this->elementDecorators)->addValidator('NotEmpty', true);
		
		/**
		 * ---------------
		 *  Tags
		 * ---------------
		 */
		$tags = new Zend_Form_Element_Text('tags');
		$tags->setLabel(false)
				->setRequired(true)
				->addFilter('StringToLower')
				->setOptions(array(
					'size'	=>	'40',
					'class'	=>	'input',
					'title'	=>	'News Tags',
					'id'	=>	'newstags'
				))
				->setDecorators($this->elementDecorators)
				->addValidators(array(
					array('NotEmpty', true)
				));
		
		
		/**
		 * ---------------
		 *  Preview Button
		 * ---------------
		 */
		$preview	  = new Zend_Form_Element_Submit('preview', array(
			'label' => 'Preview News',
			'class'	=> 'button'));
		$preview->setDecorators($this->buttonDecorators);
		
		
		/**
		 * ---------------
		 *  Submit Button
		 * ---------------
		 */
		$submit	  = new Zend_Form_Element_Submit('submit', array(
			'label' => 'Save News',
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
		$this->addElement($title)
			 ->addElement($news_content)
			 ->addElement($content_desc)
			 ->addElement($tags)
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