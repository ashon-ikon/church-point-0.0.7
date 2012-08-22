<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Sep 18, 2011
 * (c) 2011 Copyright
 * -------------------------------------------
 */
class Point_View_Helper_FloatingDialog {
	/**
	 * @var $_front
	 * 
	 * Handle to Front Controller
	 */
	protected	$_front;
	

	public function FloatingDialog(Zend_View $viewObj, $content = null, array $dialogConfig = array(), array $options = array())
	{
		$dialogObj	= new Application_Model_FloatingDialog();
		$baseUrl = $viewObj->baseUrl();
		if (empty ($dialogConfig))
		$dialogConfig = array(
        						'title' 		=> APP_NAME,
        						'id'			=> APP_NAME. time()	);  
		return $dialogObj->setView($viewObj)->makeDialog($content, $dialogConfig, $options);
	}
}