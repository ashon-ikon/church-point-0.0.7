<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 3, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Form_ArticleForm extends Point_Form_XZendForm
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
			->setMethod('post')->setAction('/article/edit');
		
		
		/*
		 * Article Title
		 * Article Desciption
		 * Article Content
		 * Article Tags 
		 * Article Preview
		 */
		 
		// Create elements:
		
		/**
		 * ---------------
		 *  Article Title
		 * ---------------
		 */
		$title = new Zend_Form_Element_Text('title');
		$title->setLabel(false)
			  ->setRequired(true)
			  ->setOptions(array(
				'size'	=>	'50',
				'class'	=>	'input',
				'title'	=>	'Article Title',
				'id'	=>	'article-title'
			))
		->setDecorators($this->elementDecorators)
		->addValidators(array(
			array('NotEmpty', true)
		));
		
		/**
		 * Article Description
		 * -------------------
		 */
		$title_desc = new Zend_Form_Element_Text('title_desc');
		$title_desc->setLabel(false)
				 ->setOptions(array(
					'size'	=>	'50',
					'class'	=>	'input',
					'title'	=>	'Brief Article Description',
					'id'	=>	'titledesc'
				))
		->setDecorators($this->elementDecorators);
		
		
		/**
		 * -------------
		 * Article Content
		 * -------------
		 */
		$article_content = new Zend_Form_Element_Textarea('tinymcetarea');
		$article_content->setOptions(array(
			'class'	=>	'input tinymce',
			'title' =>	'Enter main article',
			'id'	=>	'tmcearticle'
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
					'title'	=>	'Article Tags',
					'id'	=>	'articletags'
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
			'label' => 'Preview Article',
			'class'	=> 'button'));
		$preview->setDecorators($this->buttonDecorators);
		
		
		/**
		 * ---------------
		 *  Submit Button
		 * ---------------
		 */
		$submit	  = new Zend_Form_Element_Submit('submit', array(
			'label' => 'Save Article',
			'class'	=> 'button'));
		$submit->setDecorators($this->buttonDecorators)->addValidator('NotEmpty', true);
		
		$hidden1  = new Zend_Form_Element_Hidden('submitted');
		$hidden1->setValue('1')
		->setDecorators(array('ViewHelper'));
		
		
		// Attach the elements... 
		$this->addElement($title)
			 ->addElement($title_desc)
			 ->addElement($article_content)
			 ->addElement($tags)
			 ->addElement($preview)
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