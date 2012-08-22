<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 24, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Form_Element_Checkbox extends Point_Form_Element
{
	protected	$_seprateLabel = true;
	
	function __construct($name, $options = array() )
	{
		$this->setElementType('checkbox');
		parent::__construct($name, $options);
		return $this;
	}
	
	public function seperateLabel($option = true)
	{
		$this->_seperateLabel = $option;
		return $this;
	}
	
	public function getHtml()
	{
		$html = '<' . $this->_elementWrapperHtml.'>';
		if ($this->getLabelPosition() == Point_Form_Element::POINT_FORM_ELEMENT_LABEL_PREPEND )
		{
			if(!$this->_seprateLabel)
			{
				// Do: <tag><td>&nbsp;</td><td>{label}{element}</td></tag>
				$fakelabel = $this->wrapElement('&nbsp;', $this->_elementLabelWrapper);
				$html .= $this->wrapElement(
											 $fakelabel . $this->getLabelHtml() . $this->getElementHtml(), 
											 $this->_elementWrapperHtml);
			}
			else
			{
				$html .= $this->getLabelHtml();
				$html .= $this->getElementHtml();
			}
		}
		else
		{
			$html .= $this->getElementHtml();
			$html .= $this->getLabelHtml();
		}
		$html .= '</' . $this->_elementWrapperHtml.'>';
		return $html;
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
			$html .= (!$this->_seprateLabel)? $label : $this->wrapElement($label, $this->_elementLabelWrapper);
		}else{
			$html = $label = $this->wrapElement($this->getLabel(),
										'label' , 
										array('for'		=> strtolower($this->getElementName()),
											  'class'	=> 'label'));		
		}
		return $html;
	}
}