<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Feb 18, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Model_Agent_ImageSliderAgent implements Point_Model_Agent_Interface
{
	/**
	 * 	Creates content
	 * 
	 * 	All agents must implement this.
	 */
	public function makeContent( $params = null)
	{
		$this->_prepareHeaders();
		
		return $this->_makeHtml();		
	}
	
	protected function _prepareHeaders()
	{
			if (!defined('NIVOSLIDER_DEFINED'))
		{
			$viewRenderer 		= Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
	    	if (!$viewObject 	= $viewRenderer->view)
	    	{
		    	throw new Exception ('Call to empty view!');
	    	}
	    	/* add the script to layout script */
	    	$base_url			= $viewObject->fullUrl(array('controller'=>'index', 'action' => 'index'), null, false);
	    	$script_location 	= $base_url . 'js/jquery.nivo.slider-3.0.pack.js';
	    	$viewObject->headLink()->appendStylesheet($viewObject->fullBaseUrl() 	.'/css/nivo-slider.css');
	    	$viewObject->headLink()->appendStylesheet($viewObject->fullBaseUrl() 	.'/css/nivo-ashon-theme.css');
	    	$viewObject->headScript()->appendFile( $script_location , 'text/javascript');
			define('NIVOSLIDER_DEFINED' , 1100 );	
		}
	}
	
	protected	function _makeHtml()
	{
	
		$slider_obj				= Point_Model_Slider::getInstance();
		$topSlides				= $slider_obj->getTopSlides();
//		echo '<pre>'.print_r($topSliders, true).'</pre>'; exit;
		$content				= null;
		$contentHtmlCaptions	= null;
		$fullBase_url			= $this->_getFullBaseUrl();
		
		// Generate the content
		foreach ($topSlides as $key => $slide)
		{	
			if ($slide['slide_active'] == Point_Model_Slider::SLIDER_ACTIVE_INACTIVE)
				continue; // SKIP if slide is marked inactive
				
			// If we have html caption then make it HTML id and create div for it.
			$html_caption		= $caption_id	= null;
			if ($slide['slide_html'] != '')
			{
				$html_caption	= $slide['slide_html'];
				$caption_id		= 'caption'. substr($slide['slide_pic_location'], 0 , 5) . $key;
				
				// Append HTML caption
				$contentHtmlCaptions .= wrapHtml($html_caption, 'div',array('id' => $caption_id, 'class' => 'nivo-html-caption'));
			}
			// Create the img tag with the details
			$img_src		= $fullBase_url  . $slide['img_location'];
			$img_title		= $html_caption === null ?	$slide['slide_pic_title'] : '#' . $caption_id;
			$slide_img_tag	= wrapHtml(null, 'img', array('src' => $img_src, 'title' => $img_title), true); // Single tag
			
			// if $slide['slide_pic_link'] is '' then it's not a link
			if ($slide['slide_pic_link'] != '')
			{
				$slide_img_tag	= wrapHtml($slide_img_tag, 'a', array('href' => $slide['slide_pic_link'], 'title' => $slide['slide_pic_title']));
			}
			
			// Add it to content
			$content		.= $slide_img_tag;
		}
		
		// Join the img(s) & link(s) content with the contentHtmlCaptions

		$sliderContainer	= wrapHtml($content,'div', array('id' => 'top-slider', 'class' => 'nivoSlider'));
		$content			= $sliderContainer . $contentHtmlCaptions . $this->_getSliderJSScript();
		$viewOptions		= array('class' => 'theme-ashon');
		$ret				= wrapHtml(wrapHtml($content, 'div',$viewOptions),'div', array('class' => 'imagesliderbox'));
		$ret				= wrapHtml($ret,'div', array('id' => 'imgslider-top', 'class' => 'section'));
		
		return $ret;
	}
	
	protected function _getSliderJSScript()
	{
		$scriptOptions	= array('type' => 'text/javascript');
		$scriptContent	= '$(function(){ $(\'#top-slider\').nivoSlider({manualAdvance: false}); });';
		return wrapHtml($scriptContent,'script', $scriptOptions);
	}
	
	protected function _getFullBaseUrl()
	{
		$viewRenderer 		= Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		if (!$viewObject 	= $viewRenderer->view)
		{
			throw new Exception ('Call to empty view!');
		}
		return $viewObject->fullUrl(array('controller'=>'index', 'action' => 'index'), null, false);
	    	
	}
}