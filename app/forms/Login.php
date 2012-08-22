<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: Login.php
 * 
 * Created by ashon
 * 
 * Created on Jul 26, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */

	class Application_Form_Login extends Zend_Form
	{
		public function init()
		{
			// set initial params
			$this->setAttribs(array('class' => 'form'	,
						 		    'id'	=> 'login'	))
				 ->setMethod('post');
				 
			// Create elements:
			$email = new Zend_Form_Element_Text('email');
			$email->setLabel('Email :')
					 ->setOptions(array(
					 		'size'	=>	'35',
					 		'class'	=>	'input'
					 ))
					 ->removeDecorator('tag');
			$password = new Zend_Form_Element_Password('password');
			$password->setLabel('Password :')
					 ->setOptions(array(
					 		'size'	=>  '20',
					 		'class' =>	'input'
					 	)
					 )
					 ->removeDecorator('tag');
			$rememberMe = new Zend_Form_Element_Checkbox('remember');
			$rememberMe->setOptions(array(
									'class' 	=> 'remember',
									'name'		=> 'remember',
									'selected'	=> 'none',
									'value'		=> '1'			))
						->setLabel('Remember me')
						->setValue('1');
						
			// create submit button
			$submit	  = new Zend_Form_Element_Submit('submit', array(
										'label' => 'Login'));
			$hidden1  = new Zend_Form_Element_Hidden('submitted');
			$hidden1->setValue('1');
			
			// Attach the elements... 
			$this->addElement($email)
				 ->addElement($password)
				 ->addElement($rememberMe)
				 ->addElement($submit)
				 ->addElement($hidden1);
			
		}
		
		
		
	}