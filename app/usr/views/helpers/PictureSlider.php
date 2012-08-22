<?php
/* @FILENAME: AppConfig.php
 * @PROJECT: Church Point
 * @PACKAGE: ChurchPoint 
 * Created by ashon on Sep 11, 2011
 * @COPYRIGHTS: (c) Copyright 2010 - 2011
 * 
 * 
 */
class Point_View_Helper_PictureSlider extends Zend_Db_Table_Abstract{
	/**
	 * @var $_front
	 * 
	 * Handle to Front Controller
	 */
	protected	$_front;
	
	/**
	 * @var	$_name database table name
	 */
	protected	$_name = 'pic_slider_table';
	/**
	 * entry point to PictureSlider
	 * @var numeric index of pictures
	 */
//	public function __construct()
//	{
//		$this->_front	= Zend_Controller_Front::getInstance();
////    	$this->_request = $front->getRequest();
////       	$viewRenderer	= Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
////       	$viewRenderer->setResponseSegment($this->_request->getActionName());
//	}
	public function PictureSlider(Zend_View $viewObj, $index = null, $tableName = null)
	{
		// Include the necessary  JScript and CSS files...
		$baseUrl = $viewObj->baseUrl();

		$viewObj->headLink()->appendStylesheet($baseUrl .'/css/nivo-sliderX.css');
		$viewObj->headScript()->appendFile( $baseUrl.'/js/jquery.nivo.slider.pack.js' , 'text/javascript');
		
		// Read list of images from database...
		
		if (null != $tableName && is_string($tableName)){ // we were passed a table name
		    $this->_name = $tableName;
        }
        $sliderCollections = $this->fetchAll($this->select()->where('slider_id = ?', $index));
        
        // Show the pictures
        $content = '<!--slider--><div class="slider-wrapper theme-default">'.
							'<!-- slider from http://nivo.dev7studios.com -->'.
							'<div id="slider" class="nivoSlider">';
		// Building the links...
		foreach ($sliderCollections as $sliderCollection)
        {
        	$image = 	'<img src="'		. $baseUrl .$viewObj->escape($sliderCollection['pic_location']) .'" '.
						'alt="'		. $baseUrl .$viewObj->escape($sliderCollection['pic_alt']) 		.'" '.
						'title="'	. $baseUrl .$viewObj->escape($sliderCollection['pic_title']) 	.'" />';
        	$content .= (empty($sliderCollection['pic_link'])?
							  $image :
							  // Embed the image in link
							  '<a href="' . $baseUrl . $viewObj->escape($sliderCollection['pic_link']) 	. '">'. $image .'</a>'
						);
        }
        $content 	.=	'</div>
							<div id="4link1" class="nivo-html-caption">
								<span>A special dedication of the <a href="#">Permai Methodist Santuary</a>.</span>
							</div></div><!-- End Slider -->';
		/**
		 * 
								<a href="#"><img src="<?= $baseUrl; ?>images/slides/slide1.jpg" alt="" title="The ladies singing at the sanctuary dedication"/></a>
								<a href="#"><img src="<?= $baseUrl; ?>images/slides/slide2.jpg" alt="" title="Sister Jia Yun leading praises..." /></a>
								<img src="<?= $baseUrl; ?>images/slides/slide9.jpg" alt="" title="Emily and friends ..." />								
								<img src="<?= $baseUrl; ?>images/slides/slide3.jpg" alt="" title="Happy Hour..."/>
								<img src="<?= $baseUrl; ?>images/slides/slide4.jpg" alt="" title="#4link1" />
								<img src="<?= $baseUrl; ?>images/slides/slide5.jpg" alt="" title="Pastor O. S. Pung" />
								<img src="<?= $baseUrl; ?>images/slides/slide6.jpg" alt="" title="Singing for Tin Hou" />
								<img src="<?= $baseUrl; ?>images/slides/slide7.jpg" alt="" />
							
							<script type="text/javascript" rel="javascript" src="<?= $baseUrl; ?>js/jquery.nivo.slider.pack.js"></script>*/
		$content	.= '<script type="text/javascript">' .
					   '$(window).load(function() {'.
					   '$(\'#slider\').nivoSlider();});'.
						'</script>';
//						<script type="text/javascript">
//								$(window).load(function() {
//
//									$('#slider').nivoSlider();
//								});
//							</script>	
		return $content;
	}
}