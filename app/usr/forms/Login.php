<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: Login.php
 * 
 * Created by ashon on Sep 03, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */

class Point_Form_Login extends Point_Form_XZendForm
{
	public $elementDecorators = array(
		'ViewHelper',
		'Description',
		array(array('elemDiv' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
		array('label', array('tag' => 'td')),
		array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
	);
	
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
			'size'	=>	'30',
			'class'	=>	'input'
		))
		->setDecorators($this->elementDecorators)
		->setRequired(true)
		->addValidator('NotEmpty', true)
		->addValidator('EmailAddress');
		
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('Password :')
		->setOptions(array(
			'size'	=>  '20',
			'class' =>	'input'
			)
		)
		->setDecorators($this->elementDecorators)
		->setRequired(true)
		->addValidator('NotEmpty', true);
		
		// Remember me
		$rememberme = new Zend_Form_Element_Checkbox('remember');
		$rememberme->setLabel('Remember Me' )
		->setDecorators($this->selectDecorators);
		
		
		// create submit button
		$submit	  = new Zend_Form_Element_Submit('submit', array(
			'label' => 'Login',
			'class' => 'submit'));
		$submit->setDecorators($this->buttonDecorators);
		
		$hidden1  = new Zend_Form_Element_Hidden('submitted');
		$hidden1->setValue('1')
		->setDecorators(array('ViewHelper'));
		
		$front		= Zend_Controller_Front::getInstance();
		$request	= $front->getRequest();
//		$view		= Zend_Layout::getMvcInstance()->getView();
		$url		= /*$request->getScheme() . '://' . $request->getHttpHost(). */$request->getRequestUri();
//		$uri 		= Point_Object_Session::getInstance();
//	 	$uri->incoming_uri = $url;

		$this_url  	= new Zend_Form_Element_Hidden('url');
		$this_url->setValue($url)
				 ->setDecorators(array('ViewHelper'));
		
		// Attach the elements... 
		$this->addElement($hidden1)
		->addElement($this_url)
		->addElement($email)
		->addElement($password)
		->addElement($rememberme)
		->addElement($submit);
		
		
		/*			$this->setElementDecorators(array(
		 'ViewHelper',
		 'Errors',
		 array(array('data'=>'HtmlTag') , array('tag' => 'td', 'class' => 'element')),
		 array('label' , array('tag' => 'td', 'class' => 'label')),
		 array(array('row' => 'HtmlTag' , array('tag' => 'tr')))
		 ));
		 */			
		// Form element
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'table', array('border'=>'2'))),
			'Form'
		));
		
	}
	
	public static function checkbox($content, $element, array $options)
	{
		return '<span class="label"><pre>' . $element->getLabel() . '</pre></span>';
	}
	
}