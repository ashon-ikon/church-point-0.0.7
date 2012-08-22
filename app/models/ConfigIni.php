<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: ConfigIni.php
 * 
 * Created by ashon
 * 
 * Created on Jul 27, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */

	class Application_Model_ConfigIni extends Zend_Config_Ini
	{
		public function getSectionParams($section)
		{
			$params		= array();
			$sectionObj = $this->get($section);
			//$params		= ;
			foreach ($sectionObj->_data as $param => $subParam)
			{
				
				if (is_object($subParam))
				{
					$all = (array)$subParam;
					foreach( $all as $item)
						$params[$param][] = $item;
				}
//				$params[] =  $param . '<br />';
//				echo $param . ' ' .(is_array($subParam)? 'Is an array': 'Is NOT an array') . '<br />';
			}
/**/
			return $params;
		}
		
	}
