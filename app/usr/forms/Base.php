<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 24, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
 
/**
 * 
 * Helper class to create forms
 * 
 */
class Point_Form_Base 
{
	protected	$_elements = array();
	protected	$_formElement = null;
	
	protected	$_formWrapperHtml 	= 'div';
	protected	$_formElementWrapper= 'div';
	protected	$_formMethod		= 'post';
	protected	$_formAction		= '';
	protected	$_formEncryptionType= 'application/x-www-form-urlencoded';
	protected	$_formClass			= null;
	protected	$_formId			= null;
	
	protected	$_formOptions		= array(
											'class' => 'PointForm'
										);
	
	/**
	 * Called every instance once
	 */
	public function __construct($options = null)
	{
		//$this->makeFormHeader();
	}
	
	public function makeText($options)
	{
		/**
		 * Extract all options
		 */
		$textName = null;
		if (is_array($options))
		{
			foreach($options as $option => $value)
				$$option = $value;
			if ( isset($name) ) $textName = $name;
		} else if (is_string($options)){
			$textName = $options;
		}
		// Ensure we always have a valid name
		if (null === $textName)
			$textName = 'Point_Text';
	}
	
	
	/**
	 * This sets the form method
	 * @param $id 
	 */
	public function setFormId( $id )
	{
		$this->_formId = $id;
	}
	
	/**
	 * This gets the form id
	 */
	public function getFormId()
	{
		return $this->_formId;
	}
	
	/**
	 * This sets the form method
	 * @param $method default:= 'post'
	 */
	public function setMethod($method = 'post')
	{
		$this->_formMethod = $method;
	}
	
	/**
	 * This gets the form method
	 */
	public function getMethod()
	{
		return $this->_formMethod;
	}
	
	
	
	/**
	 * This sets the form action
	 * @param $action default:= ''
	 */
	public function setAction($action= '')
	{
		$this->_formAction = $action;
	}
	
	/**
	 * This gets the form action
	 */
	public function getAction()
	{
		return $this->_formAction;
	}
	
	protected function mergeOptions(array $options)
	{
		if( !empty($options) )
			foreach ($options as $option => $value)
				$this->_formOptions[strtolower($option)] = $value; // This ensure rewrite of old values
				
				
		return $this;
	}
	
	/**
	 * This sets the form Encryption Type
	 * @param $encryption default:= 'application/x-www-form-urlencoded'
	 */
	public function setEncryptionType($encryption = 'application/x-www-form-urlencoded')
	{
		$this->_formEncryptionType = $encryption;
	}
	
	/**
	 * This gets the form Encryption Type
	 */
	public function getEncryptionType()
	{
		return $this->_formEncryptionType;
	}
	
	
	protected function makeFormHeader(array $options = null)
	{
		$header	= '<'.$this->getFormWrapper() . '>';
		$header .= '<form ';
		$header .= 'method="' . $this->getMethod() . '" ';
		$header .= 'action="' . $this->getAction() . '" ';
		$header .= 'enctype="' . $this->getEncryptionType() . '" ';
		// Append Id if any
		if (null !== $formId = $this->getFormId() )
			 $header .= $formId . ' ';
		
		// Append options
		if(! empty($options) )
			$this->mergeOptions( $options );
			
		if (!empty($this->_formOptions)){
			foreach ($this->_formOptions as $attr=>$val) $header .= $attr. '="' . $val . '" ';
		}
		
		$header .= '>';
		return $header;
	}
	
	protected function getAllElements()
	{
		$elements = null;
		if(!empty($this->_elements))
		{
			foreach($this->_elements as $element)
			{
				$elements .= $element;//$this->getElementHtml($element);	
			}
		}
		
		return $elements;
	}
	
	protected function getElementHtml($element)
	{
		if (array_key_exists($element, $this->_elements))
			return $this->_elements[$element];
	}
	
	public function setFormWrapper( $wrapper = 'div')
	{
		if ($wrapper)
		{
			$this->_formWrapperHtml = $wrapper;
		}
		return $this;
	}
	
	public function getFormWrapper()
	{
		if (null === $this->_formWrapperHtml)
		{
			$this->setFormWrapper();
		}
		return $this->_formWrapperHtml;
	}
	
	protected function formClose()
	{
		return '</form>'. '</'.$this->getFormWrapper() . '>';
	} 
	
	public function __toString()
	{
		return $this->getForm();
	}
	
	public function getForm()
	{
		$form = 	$this->makeFormHeader();
		$form .= 	$this->getAllElements();
		$form .= 	$this->formClose();
		
		return $form;
	}
	
	public function setElementsWrapper($wrapper)
	{
		foreach($this->_elements as $element)
			$element->setElementWrapper($wrapper);
		return $this;
	}
	
	public function createElement($elementType, $elementName, $elementOptions = array())
	{
		$elementClass = 'Point_Form_Element_'. ucfirst($elementType);
		if (class_exists($elementClass))
			$element = new $elementClass($elementName, $elementOptions);
		else 
			throw new Exception('could not find<pre>'.$elementClass.'</pre>');
		return $element;
	}
	
	/**
	 * Must follow a defined naming
	 * @param String elementType: Text | Password | Checkbox etc
	 */
	public function addElement($elementType, $elementName = null, $elementOptions = array())
	{
		$element = null;
		if ( is_string($elementType))
		{
			if (!ctype_alpha($elementType))
				throw new Exception('Invalid Element Class specified');	
				
			$element = $this->createElement($elementType, $elementName, $elementOptions);
		}
		if ($elementType instanceof Point_Form_Element)
		{
			$element = $elementType;
		}
		
		
		$this->_elements[$element->getElementName()] = $element;
		
		return $this;
	}
}