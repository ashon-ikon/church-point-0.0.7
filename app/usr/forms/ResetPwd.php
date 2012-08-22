<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Jan 23, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Form_ResetPwd extends Point_Form_XZendForm
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
			->setMethod('post')->setAction('/account/resetpassword');
		
				
		/**
		 * ---------------
		 */
		$pattern = '/^(?=.*\d)(?=.*[a-z\p{L&}\p{Nd}])(?=.*[A-Z\p{L&}\p{Nd}]).{8,16}$/i';
		$password_validator = new Zend_Validate_Regex($pattern);
		$password_validator->setMessages(array(Zend_Validate_Regex::NOT_MATCH => 'Password must contain at least:<br />1 Lowercase Character [a - z]<br />1 Uppercase Character [A - Z]<br />1 Number'));
		
		$oldpassword = new Zend_Form_Element_Password('oldpassword');
		$oldpassword->setLabel('Old Password :')
		->setOptions(array(
			'size'	=>  '20',
			'class' =>	'input',
			'id' 	=>	'oldpassword'
		)
		)
		->setDecorators($this->elementDecorators);
		//					 ->addValidators(array(
		//					 	array($password_validator)
		//					 	)
		//					 );
		
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('New Password :')
		->setOptions(array(
			'size'	=>  '20',
			'class' =>	'input',
			'id' 	=>	'password1'
		)
		)
		->setDecorators($this->elementDecorators)
		->addValidators(array(
			array($password_validator),
			array('Identical', false, array('password2'))
		)
		);
		
		$password2 = new Zend_Form_Element_Password('password2');
		$password2->setLabel('Confirm New Password :')
		->setOptions(array(
			'size'	=>  '20',
			'class' =>	'input',
			'id' 	=>	'password2'
		)
		)
		->setDecorators($this->elementDecorators)->addValidator('NotEmpty', true);
		
		
		/**
		 * ---------------
		 *  Submit Button
		 * ---------------
		 */
		$submit	  = new Zend_Form_Element_Submit('submit', array(
			'label' => 'Reset Password',
			'class'	=> 'button'));
		$submit->setDecorators($this->buttonDecorators)->addValidator('NotEmpty', true);
		
		$hidden1  = new Zend_Form_Element_Hidden('submitted');
		$hidden1->setValue('1')
		->setDecorators(array('ViewHelper'));
		
		
		// Attach the elements... 
		$this->addElement($oldpassword)
			->addElement($password)
			->addElement($password2)
			->addElement($submit)
			->addElement($hidden1);
		
		return $this;
	}
	
}