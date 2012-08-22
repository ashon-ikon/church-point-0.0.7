<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 4, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Editor_Base
{
	protected function _prepareDialog($msg, $ids, $title = 'Edit')
	{
		 /* set up the dialog options */
        $floatingDialog = new Point_Model_FloatingDialog();
        $dialogOptions = array(
        						'title' 		=> $title,
        						'id'			=> $ids . '_d',
        						'linkName'		=> $title,
        						'class'			=> '',
        						
        						/* Button Style for the css */
        						//style="padding: .4em 1em .4em 20px; text-decoration: none; position: relative;"
        						'linkCss'		=> array( 
        													'font-size' 	=> '0.85em',
        													'font-weight'	=> 'bold',
        													'line-height'	=> '1.2em',
        													'text-decoration'=>'none',
        													'color'			=> '#fff',
        													'background'	=> '#88BBD4',
        													'border'		=> '1px solid #88BBE4',
        													'padding'		=> '.4em 1em .4em 20px',
        													'position'		=> 'relative'),
        						/* Dialog settings */
        						'settings'		=> array(
															'width' 	=> 650,
															'height'	=> 600,
															'draggable'	=> 'false',
															'modal'		=> 'true',
															'resizable'	=> 'false',
															'autoOpen'	=> 'false'),	
								'others'		=> array(
															'class' => '.edit-content'));  
															
        return $floatingDialog->makeDialog($msg, $dialogOptions);
		
	}
}