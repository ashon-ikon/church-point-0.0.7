<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 25, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */


class Point_Form_Element
{
	protected	$_elementWrapperHtml	= 'div';
	protected	$_elementCellWrapper	= 'div';
	
	protected	$_elementName			= null;
	protected	$_elementType			= null;
	protected	$_elementLabel			= null;
	protected	$_elementLabelPosition	= Point_Form_Element::POINT_FORM_ELEMENT_LABEL_PREPEND;
	protected	$_elementLabelWrapper	= null;
	protected	$_elementOptions		= array(
											'class' => 'input'
										);
	protected	$_removeLabel			= false;
	
	
	const	POINT_FORM_ELEMENT_LABEL_PREPEND	= 'prepend';
	const	POINT_FORM_ELEMENT_LABEL_APPEND		= 'append';
	
	function __construct($name, $options = null)
	{
		if (!ctype_alpha($name))
			throw new Exception('Invalid Input Element name supplied<pre>'.$name.'</pre>');
		$this->setElementName($name);
		if(array_key_exists('label', $options))
			$this->setLabel($options['label']);
		$this->mergeOptions($options);
	}
	
	protected function getElementHtml()
	{
		$html = '<input type="'. $this->getElementType() . '" ';
		
		if(!array_key_exists('name', $this->_elementOptions))
			$this->_elementOptions['name'] = $this->getElementName();
			
		if(!array_key_exists('value', $this->_elementOptions))
			$this->_elementOptions['value'] = '';
			
		foreach ( $this->_elementOptions as $attr => $val )
			$html .= $attr . '="'. $val . '" ';
		
		$html .= '/>'; 
		return $html;
	}
	
	public function setElementName( $name = null)
	{
		if ($name)
			$this->_elementName = $name;
		return $this;
	}
	
	public function getElementName()
	{
		return $this->_elementName;
	}
	
	protected function setElementType( $type = null)
	{
		if ($type)
			$this->_elementType = $type;
		return $this;
	}
	
	protected function getElementType()
	{
		return $this->_elementType;
	}
	
	/**
	 * Set Label
	 */
	public function setLabel( $label , $position = Point_Form_Element::POINT_FORM_ELEMENT_LABEL_PREPEND)
	{
		if ($label)
		{
			$this->_elementLabel = $label;
			$this->_removeLabel	= false;
			$this->setLabelPosition($position);
		}
		return $this;
	}
	
	public function getLabel()
	{
		if (null === $this->_elementLabel)
		{
			$this->_elementLabel = $this->_elementName;
		}
		return $this->_elementLabel;
	}
	
	public function removeLabel()
	{
		$this->_removeLabel	= true;
		return $this;
	}
	
	public function setLabelPosition($position)
	{
		$this->_elementLabelPosition = $position;
		return $this;
	}
	
	public function setLabelWrapper( $wrapper)
	{
		if ($wrapper)
		{
			$this->_elementLabelWrapper = $wrapper;
		}
		else 
			$this->_elementLabelWrapper = $this->_elementCellWrapper;
		return $this;
	}
	
	public function getLabelWrapper()
	{
		if (null === $this->_elementLabelWrapper)
		{
			$this->setLabelWrapper();
		}
		return $this->_elementLabel;
	}
	
	public function setElementWrapper( $elementWrapper, $dataCellWrapper =  'td')
	{
		if ($elementWrapper)
		{
			$this->_elementWrapperHtml = $elementWrapper;
		}

		if ($dataCellWrapper)
		{
			$this->_elementDtWrapper = $dataCellWrapper;
		}
		
		return $this;
	}
	
	public function getElementWrapper()
	{
		if (null === $this->_elementWrapperHtml)
		{
			$this->setElementWrapper();
		}
		return $this->_elementWrapperHtml;
	}
	
	
	public function getLabelPosition()
	{
		if (null === $this->_elementLabelPosition) // Use prepend
			$this->_elementLabelPosition = Point_Form_Element::POINT_FORM_ELEMENT_LABEL_PREPEND;
			
		return $this->_elementLabelPosition;
	}
	
	protected function mergeOptions(array $options)
	{
		if( !empty($options) )
			foreach ($options as $option => $value)
				$this->_elementOptions[strtolower($option)] = $value;
		return $this;
	}
	
	public function __toString()
	{
		return $this->getHtml();
	}
	
	public function getHtml()
	{
		
		if ($this->getLabelPosition() == self::POINT_FORM_ELEMENT_LABEL_PREPEND )
		{
			$html = $this->getLabelHtml();
			$html .= $this->getElementHtml();
		}
		else
		{
			$html = $this->getElementHtml();
			$html .= $this->getLabelHtml();
		}
		$html = $this->wrapElement( $html, $this->_elementWrapperHtml );
		
		return $html;
	}
	
	protected function wrapElement($element, $tag, array $attrs = array())
	{
		$ret =  '<'.$tag;
		if (!empty($attrs))
		{
			$ret .= ' ';
			foreach($attrs as $attr => $val)
			$ret .= $attr .'="'.$val .'" ';
		}
		$ret .= '>'.$element. '</'.$tag.'>';
		return $ret;
	}
	
	protected function getLabelHtml()
	{
		/**
		 * If no label is set go back
		 */
		if ($this->_removeLabel) return '';
		
		$html = null;
		if ( !empty($this->_elementLabelWrapper) )
		{
			$label = $this->wrapElement($this->getLabel(),
										'label' , 
										array('for'		=> strtolower($this->getElementName()),
											  'class'	=> 'label'));
			$html .= $this->wrapElement($label, $this->_elementLabelWrapper);
		}else{
			$html = $label = $this->wrapElement($this->getLabel(),
										'label' , 
										array('for'		=> strtolower($this->getElementName()),
											  'class'	=> 'label'));
		}
		return $html;
	}
	
}