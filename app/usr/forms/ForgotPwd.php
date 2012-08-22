<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Jan 23, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Form_ForgotPwd extends Point_Form_XZendForm
{

	function __construct()
	{
		$this->getForm();
	}
		
	public function init()
	{
		
	}
	
	public function getForm()
	{
		// set initial params
		$this->setAttribs(array('class' => 'form'	,
			'id'	=> 'forgotpwd'	))
			->setMethod('post')->setAction('/account/forgotpwd');
		
				
		/**
		 * ---------------
		 *  Email
		 * ---------------
		 */
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email :')
		->setRequired(true)
		->addFilter('StringToLower')
		->setOptions(array(
			'size'	=>	'40',
			'class'	=>	'input',
			'id'	=>	'remail'
		))
		->setDecorators($this->elementDecorators)
		->addValidators(array(
			array('NotEmpty', true),
			array('EmailAddress')/*,
			array('regex', false, array('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i'))*/
		));
		
		
		/**
		 * ---------------
		 *  Submit Button
		 * ---------------
		 */
		$submit	  = new Zend_Form_Element_Submit('submit', array(
			'label' => 'Request Password Change',
			'class'	=> 'button'));
		$submit->setDecorators($this->buttonDecorators)->addValidator('NotEmpty', true);
		
		$hidden1  = new Zend_Form_Element_Hidden('submitted');
		$hidden1->setValue('1')
		->setDecorators(array('ViewHelper'));
		
		
		// Attach the elements... 
		$this->addElement($email)
			->addElement($submit)
			->addElement($hidden1);
		
		return $this;
	}
	
}