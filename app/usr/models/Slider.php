<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Jun 27, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_Slider extends Point_Model_BaseClass {
	
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	
	const SLIDER_TYPE_PICTURE		= 'picture';
	const SLIDER_TYPE_HYPERPICTURE	= 'hyperimage';
	const SLIDER_TYPE_TEXT			= 'text';
	const SLIDER_TYPE_MIXED			= 'mixed';
	
	
	const SLIDER_ACTIVE_ACTIVE		= 'active';
	const SLIDER_ACTIVE_INACTIVE	= 'inactive';
	
	private function __construct()
	{
		$this->init();
	}
		
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
	public function init()
	{
		$this->_table_name = 'slider_table';
	}
	
	/**
	 * Add new slide to slider
	 */
	public function addSlide(
			$slide_html = '', $slide_pic_location = '', $slide_pic_title = '',	$slide_pic_size = '',
			$slide_position, 
			$slide_active = Point_Model_Slider::SLIDER_ACTIVE_ACTIVE, 
			$slider_type = Point_Model_Slider::SLIDER_TYPE_PICTURE)
	{
		
	}
	 
	/**
	 * Update slide
	 */
	/**
	 * Remove slide
	 */
	/**
	 * Fetch slide from sliders
	 */
	/**
	 * Fetch ALL sliders from sliders
	 */
	public function getSlides ( $start = 0 , $amount = 10, $order = 'ASC')
	{
		$db = $this->getDbTable();
		
		$query = $db->select()->order('slide_position ' .$order)->limit($amount, $start);
						
		$slides 	= $query->query()->fetchAll();
		
		if ($slides)
		{
			foreach ($slides as &$slide)
			{
				// Create relative path form site root.
				$slide['img_location'] = $this->getSliderImgPath( $slide['slide_pic_location'] );
			}
			return $slides;
		}
	}
	 
	public function getTopSlides ( $amount = 10)
	{
		return $this->getSlides(0, $amount);	
	}
	
	public function getSliderImgPath ($path, $absolute = false)
	{
		$full_path 	= remove_trailing_slash(APP_SLIDER_IMAGES_DIRECTORY) . DS . $path;
		$full_path	= str_replace('/', DS , $full_path); // For Windows compatibility
		if ($absolute)
			$full_path = APPLICATION_PATH . DS . $full_path;
		return $full_path;
	} 
}