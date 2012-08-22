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

class Point_Form_Registration extends Point_Form_XZendForm
{
	
	public function init()
		{
		
		}
	
	public function getForm($page = null)
		{
		$months = array(1=>'January', 'February','March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');;
		// set initial params
		$this->setAttribs(array('class' => 'form'	,
			'id'	=> 'userregistration'	))
			->setMethod('post')->setAction('/account/register');
		
		
		/*
		 * First Name
		 * Last Name
		 * DOB
		 * Email
		 * Phone No
		 * Address
		 * 
		 */
		// Create elements:
		
		/**
		 * ---------------
		 *  Name
		 * ---------------
		 */
		$firstname = new Zend_Form_Element_Text('firstname');
		$firstname->setLabel('* First name :')
		->setRequired(true)
		->setOptions(array(
			'size'	=>	'40',
			'class'	=>	'input',
			'id'	=>	'firstname'
		))
		->setDecorators($this->elementDecorators)
		->addValidators(array(
			array('NotEmpty', true),
			/*array('alnum', true),*/
			array('StringLength', true, array(3, 50))
		));
		
		$lastname = new Zend_Form_Element_Text('lastname');
		$lastname->setLabel('* Last name :')
		->setRequired(true)
		->setOptions(array(
			'size'	=>	'40',
			'class'	=>	'input',
			'id'	=>	'lastname'
		))
		->setDecorators($this->elementDecorators)
		->addValidators(array(
			array('NotEmpty', true),
			/*array('Alpha', true),*/
			array('StringLength', true, array(3, 50))
		));
		
		
		/**
		 * -------------
		 * Gender
		 * -------------
		 */
		$gender = new Zend_Form_Element_Select('gender');
		$gender->addMultiOption('', '-- Select Gender --')
		->addMultiOptions(array(1=>'Male', 'Female'))
		->setOptions(array(
			'class'	=>	'input',
			'title' =>	'Select your gender',
			'id'	=>	'gender'
		))
		->setRequired(true)
		->setDecorators($this->selectDecorators)->addValidator('NotEmpty', true);
		
		/**
		 * ---------------
		 *  Email
		 * ---------------
		 */
		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('* Email :')
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
		 *  Mobile no
		 * ---------------
		 */
		$mobile = new Zend_Form_Element_Text('mobile');
		$pattern = '/^\(?([0-9+]{2,3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4,})$/i'; 
		
		$mobile_len_validator = new Zend_Validate_StringLength(array('min'=>8, 'max'=>16));
		$mobile_len_validator->setMessages(array(
			Zend_Validate_StringLength::TOO_SHORT => "'%value%' is too short",
			Zend_Validate_StringLength::TOO_LONG  => "'%value%' is too long",));
		
		$mobile_validator = new Zend_Validate_Regex($pattern);
		$mobile_validator->setMessages(array(Zend_Validate_Regex::NOT_MATCH => '\'%value%\' does not appear to be a valid phone number '));
		
		$mobile->setLabel('Mobile No. :')
		->setOptions(array(
			'size'	=>	'40',
			'class'	=>	'input',
			'id'	=>	'mobile'
		))
		->setDecorators($this->elementDecorators)
		->addValidators(array(
			array('NotEmpty', true),
			array($mobile_len_validator)
			//						array('regex', false, 
			//							array(
			//										/* +234-801-345-2345
			//										 * 016-661-1157
			//										 * +6011-644-1234 
			//										 */
			//								'pattern' => '/^\(?([0-9+]{2,3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4,})$/i'),
			//								'messages' => array(
			//											'regexInvalid'   => "Invalid type given, value should be string, integer or float",
			//											Zend_Validate_Regex::NOT_MATCH => '\'%value%\' does not appear to be a valid phone number ',//"'%value%' does not match against pattern '%pattern%'",
			//											'regexErrorous'  => "There was an internal error "//while using the pattern '%pattern%'"
			//												)
			//									)
		)
		)
		->addValidator($mobile_validator);
		
		/**
		 * ---------------
		 *  Date of Birth
		 * ---------------
		 */
		$day = new Zend_Form_Element_Select('day');
		$day->addMultiOption('', '-- Day --');
		for($i = 1; $i <= 31 ; $i++)
			$day->addMultiOption($i, $i);
		$day->setOptions(array(
			'class'	=>	'input',
			'title' =>	'Day',
			'id'	=>	'day'
		))
		->setRequired(true)
		->setDecorators($this->selectDecorators)->addValidator('NotEmpty', true);
		
		$month = new Zend_Form_Element_Select('month');
		$month->addMultiOption('', '-- Month --');
		$month->addMultiOptions($months)
		->setOptions(array(
			'class'	=>	'input',
			'title' =>	'Month',
			'id'	=>	'month'
		))
		->setRequired(true)
		->setDecorators($this->selectDecorators)->addValidator('NotEmpty', true);
		
		$year = new Zend_Form_Element_Select('year');
		$year->addMultiOption('', '-- Year --');
		for($i = intval(date('Y', time())); $i >= 1910 ; $i--)
			$year->addMultiOption($i, $i);
		$year->setOptions(array(
			'class'	=>	'input',
			'title' =>	'Year',
			'id'	=>	'year'
		))
		->setRequired(true)
		->setDecorators($this->selectDecorators)->addValidator('NotEmpty', true);
		
		/**
		 * ---------------
		 *  Address
		 * ---------------
		 */
		$address1 = new Zend_Form_Element_Text('address1');
		$address1->setLabel('* Address :')
		->setOptions(array(
			'size'	=>	'40',
			'class'	=>	'input required',
			'title' =>	'Your house address',
			'id'	=>	'address1'
		))
		->setRequired(true)
		->setDecorators($this->elementDecorators)->addValidator('NotEmpty', true);
		
		$address2 = new Zend_Form_Element_Text('address2');
		$address2->setLabel('Address :')
		->setOptions(array(
			'size'	=>	'40',
			'class'	=>	'input',
			'title' =>	'Your house address',
			'id'	=>	'address2'
		))
		->setDecorators($this->elementDecorators);
		
		
		$town = new Zend_Form_Element_Text('town');
		$town->setLabel('* Town :')
		->setOptions(array(
			'size'	=>	'40',
			'class'	=>	'input',
			'title' =>	'Your house address',
			'id'	=>	'town'
		))
		->addFilters(array(
			array('Alnum')
		))
		->setRequired(true)
		->setDecorators($this->elementDecorators)->addValidator('NotEmpty', true);
		
		
		$zip = new Zend_Form_Element_Text('zip');
		$zip->setLabel('Zip/Post Code :')
		->setOptions(array(
			'size'	=>	'40',
			'class'	=>	'input',
			'title' =>	'Your house zip/postal code',
			'id'	=>	'zip'
		))
		->setDecorators($this->elementDecorators)->addValidator('StringLength', false, array(3, 7));
		
		/**
		 * ------------
		 * State
		 * -----------
		 */
		$state = new Zend_Form_Element_Select('state');
		$state->setLabel('* State :')
		->addMultiOption('', '-- Select State --');
		/* Fetch and add states */
		$db = new Zend_Db_Table('states_table'); 
		$dbstates  = $db->select()->query()->fetchAll();
		asort($dbstates);
		if (empty($dbstates)) throw new Exception('Error fetching states'); // No matching editor
		foreach($dbstates as $dbstate)
		$state->addMultiOption($dbstate['state_id'], $dbstate['state_name']);
		/* -----------  */
		$state->addMultiOption('*', '');
		//$state->addMultiOption(17, '-- Other --');
		$state->setOptions(array(
			'class'	=>	'input',
			'title' =>	'Choose State',
			'id'	=>	'state'
		))
		->setRequired(true)
		->setDecorators($this->elementDecorators)->addValidator('NotEmpty', true);
		
		/**
		 * -------------
		 * Country
		 * -------------
		 */
		$country = new Zend_Form_Element_Select('country');
		$country->setLabel('* Country :')
		->addMultiOption('', '-- Select Country --');
		/* Fetch and add states */
		$db = new Zend_Db_Table('countries_table'); 
		$dbcountries  = $db->select()->order('country_name')->query()->fetchAll();
		asort($dbcountries);
		if (empty($dbcountries)) throw new Exception('Error fetching Countries'); // No matching editor
		foreach($dbcountries as $dbcountry)
		$country->addMultiOption($dbcountry['country_id'], $dbcountry['country_name']);
		/* -----------  */
		$country->setOptions(array(
			'class'	=>	'input',
			'title' =>	'Choose Country',
			'id'	=>	'country'
		))
		->setRequired(true)
		->setDecorators($this->elementDecorators)->addValidator('NotEmpty', true);
		/**
		 * ---------------
		 *  Password
		 * ---------------
		 */
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel('* Password :')
		->setOptions(array(
			'size'	=>  '20',
			'class' =>	'input',
			'id' =>	'password1'
		)
		)
		->setRequired(true)
		->setDecorators($this->elementDecorators)
		->addValidators(array(
			array('Identical', false, array('password2'))
		)
		);
		
		$password2 = new Zend_Form_Element_Password('password2');
		$password2->setLabel('* Confirm Password :')
		->setOptions(array(
			'size'	=>  '20',
			'class' =>	'input',
			'id' 	=>	'password2'
		)
		)
		->setRequired(true)
		->setDecorators($this->elementDecorators)->addValidator('NotEmpty', true);
		
		
		/**
		 * ---------------
		 *  Submit Button
		 * ---------------
		 */
		$submit	  = new Zend_Form_Element_Submit('submit', array(
			'label' => 'Join Now',
			'class'	=> 'button'));
		$submit->setDecorators($this->buttonDecorators)->addValidator('NotEmpty', true);
		
		$hidden1  = new Zend_Form_Element_Hidden('submitted');
		$hidden1->setValue('1')
		->setDecorators(array('ViewHelper'));
		
		
		// Attach the elements... 
		$this->addElement($firstname)
		->addElement($lastname)
		->addElement($email)
		->addElement($mobile)
		->addElement($gender)
		->addElement($day)
		->addElement($month)
		->addElement($year)
		->addElement($address1)
		->addElement($address2)
		->addElement($town)
		->addElement($zip)
		->addElement($state)
		->addElement($country)
		->addElement($password)
		->addElement($password2)
		->addElement($submit)
		->addElement($hidden1);
		
		
		/*			$this->setElementDecorators(array(
		 'ViewHelper',
		 'Errors',
		 array(array('data'=>'HtmlTag') , array('tag' => 'td', 'class' => 'element')),
		 array('label' , array('tag' => 'td', 'class' => 'label')),
		 array(array('row' => 'HtmlTag' , array('tag' => 'tr')))
		 ));
		 */			
		// Form element
		//			$this->setDecorators(array(
		//				'FormElements',
		//				array('HtmlTag', array('tag' => 'table', array('border'=>'2'))),
		//				'Form'
		//			));
		//			
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
		echo '<pre>', print_r($params,true), '</pre>';
		}
	
}