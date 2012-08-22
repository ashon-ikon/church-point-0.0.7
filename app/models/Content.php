<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: content.php
 * 
 * Created by ashon
 * 
 * Created on Jul 24, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */
class Application_Model_Contents
{
	protected $_content_id;
//	protected $_content_key_id;
	protected $_content;
	
	function __construct(array $options = null)
	{
		if (is_array($options))
		{
			$this->setOptions($options);
		}
	}
	
	public function setOptions(array $options)
	{
		$methods 	= get_class_methods($this);
		foreach ($options as $key => $value)
		{
			$method = 'set'. ucfirst($key);
			
			if (in_array($method, $methods))
			{
				$this->$method($value);
			}
		}
		return $this;
	}
	
	public function setId($value)
	{
		$this->_c_id = (int)$value;
		return $this;
	}
	public function getId()
	{
		return $this->_c_id;
	}
	
//	public function setContentKeyId($value)
//	{
//		$this->_content_key_id = (int)$value;
//		return $this;	
//	}
//	public function getContentKeyId()
//	{
//		return $this->_content_key_id;
//	}
	
	public function setContent($content)
	{
		$this->_content = (string)$content;
		return $this; 
	}
	public function getContent()
	{
		return $this->_content;
	}
	
	public function __set($name , $key)
	{
		$method = 'set'. $this->genMethodName($name);
		if (('mapper') == $name || !method_exists ($this, $method))
		{
			throw new Exception ('Invalid content property');
		}
		$this->$method($key);
	}

	private function genMethodName($name)
	{
		$ret = '';
		if (false !== stripos($name, '_')) // if '_' is used
		{
			
			$words	 = explode( '_', $name);
			foreach ($words	as $word)
				$ret .= ucfirst($word);
			return $ret;
		}
		return $name;
	}
	public function __get($name )
	{
			
		$method = 'get'. $this->genMethodName($name);
		if (('mapper') == $name || !method_exists ($this, $method))
		{
			throw new Exception ('Invalid content property');
		}
		return $this->$method();
	}
}

