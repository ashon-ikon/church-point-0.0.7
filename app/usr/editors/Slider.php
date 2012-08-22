<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 25, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Editor_Slider  extends Point_Editor_Base implements Point_Editor_Base_Interface
{
	
	/**
	 * 	Treats content
	 * 
	 * 	@return String Content with Editor based on Role
	 */
	public function treat( $content, $access, $ids, $mode = 'done')
	{
		$admin = ''; // Admin Content
		
		$content_id = makeObscureItem($ids);
		
		// Check if user is an editor or admin
		if ( Point_Model_User::GUEST != $access && Point_Model_Page_Contents::MODE_EDIT == $mode )
		{
			// Setup the edit stuff
//			self::$_content_count ++;
			$admin = $this->_prepareDialog('<p>Slider Editor</p>' . $this->_prepareForm( $content, $content_id), $content_id, 'Slider Image Editor');
			$admin = '<div class="editor-panel"><p>' .$admin. '</p></div>';
		}
		
		// do the content 
		$content = '<div id="'.$content_id.'_div">'.$content.'</div>';
		$content =(!empty($admin) ? $content .$admin: $content); // add admin html
		
		return $content;	
	}
	
	/**
	 * Form content:
	 * 		=> Slider Picture
	 * 		=> Picture info
	 */
	protected function _prepareForm( $msg, $ids)
	{
		 $tabs = new Point_Object_Tabs;
		 
		 $tab1 = '<strong>Main Slider</strong>';
		 $tab2 = '<strong>Preview</strong>';
		 
		 $tabs_content = $tabs->makeTabs(array('Image Slider Editor'=>array('content'=>$tab1, 'id'=>'tab-1'.$ids),
							   				   'Preview'	=>array('content'=>$tab2, 'id'=>'tab-2'.$ids)),
							   				   'myId'.$ids);
		
		
		return $tabs_content;
	}
	
}