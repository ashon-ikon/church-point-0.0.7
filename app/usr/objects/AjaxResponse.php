<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 10, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Object_AjaxResponse extends Zend_Controller_Response_Http
{
	protected	$_xml = null;
	
	
	public function __construct($options = null)
	{
		
		/**
		 * Set the headers..
		 */
		$this->setHeader('Content-Type', 'text/xml; charset=utf-8')
			 ->setHeader('Cache-Control',
                               'no-cache, no-store, max-age=0, must-revalidate, post-check=0, pre-check=0')
             ->setHeader('Expires', 'Tue, 14 Aug 1997 10:00:35 GMT');
		
		$xml = new DOMDocument();
		// Create the parents...
		$root = $xml->createElement('ajax-response');
		
		$response 	= $xml->createElement('response');
		$status		= $xml->createElement('status');
		$status->appendChild($xml->createElement('error') );
		$status->appendChild($xml->createElement('message'));
		
		$response->appendChild($status);
		$response->appendChild($xml->createElement('content'));
		
		$root->appendChild($response);
        $xml->appendChild($root);
		
/*		
				$status->appendChild($xml->createElement('message', $msg));
				$status->appendChild($xml->createElement('error', $err_no));
        // Set message body and end application
        $this->getResponse()->setHeader('Content-Type', 'text/xml; charset=utf-8')
        					->setBody($xml->saveXML())
        					->sendResponse();	
*/
		$this->_xml = $xml;
	}
	
	public function postContent($content, $error = null , $err_no = null)
	{
//		file_put_contents(APPLICATION_PATH . '/test.log',$this->_xml->saveXml(), FILE_APPEND);
//		file_put_contents('/home/ashon/something',null);
		// Get the contents section
		$xml = $this->_xml;
		// If errors exists show them too.
		$errorElem		= $xml->getElementsByTagName('message')->item(0);
		$errorElem->appendChild($xml->createTextNode($error)); 
		
		$error_noElem		= $xml->getElementsByTagName('error')->item(0);
		$error_noElem->appendChild($xml->createTextNode($err_no));
		
		$contentElem 	= $xml->getElementsByTagName('content')->item(0);
		$contentElem->appendChild($xml->createTextNode($content));
		
		// Set message body and end application
        Zend_Controller_Front::getInstance()->getResponse()->setHeader('Content-Type', 'text/xml; charset=utf-8')
        					->setBody($this->_xml->saveXml())
        					->sendResponse();
//        file_put_contents(APPLICATION_PATH . '/test.log',"\n".print_r($this->_xml->saveXml(), true), FILE_APPEND);	
       	exit; // Explicit leaving
		return;
	}
	
	public function postError($error, $err_no = null)
	{
		// Get the contents section
		$xml = $this->_xml;
		// If errors exists show them too.
		$errorElem		= $xml->getElementsByTagName('message')->item(0);
		$errorElem->appendChild($xml->createTextNode($error)); 
		
		$error_noElem		= $xml->getElementsByTagName('error')->item(0);
		$error_noElem->appendChild($xml->createTextNode($err_no));

		
		Zend_Controller_Front::getInstance()->getResponse()->setHeader('Content-Type', 'text/xml; charset=utf-8')
        					->setBody($this->_xml->saveXml())
        					->sendResponse();
        exit;					
        return ;
	}
	
	public function getAjaxResponse()
	{
		if (null !== $this->_xml)
		{
			return $this->_xml->saveXml();
		}
	}
	
}
