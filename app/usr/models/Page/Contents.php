<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 20, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Model_Page_Contents 
{
	protected	$_plugins	= null;
	/**
	 * @var array	mixed
	 */
	protected	$_markers	= null;
	
	/**
	 * @var Point_Model_Pages_Template Template holder
	 */
	protected	$_template	= null;
	
	/**
	 * Db Adapter for Contents
	 */
	protected	$_adapter	= null;
	
	/**
	 * 	Contents Constants
	 * 
	 */
	const		ACCESS_GUEST				= 'guest';
	const		ACCESS_MEMBER				= 'member';
	const		ACCESS_MODERATOR			= 'moderator';
	const		ACCESS_ADMIN				= 'admin';
	
	const		MODE_DONE					= 'done';
	const		MODE_EDIT					= 'edit';
	
	public function __construct()
	{
		// Do the database thingy
		$this->_adapter = new Zend_Db_Table( 'contents_table' );	
	}
	
	
	/**
	 * Here we perform some preg matching ;)
	 * {{%hypertext:wordquote1%}}
	 *	/({{%)[a-z]+:[a-zA-Z0-9_-](%}})/
	 *	@return array array(editor , marker)
	 */
	public function _getSections($stream , $pattern)
	{
		$editor_marker = array();
	
		
		if (!empty($stream) && $pattern !== null)
		{
			
			$ma = null;
			preg_match_all($pattern, $stream, $ma);
				
			if(is_array($ma))
				foreach ($ma[0] as $m)
					$markers[] = $m;
		}
			else
				throw new Zend_Exception('Empty stream or invalid pattern passed!');

			
		foreach ($markers as $haystack)
		{
			// Get editor & marker
			$pos 	= 	strpos($haystack,':');
			$editor	=	substr($haystack, 0 , $pos);
			$editor =	str_replace(array('{','%','}'), '', $editor);
			$marker =	strstr($haystack, ':');
			$marker =	str_replace(array(':','%','}'), '', $marker);
			
			$editor_marker[] = array('editor' 		=> $editor , 
									 'marker'		=> $marker,
									 'delimiter'  	=> $haystack);
		}
		
		return $editor_marker;
	}
	
	
	
	/**
	 * This build all the contents based on the role
	 * 
	 * @return Array each content
	 * ----------------------------------------------
	 */
	protected function _prepareContent( &$template, &$results, $access)
	{
		
		$broker = Point_Editor_Broker::getInstance();
		
		// Go through each section and call the broker. 
		$sections		= array();
		$pattern 		= '/({{%)[a-z]+:[a-zA-Z0-9_-]+(%}})/';
		$read_contents 	= array();
		$padded_markers = array();
		$publication_id = null; // extract from results
		
		$sections = $this->_getSections($template, $pattern);
		
		
		
		// Get mode
		$session = Point_Object_Session::getInstance();
		$mode	 = 'done';
		if (isset($session->edit_mode))
			$mode 	 = $session->edit_mode;
		$role = Point_Model_User::getInstance()->getRole();
		
		
		foreach ($sections as $section)
		{
			$editor = $broker->getEditor($section['editor'], null);
			$content = '';
			/**
			 *  IDs will be encoded in the following order:
			 * 	publication_id / editor_id / content_id / marker
			 */

			$ids	= null;
			$break	= false; // used for delay break :)
			foreach ($results as $result)
			{
				/**
				 * temporary storage in case we dont get valid id's
				 */
				if(null == $publication_id)
						$publication_id = $result['publication_id'];
						
				$xeditor_id 	= $section['editor']; // 
				$xcontent_id	= $section['marker'];
				
				if ($section['marker'] == $result['content_marker'])
				{
					
					$content = $result ['content'];
					 
					//-------------------------------------------------
					// Modify the Xvalues since we have a valid id
					//-------------------------------------------------
					$xeditor_id	= $result['editor_id'];
					$xcontent_id 	= $result['content_id'];
							
					$break = true;
				}
				$ids = $publication_id  . '/' . $xeditor_id . '/' . $xcontent_id;
				
				//file_put_contents('/var/www/php-stuff/tests.log',"\n". $ids, FILE_APPEND);
				if ($break) break;
			}
			
			
			// compute IDs
			/*
			 * Array
        (
            [publication_id] => 3
            [page_ref] => defaultindexindex
            [publication_title] => blessings
            [publication_date] => 2011-10-31
            [content_id] => 5
            [content_marker] => content1
            [content] => This is a new text
            [contents_editors_id] => 1
            [content_date] => 2011-10-25 15:12:24
            [editor_id] => 1
            [editor_name] => hypertext
        )
			 */
			$read_contents[] = $editor->treat( $content, $role, $ids, $access );
			$padded_markers[] = '/'. $section['delimiter'] . '/' ;
		}
		
		/**
		 * Substitute the markers for content
		 * ----------------------------
		 */
		
		return preg_replace($padded_markers, $read_contents, $template);
		
	}
	
	public function buildContent( &$results, $template, $access = Point_Model_Page_Contents::ACCESS_GUEST )
	{
		
		// Set our template
		$this->_template = $template;
		
		// Get Editors and associated contents
		return $this->_prepareContent( $this->_template, $results, $access);
		
	}
	
	/**
	 * Returns false if unable to update content
	 */
	public function writeContent($content, $hash, $ids , $new = false)
	{
		// Strip unessary slashes in case.
		$content = stripcslashes($content);
		
		if ( $hash && $ids )
		{
			/*===================
			 * Retrive each id
			 *===================
			 */
			/**
			 *  IDs were encoded in the following order:
			 * 	publication_id / editor_id / content_id
			 */
			
			$id_array = explode('/', $ids);
			if (count($id_array) != 3) return array(false,'Failed: Irreguar identifiers'); // ensure we have only 3 times
			
			$publication_id = $id_array[0];
			$editor_id		= $id_array[1];
			$content_id		= $id_array[2];
			
			
			
			
			// ensure we have valid numeric IDs
			if (!ctype_digit(strval($publication_id))) return array(false,'Failed: Unknown publication'); // This MUST BE KNOW else we are in trouble
			
			$editor_info = null;
			
			if (!ctype_digit(strval($editor_id)))	// We try to retrieve from database.
			{
				$adapter  = new Zend_Db_Table('contents_editors_table');	
				$editor_info	  = $adapter->select()
							->where('editor_name = ?', $editor_id)
							->query()->fetch();
							
				if (empty($editor_info)) return array(false,'Failed: Unknown editor'); // No matching editor
				
				$editor_id = $editor_info['editor_id'];
				
			}
			
			$adapter = new Zend_Db_Table('contents_table');
			
			/* Attempt to get a valid content marker name from id */
			$content_marker = $content_id;
			if (ctype_digit(strval($content_id)))
			{
				/* retrieve the equivalent marker */
				$info = $adapter->select()->where('content_id = ?', $content_id)->query()->fetch();
				$content_marker = $info['content_marker'];
			}
			
//			file_put_contents('/var/www/php-stuff/tests.log',"\n". print_r($content, true), FILE_APPEND);
			if ($new)
			{
				
				
				/* create new content */
				//	content_id	publication_id	content_marker	content	contents_editors_id	
				$data = array(
					'publication_id' 		=> $publication_id,
					'content_marker' 		=> $content_marker,
					'content'		 		=> $content,
					'contents_editors_id'	=> $editor_id,
					'content_date'	 		=> new Zend_Db_Expr('Now()')
				);
				
				if(($content_info['content_id'] = $adapter->insert($data)) === false)
				{
					return array(false,'Failed: Adding new content');
				}// No matching editor
			}
			else
			{
				
				/* find existing content */
				
				/* update existing content */
				$data = array(
					'publication_id' 		=> $publication_id,
					'content_marker' 		=> $content_marker,
					'content'		 		=> $content,
					'contents_editors_id'	=> $editor_id,
				);
				$pr = $data;
				$pr['content_id'] = $content_id;
				//file_put_contents('/var/www/php-stuff/tests.log',"\n". print_r($pr, true), FILE_APPEND);
				$where = $adapter->getAdapter()->quoteInto('content_id = ?', $content_id);
				//file_put_contents('/var/www/php-stuff/tests.log',"\n". print_r($where->__toString(), true), FILE_APPEND);
				$result = null;
				if(($result = $content_info['content_id'] = $adapter->update($data, $where)) === false)
				{
					/* if fail update return false */
					return array(false,'Failed: Updating content');
				}else {
					if ($result != 1)
						return array(true,'Notice: Content unchanged updates');
				}

//				file_put_contents('/var/www/php-stuff/tests.log',"\n". print_r($result, true), FILE_APPEND);
			}
			
			return array(true,'Success');
		}
		return array(false,'Failed: Unknown content action');
	}
}