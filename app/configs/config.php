<?php
/*
 * @PROJECT: ChurchPoint
 * 
 * @FILENAME: config.php
 * 
 * Created by ashon
 * 
 * Created on Jul 27, 2011
 *
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */

// Set up the global time zone:
date_default_timezone_set('Asia/Kuala_Lumpur');

/* APPLICATION RELATED DEFINES */
defined('APP_DESCRIPTION') ||
	define('APP_DESCRIPTION', 'An easy to use paperless church administration, and ' .
							  'social portal with enhanced electronic sharing system');
defined ('APP_KEYWORDS') ||
	define('APP_KEYWORDS', 'Content Management System, Church Management System, ' .
						   'Social Point, Christian Social-networking, Work Flow, ' .
						   'Productivity, e-Church, Portal');
defined ('APP_NAME') ||
	define ('APP_NAME' , 'ChurchPoint');

defined ('APP_VERSION') ||
	define ('APP_VERSION' , '0.0.1');

defined('TOP_PAGE_ID') ||
	define ('TOP_PAGE_ID', 0);
	
defined('APP_SESSION_NAMESPACE') ||
	define ('APP_SESSION_NAMESPACE', 'point');
