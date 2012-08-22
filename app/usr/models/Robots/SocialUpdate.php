<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Jul 25, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
 
/**
 * This module automatically updates 
 */
class Point_Model_Robots_SocialUpdate
{
	/**
	 * The social network types
	 * eg. Facebook, Google+, Twitter
	 */
	var $_config		= array('config_file' => 'socialconfig.ini',
								'networks' => array(
													'facebook' => array(
																'name' 				=> 'Facebook',
																'app_id'			=> 'PPPPPPPPP261827767260166',
																'app_secret' 		=> 'PPPPPPPPP32bb561b1c23ac327a0150eb2ec2e09a',
																'app_user'			=> null,
																'app_user_activated' => false
																)
											));
	
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	
	private function __construct()
	{
		$this->_init();
	}
		
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	/**
	 * Initialize the social update robot
	 * 
	 * Reads from social config ini
	 */
	protected function _init()
	{
		// READ Config file
		$config_file			= APPLICATION_PATH . DS. 'configs'. DS .$this->_config['config_file'];
		$social_config_options	= new Zend_Config_Ini($config_file);
		
		// Merge the config options
		$this->_config 			= array_merge((array)$this->_config, $social_config_options->testing->toArray());
		
		/*
		 * Setup each network
		 */
		$networks				= $this->_config['networks'];
		foreach ($networks as $network)
		{
			$this->setupNetwork($network);		
		}
	}
	
	/**
	 * This is the handle to posting to social network
	 */
	public function post()
	{
		
	}
	
	public function setupNetwork($options = null)
	{
		if (is_array($options))
		{
			switch($options['network'])
			{
				case 'facebook':
				{
					// Setup facebook.
					$facebook_obj	= new Facebook_Facebook(array('appId', 'secret'));
				}break;
			}
		}
	}
}