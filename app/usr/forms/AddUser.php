<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 11, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Form_AddUser extends Zend_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);
		
		$this->setup($options);	
	}
	
	public function setup ($options = null)
	{
		/**
		 * Set up the initial form properties
		 * 
		 * Action SHOULD BE SET BY caller!!
		 */
		$this->setMethod('post')
			 ->setAction('#');
		
		$firstname = new Zend_Form_Element_Text('firstname');
		$firstname->setLabel('Firstname : ');
		$firstname->setDecorators(array(
			'ViewHelper',
			'Description',
			array(array('elemDiv' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
			array('label', array('tag' => 'td')),
			array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
		));
		
		
		// Submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setDecorators(array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
			array(array('label'=> 'HtmlTag'), array('tag' => 'td' , 'placement' => 'prepend')),
			array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
			)
		);
		
		
		// Form element
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'table')),
			'Form'
		));
		
		$this->addElement($firstname);
		$this->addElement($submit);
	}
	
	public static function checkbox($content, $element, array $options)
    {
        return '<span class="label">' . $element->getLabel() . "</span>";
    }
}
