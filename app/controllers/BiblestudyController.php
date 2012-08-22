<?php

class BiblestudyController extends Point_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$this->_forward('edevotional');
        $this->view->content = "Some nice value";
    }
    
    public function edevotionalAction()
    {
    	/**/
    	$devotion['devotion_author']	= null;
    	$devotion['devotion_date']		= null;
    									
    	$feedUrl = "http://odb.org/feed/";
    	
    	$feedContent = "";
    	
    	// Fetch feed from URL
    	$curl = curl_init();
    	curl_setopt($curl, CURLOPT_URL, $feedUrl);
    	//curl_setopt($curl, CURLOPT_TIMEOUT, 3);
    	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($curl, CURLOPT_HEADER, false);
    	
    	// FeedBurner requires a proper USER-AGENT...
    	//curl_setopt($curl, CURL_HTTP_VERSION_1_1, true);
    	curl_setopt($curl, CURLOPT_ENCODING, "gzip, deflate");
    	curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:13.0) Gecko/20100101 Firefox/13.0");
    	//curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3");
    	
    	$feedContent = curl_exec($curl);
    	curl_close($curl);
    	// Did we get feed content?
    	if($feedContent && !empty($feedContent))
	    	$feedXml = @simplexml_load_string($feedContent);
    	if($feedXml)
    	{
    		$latest			=	$feedXml->channel->item[0];
    		$pub_date		= 	$latest->pubDate;
    		$latest_url		= 	$latest->link;
    		$devotionContent	= 	@file_get_contents($latest_url);
    		if ($devotionContent)
    		{
    			$devotionContentXml = @simplexml_load_string($devotionContent);
    		}
    		echo $latest_url, '<br />', $pub_date; 
    		$this->view->feedContent= $feedContent;	
    	}
    									
//    	$this->view->devotion	= $devotion;
    	
    }

}

