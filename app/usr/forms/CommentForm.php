<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 3, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Form_CommentForm extends Point_Form_XZendForm
{
	public $elementDecorators = array(
			'ViewHelper',
			'Description'
			);
	
	public function init()
	{
	
		// set initial params
		$this->setAttribs(array('class' => 'form comment-form'	,
								'id'	=> 'comment-form-' . genRandomNumString(3)	))
			->setMethod('post')->setAction('/');
		
		
		/*
		 * Comment Area
		 * Comment Anonymous
		 * Comment Submit
		 */
		 
		// Create elements:
		
		/**
		 * ---------------
		 *  Comment Input Area
		 * ---------------
		 */
		$comment = new Zend_Form_Element_Textarea('comment');
		$comment->setLabel(false)
			  	->setRequired(true)
			  	->setOptions(array(
				'class'	=>	'input',
				'title'	=>	'Say something...',
				'id'	=>	'comment-field-' . genRandomNumString(3)
			))
		->setDecorators($this->elementDecorators)
		->addValidators(array(
			array('NotEmpty', true)
		));
		
		
		/**
		 * Anonymous
		 * -------------------
		 */
		$anonymous = new Zend_Form_Element_Checkbox('anonymous');
		$anonymous->setLabel(false)
				 ->setOptions(array(
					'class'	=>	'input',
					'title'	=>	'Post comment anonymously',
					'id'	=>	'anonymous-' . genRandomNumString(3)
				))
		->setDecorators($this->elementDecorators);
		
		/**
		 * ---------------
		 *  Submit Button
		 * ---------------
		 */
		$submit	  = new Zend_Form_Element_Submit('submit', array(
			'label' => 'Post',
			'class'	=> 'button comment-button'));
		$submit->setDecorators($this->buttonDecorators)->addValidator('NotEmpty', true);
		
		$hidden1  = new Zend_Form_Element_Hidden('submitted');
		$hidden1->setValue('1')
		->setDecorators(array('ViewHelper'));
		
		
		// Attach the elements... 
		$this->addElement($comment)
			 ->addElement($anonymous)
			 ->addElement($submit);
		// Form element
					$this->setDecorators(array(
						'FormElements',
						'Form'
					));
		
		return $this;
	}
}