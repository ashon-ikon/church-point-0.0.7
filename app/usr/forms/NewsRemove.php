<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 25, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Form_NewsRemove extends Point_Form_XZendForm
{
	public $elementDecorators = array(
			'ViewHelper',
			'Description'
			);
	
	public function init()
	{
		
		// set initial params
		$this->setAttribs(array('class' => 'form'	,
								'id'	=> 'news-remove-form'	))
			->setMethod('post')->setAction('/articles/removenews');
		
		
		/*
		 * News Remove		Button (Submit)
		 * Cancel			Button (Submit)
		 */
		 
		// Create elements:
		
		/**
		 * ---------------
		 *  Cancel Button
		 * ---------------
		 */
		$cancel	  = new Zend_Form_Element_Submit('cancel', array(
			'label' => 'Cancel',
			'class'	=> 'button'));
		$cancel->setDecorators($this->buttonDecorators);
		
		
		/**
		 * ---------------
		 *  Remove Button
		 * ---------------
		 */
		$remove	  = new Zend_Form_Element_Submit('remove', array(
			'label' => 'Remove News',
			'class'	=> 'button'));
		$remove->setDecorators($this->buttonDecorators);
		
		$hidden1  = new Zend_Form_Element_Hidden('submitted');
		$hidden1->setValue('1')
		->setDecorators(array('ViewHelper'));
		
		$hidden2  = new Zend_Form_Element_Hidden('n_id');
		$hidden2->setValue(null)
		->setDecorators(array('ViewHelper'));
		
		
		// Attach the elements... 
		$this->addElement($hidden1)
			 ->addElement($hidden2)
			 ->addElement($cancel)
			 ->addElement($remove);
		// Form element
					$this->setDecorators(array(
						'FormElements',
						'Form'
					));
		
		return $this;
	}
}