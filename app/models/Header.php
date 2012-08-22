<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: header.php
 * 
 * Created by ashon
 * 
 * Created on Jul 29, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */
	class Application_Model_Header
	{
		public function __construct()
		{
			
		}
		
		/**
		* @method 	getMarkers
		* 
		* @param	$filename
		* 
		* @return mixed collection of markers found
		*/
		public function getMarkers($buffer, $pattern = null)
		{
			$markers	= array();
			if (null == $pattern) 
				$pattern = '/(\{\$\$){1}[\w +-]+:[\w +-]+\}{1}/i'; // use: {$$APP:MARKER}
			preg_match_all($pattern, $buffer, $matches);
			if(is_array($matches)){
				foreach ($matches[0] as $matche) {
					$markers[]	= $matche;
				}
				return $markers;
			}
		}
		 
		/**
		 * @method	parseFile
		 * 
		 * @param	$filename
		 * 
		 * @return mixed Correctly injected html files
		 */
		public function parseFile($filename, $pattern = null)
		{
//			echo (is_file($filename)? 'Yes is FILE' : 'No it\'s not a file'). '<br />';
			if (is_file($filename) && is_readable($filename))	{
				$buffer = NULL;
				// Open the file for reading
				if ($hFile	= @fopen($filename, 'rb'))	{
					$buffer = fread($hFile, filesize($filename));
					// Close the file
					fclose($hFile);
					$markers = $this->getMarkers($buffer);
					return $buffer;
				}
				else
					throw new Exception ('Error occured reading header file');
			}else{
				echo 'problem parsing' . $filename;
			}
		 	
		}
	}