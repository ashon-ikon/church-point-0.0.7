<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 23, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */

defined('DS')
	|| define ('DS' , DIRECTORY_SEPARATOR );

$G_days		= array(1=>'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
$G_months		= array(1=>'January', 'February','March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');


if (!function_exists('pr'))
{
	function pr($array)
	{
		echo '<pre>';
		echo print_r($array, true);
		echo '</pre>';
	}
}

if (!function_exists('vp'))
{
	function vp($variable)
	{
		echo '<pre>';
		echo $variable;
		echo '</pre>';
	}
}

/**
 * Cleans all html entities from code
 */
function remove_html_tags($content, string $allowed_tags = null)
{
	/* remove unencoded stuff */
	$ret = strip_tags($content, $allowed_tags);
	
	/* remove encoded stuff */
	$ret = preg_replace('/&#?[a-z0-9]{2,8};/i','', $ret);
	
	return $ret;
}

// Helper function to remove trailing slash
function remove_trailing_slash($path)
{
	if (substr($path, -1) == DS )
			$path = substr($path, 0, strlen($path) - 1);
	return $path;
}

// Helper function to check if file exists and we have reading permissions
function readable($file)
{
	return (file_exists($file) && is_readable($file));
}

// Helper function to check if file exists and we have WRITING permissions
function writable($file)
{
	return (file_exists($file) && is_writable($file));
}

// Helper function to convert params to uri
function params2uri($params = array())
{
	$ret = '?';
	//http://churchpoint.local/default/index/index?module=default&controller=index&action=index&editmode=edit&f=2011/10/25/abdsf
	foreach ($params as $key => $param)
	{
		if ($key == 'module' || $key == 'action' || $key == 'controller')
			continue;
		$ret .= $key. '=' .$param . '&';
	}
		
	if (substr($ret, -1) == '&' ) // remove trailing '&'
		$ret = substr($ret, 0, strlen($ret) - 1);
	
	return $ret;
}


/**
 * 	Helps to store info on the session 'securely'
 */
function makeObscureItem($item) 
{
	/**
	 * ENSURE ALL NEEDED NAMESPACE IS INITIALIZED
	 */
	
	if (!defined('APP_NAMESPACE_INIT'))
		return false;
		
	$session = Point_Object_Session::getInstance();
	$data = array();
	// Retrieve the old values if any
	if (isset($session->obscure))
	{
		if(is_array($session->obscure))
			$data = $session->obscure;
	}
	$random = substr(md5(uniqid()), 0, 10);
	$data [$random] = $item; 
	// store the new data
	$session->obscure = $data;
   		
	return $random;
}

/**
 *	Helps to retrieve true info from session
 */
function getTrueItem($randomHash) 
{
	/**
	 * ENSURE ALL NEEDED NAMESPACE IS INITIALIZED
	 */
	if (!defined('APP_NAMESPACE_INIT'))
		return false;
		
	$session = Point_Object_Session::getInstance();
	$var 	 = 'obscure_'. '55';//$randomHash;
  	if(isset($session->obscure)) 
  	{ 
  		$data = $session->obscure;
    	if(array_key_exists($randomHash, $data)) return $data[$randomHash];
  	} 
	return false;
}


/**
 *  remove an array using key
 */
function unset_key(&$array = array(), $key)
{
	if (is_array($array) && array_key_exists($key, $array))
	{		
		unset($array[$key]);
	}
}

function makeUniqueHash($seed)
{
	/**
	 * ENSURE ALL NEEDED NAMESPACE IS INITIALIZED
	 */
	if (!defined('APP_NAMESPACE_INIT'))
		return false;
	return md5(Zend_Controller_Front::getInstance()->getRequest()->getClientIp() . $seed . strlen($seed) );
}

/**
 * Function to clean string from poisinus characters 
 */
function cleanReplace($string, $replacement = " ", $skip = array(), $addition = array())
{
	$poison = array(' ', '\'', '"','-', '`', '´', "\a", "\n", "\r","%0a", "%0d");
	
	if (!empty($addition))
	{
		$poison = array_merge($poison, $addition); 
	}
	
	/* Sometimes you wish to allow some words ;) */
	if ( is_array($skip))
	{
		foreach($skip as $good)
		{
			foreach($poison as $key => $type)
			{
				if($type == $good)
				{
					unset($poison[$key]);
				}
			}
		}
	}
	return str_replace($poison, $replacement, $string);
}

function wrapHtml($data = '', $tag, $attrs = null, $single_tag = false)
{
	$ret =  '<'.$tag;
	if (is_array($attrs) && !empty($attrs))
	{
		$ret .= ' ';
		foreach($attrs as $attr => $val)
		$ret .= $attr .'="'.$val .'" ';
	}
	if ($single_tag)
		$ret .= ' />';
	else
		$ret .= '>'. $data. '</'.$tag.'>';
	return $ret;
}

function getGVar($GlobalVariable, $index, $default = null)
{
	if (isset($GlobalVariable[$index]))
	{
		return $GlobalVariable[$index];
	}
	else
		return $default;
}


function encrypt($msg, $salt) 
{
	//echo "<p>msg: '$msg' <br /> salt: $salt<br /></p>";
	$encrypt =  trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $msg, MCRYPT_MODE_ECB, 
							mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), 
							MCRYPT_RAND))));
//	file_put_contents('/var/www/php-stuff/vbrand2/var/test.log' , "\n".'Encrypt: '. $encrypt . ' Msg: '. $msg . ' Salt: ' . $salt, FILE_APPEND );
	return $encrypt; 
} 
	
function decrypt($msg, $salt) 
{
	//echo "<p>msg: '$msg' <br /> salt: $salt<br /></p>";
	$decrypt = trim(mcrypt_decrypt( MCRYPT_RIJNDAEL_256, $salt, base64_decode($msg), MCRYPT_MODE_ECB, 
							mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), 
							MCRYPT_RAND)));
//	file_put_contents('/var/www/php-stuff/vbrand2/var/test.log' , "\n".'Decrypt: '. $decrypt . ' Msg: '. $msg . ' Salt: ' . $salt, FILE_APPEND );
	return $decrypt; 
}

function getDateSelect( $parts_lag = ASHON_DAY , $start_date = null, $classes = array())
{
	$dayclass = getGVar($classes , 'day');
	$monthclass = getGVar($classes , 'month');
	$yearclass = getGVar($classes , 'year');
	
	$ret = null;
	if ($parts_lag & ASHON_DAY)
	{	
		$ret .= '<select ';
		$ret .= ' name="day" '. (!empty($dayclass)? ' class="'. $dayclass.'" ': '') . '>';
		$ret .= '<option value="">-- Day --</option>';
		for($i = 1; $i <= 31; $i++)
			$ret .= '<option value="'. $i.'">' .$i.'</option>';
		$ret .= '</select> '; 		
	}
	
	if ($parts_lag & ASHON_MONTH)
	{
		global $months;
		$ret .= '<select name="month" '. (!empty($monthclass)? ' class="'. $monthclass.'" ': '') . '>';
		$ret .= '<option value="">-- Month --</option>';
		foreach( $months as $month )
			$ret .= '<option value="'. $month.'">' .$month.'</option>';
		$ret .= '</select> '; 		
	}
	
	if ($parts_lag & ASHON_YEAR)
	{
		$ret .= ' <select ';
		$ret .= ' name="year" '. (!empty($yearclass)? ' class="'. $yearclass.'" ': '') . '>';
		$ret .= '<option value="">-- Year --</option>';
		for($i = 1920; $i <= date('Y',time()); $i++)
			$ret .= '<option value="'. $i.'">' .$i.'</option>';
		$ret .= '</select>'; 		
	}
	return $ret;
}

/**
 * Returns date params in the form
 * 
 * array('month num*', 'day', 'year', 'hour', 'min', 'sec')
 * 
 */
function getDateArray($unix_time_date = null)
{
	if ($unix_time_date == null)
		$unix_time_date = date();

	if (!is_numeric($unix_time_date))
	{
		$unix_time_date	= strtotime($unix_time_date);
	}
	
	$ret_time	= array(
					'month'	=> date('n', $unix_time_date),
					'week'	=> date('W', $unix_time_date),
					'day'	=> date('j', $unix_time_date),
					'year'	=> date('Y', $unix_time_date),
					'hour'	=> date('G', $unix_time_date),
					'min'	=> date('i', $unix_time_date),
					'sec'	=> date('s', $unix_time_date),
					);
	return $ret_time;
}

/**
 * This function checks if the date is valid
 * 
 * @author php.net
 * 
 * @internal Adapted and modified to allow for dates without leading 0
 */
function isValidDateFormat($date)
  {
	$converted=str_replace(array('/' , '.' ), '-',$date);		// Ensure only '-' is used for spacing

	if(preg_match("/^((((19|20)(([02468][048])|([13579][26]))-0?2-29))|((20[0-9][0-9])|(19[0-9][0-9]))-((((0?[1-9])|
		(1[0-2]))-((0?[1-9])|(1\d)|(2[0-8])))|((((0?[13578])|(1[02]))-31)|(((0?[1,3-9])|
		(1[0-2]))-(29|30)))))/",$converted)===1)
	{
		return true;
	}
	
	return false;	// Explicit false
  } 

/**
 * Gets the next possible word from a string
 * 
 * @param string 	$words The content of the set of words
 * @param int		$length desired character length
 * @param boolean	$skip_dot Should dots be allowed or not
 * 
 * @return string 	snipped words
 */ 
function snipByWords($words, $length = null, $skip_dot = true)
{
	$words .=  ' '; // Ensure last word shows forth 
	
	if (null !== $length)
	{
		if ($words_len 	= mb_strlen($words) > $length)
		{
			$words		= mb_substr($words, 0, $length, 'UTF-8');
		}
	}
	
	if (!$skip_dot) // Remove the unwanted dots...
		str_replace('.', '', $words);
	
	
//	$pattern	= '@^([\w\s ]) ([\w\s ])$@Us';
//	$match		= null;
//	preg_match($pattern, $words, $match);
//	echo '<pre>',print_r($match, true),'</pre>';
	return $words;
	
	while (mb_substr($words, -1, 1, 'UTF-8') != ' ')
	{
//		echo mb_substr($words, -1, 1, 'UTF-8');
		$words = mb_substr($words, 0, -1, 'UTF-8');
//		echo  '+<br /> Not eq != space:' . mb_substr($words, -1, 1, 'UTF-8') != ' ' ? 'No' : 'Yes',  '<br /> ';
	}

	return $words;
}

/**
 * This function generates a nicely formatted
 * SEO aware string.
 */
function makeSeoString($string, $pad = '-')
{
	$seo_string 	= null;
	$words = preg_split('/ +/',$string);
	if (count($words) > 0)
	{
		foreach($words as $word )$seo_string .= $word . $pad;
	}
	$seo_string 	= strtolower(rtrim($seo_string, $pad));
	
	$seo_string	= cleanReplace($seo_string, null , array($pad), array('.','?','\\','/'));
	
	return $seo_string;
}

function genRandomString($required_length = 10, $startAlpha = false) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string = '';
    $alphaPos= 11;
    if ($startAlpha)
    {
    	for ($p = $alphaPos; $p < $required_length + $alphaPos; $p++) 
	    {
	        $string .= $characters[mt_rand($alphaPos, strlen($characters) - 1)];
	    }
    }
    else
    {
	    for ($p = 0; $p < $required_length; $p++) 
	    {
	        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
	    }
    }
    return $string;
}

function genRandomNumString($required_length = 10) {
    $characters = '0123456789';
    $string = '';
    
    
    for ($p = 0; $p < $required_length; $p++) 
    {
	    $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }
    
    return $string;
}

function getArrayVar($array, $key, $default = null)
{
	if (!is_array($array))
		return;
		
	if ( array_key_exists($key, $array) && $array[$key] != null)
		return $array[$key];
	else
		return $default;
}

function printArray($array)
{
	echo '<pre>', print_r($array, true), '</pre>';
}

function cleanSlashes(&$text)
{
	if (is_string($text))
	{
		if (get_magic_quotes_gpc())
		{ 
			$text = stripcslashes($text);
			$text = stripslashes($text);
		}
	}
	
	return $text;
}

/**
 *  sanitization
 */ 
function sanitizeVariables(&$item, $cleanText = true) 
{ 
	if (!is_array($item)) 
	{ 
		// undoing 'magic_quotes_gpc = On' directive
		if (get_magic_quotes_gpc()) 
			$item = stripcslashes($item); 
		if ($cleanText)
			$item = sanitizeText($item); 
	} 
} 

// does the actual 'html' and 'sql' sanitization. customize if you want. 
function sanitizeText($text, $trim = false) 
{ 
	$text = str_replace("<", "&lt;", $text); 
	$text = str_replace(">", "&gt;", $text); 
	$text = str_replace("\"", "&quot;", $text); 
	$text = str_replace("'", "&#039;", $text); 
	
	// it is recommended to replace 'addslashes' with 'mysql_real_escape_string' or whatever db specific fucntion used for escaping. However 'mysql_real_escape_string' is slower because it has to connect to mysql.
	
	if (defined('APP_NAMESPACE_INIT'))
	{
		$db   = new Zend_Db_Table('users_table');
		
		$text = $db->getAdapter()->quote($text);
		
	} 
	else
		$text = addslashes($text); 
	if ($trim)
		$text = trim($text, '\'');
	return $text; 
}

function getTagContent($html_string, $tag)
{
	$patterns		= '@<'.$tag.'\b[^>]*>(.*?)</'.$tag.'>@U'; 
	$ret			= null;
	
	/* Get the first paragraph */
	$match			= null;
	
	if (preg_match_all($patterns, $html_string, $match))
	{
		return $match[1][0];
	}
	
}
/**
 * Get the systems temporary directory
 */
if ( !function_exists('sys_get_temp_dir')) 
{
  function sys_get_temp_dir() 
  {
      if( $temp=getenv('TMP') )        return $temp;
      if( $temp=getenv('TEMP') )        return $temp;
      if( $temp=getenv('TMPDIR') )    return $temp;
      $temp=tempnam(__FILE__,'');
      if (file_exists($temp)) 
      {
          unlink($temp);
          return dirname($temp);
      }
      return null;
  }
}

/**
 * This function perses a text and replaces the content with Bible reference
 * 
 * For now it uses Bible Gateway
 * 
 * @return string HTML anchor to Gateway Bible reference
 */
function scrubBibleRef($content)
{
	$books_names	= 'Genesis|Exodus|Leviticus|Numbers|Deuteronomy|Joshua|Judges|Ruth|1 Samuel|2 Samuel|1 Kings|2 Kings|1 Chronicles|2 Chronicles|' .
					  'Ezra|Nehemiah|Esther|Job|Psalm|Proverbs|Ecclesiastes|Song of Songs|Song of Solomon|Isaiah|Jeremiah|Lamentations|' .
					  'Ezekiel|Daniel|Hosea|Joel|Amos|Obadiah|Jonah|Micah|Nahum|Habakkuk|Zephaniah|Haggai|Zechariah|Malachi|' .
					  'Matthew|Mark|Luke|John|Acts|Romans|1 Corinthians|2 Corinthians|Galatians|Ephesians|Philippians|Colossians|1 Thessalonians|' .
					  '2 Thessalonians|1 Timothy|2 Timothy|Titus|Philemon|Hebrews|James|1 Peter|2 Peter|1 John|2 John|3 John|Jude|Revelation';
					  
	$pattern		= '@('.$books_names.') ([1-9]{1,3}0?)( ?: ?([1-9]{1,3}0?)( ?[~-] ?[1-9]{1,3}0?)?)*@i';
	$matches		= null;
	
	$version		= 'version=NIV';
	$link_url		= 'http://www.biblegateway.com/passage/?search='; /* CHANGE THIS AS YOU CHOOSE */
	
	if (is_string($content))
	{
		preg_match_all($pattern, $content, $matches);
		if (is_array($matches) && !empty($matches))
		{
			$scriptures	= $matches[0];
			
			foreach($scriptures as $scripture)
			{
				$replacement = wrapHtml($scripture, 'a',array('href'	=> $link_url . urlencode($scripture).'&'.$version, 
															  'title'	=> 'Read ' .$scripture . ' on BibleGateway.com', 
															  'target'	=> '_blank',
															  'class' 	=> 'bible-reference'));
				$content	= str_replace($scripture, $replacement, $content);
			}
			
		}	
	}
	return $content;
}
/**
 * This function extracts the first few lines using the DOM Objects
 * 
 */
function getShowcaseText($content, $required_len = 600)
{
	$dom_obj	= new DOMDocument();
	
	$head			= '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>';
	$html_content 	= $head . wrapHtml(snipByWords($content, $required_len) . ' ...', 'body');
	
	@$dom_obj->loadHTML( $html_content );
	
	$body		= $dom_obj->getElementsByTagName('body');
	
	/**
	 * We would keep trying the block elements...
	 */
	
	$body_element	= $body->item(0);
	$body_html	= null;
	
	preg_match('@<body\b[^>]*>(.*?)</body>@Us',$dom_obj->saveHTML(), $body_html);
	
//	echo '<pre>', print_r($body_html, true), '</pre>'; exit;
	
	return $body_html[1];
}

/**
 * This function extracts the first few lines
 */

function getShowcaseParagraph($content, $required_len = 600)
{
	
	/* TODO: We are to use simple xml instead 
	 *
	 *  simplexml_load_string($xmlstr);
	 * */
	 
//	$proper_html_content 	= '<?xml version=\'1.0\' ? >' . wrapHtml($content, 'body');
//	$content_xml 			=	simplexml_load_string($proper_html_content);
//	echo $content_xml; exit;
	
	// <p\b[^>]*>(.*?)</p>
	$patterns		=  '@<(p|div)\b[^>]*>(.*?)</\1>@Us';
//	$patterns		= '@<(?:"[^"]*"[\'"]*|\'[^\']*\'[\'"]*|[^\'">])+(?<!/\s*)>@'; 
//	$patterns		= '@<(p|ol|ul)\b[^>]*>(?!<\1(.*?)>)</\1>@Us'; 
//	$patterns		= '@<(div|p|ol|ul)\b.*>(.*?)</\1>@U'; 
	$ret			= null;
	
	/* Get the first paragraph */
	$match			= null;
	
	if (preg_match_all($patterns, $content, $match))
	{
		echo '<pre>', print_r($match,true), '</pre>'; exit;
		if (strlen($match[2][0])> $required_len)
		{
			$para_content = $match[2][0];
			
			$para_content = substr($para_content, 0, $required_len);
			
			$html_tag	  = $match[1][0];			
			
			/* truncate word to meaningful stuff */
			while (substr($para_content, -1, 1) != ' ')
			{
				$para_content = substr($para_content, 0, -1);
			} 
			
			$parag	= $para_content;//substr($para_content, 0, $required_len);
			$ret	= wrapHtml($parag . '...', $html_tag);
		}
		else
		{
					
			$para_content1 	= $match[2][0];
			$html_tag	  	= $match[1][0];			
	
			/* store prepared paragraph 1 */
			$ret			= wrapHtml($para_content1, $html_tag);
			
			
			/* run a foreach stuff and checkk checking until we reach end or get to limit */
			
			/*!!!!remove first paragraph */
			
			array_shift($match[1]);
			array_shift($match[2]);
		
			$debug		=  $pointer = 0;
			
			foreach($match[2] as $paragraph)
			{
		
				$html_tag	  = $match[1][$pointer++];			
	
				if (isset($paragraph) && null != $paragraph)
				{
					
					/* add this paragraph */
					
					/* Create a test paragraph */
					$temp_para		= $ret . wrapHtml($paragraph . ' ...', $html_tag);
					
					$estimated_len	= strlen($temp_para);
					
					$old_len		= strlen($ret);
		
					/* New paragraph is too long so we need to take just a little */
					if ($estimated_len > $required_len)
					{
						$overshoot_len 		= $estimated_len - $required_len;
						
						$expected_para2_len = strlen($paragraph) - $overshoot_len;
						/* Take only the little portion of this paragraph */
						$new_para 			= substr($paragraph, 0 , $expected_para2_len);
						
						/* truncate word to meaningful stuff */
						while (substr($new_para, -1, 1) != ' ')
						{
							$new_para 	= substr($new_para, 0, -1);
						} 
						
						
						$final_p		= wrapHtml($new_para . ' ...', $html_tag);
						
						/* Add content */
						$ret 			.=  $final_p;
						
						/* Stop check /adding paragraphs */
						break;
					}
					else
					{	
						
						/* Return para1 and para2 */
						$ret 	.= wrapHtml($paragraph , $html_tag);
						
					}
				}
			}
		}
		
		return $ret ;	
	}
}