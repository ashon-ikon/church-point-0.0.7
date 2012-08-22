<?php
/*-------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_name
 * Created by ashon on Jan 22, 2012
 * (c) 2010 - 2012 Copyright
 * -------------------------------------------
 */
class Point_Object_Mail extends Zend_Mail
{
	protected 	$_transport = null;
	
	function __construct()
	{
		parent::__construct();
		
		/* Setup the mailer */
		$this->setupMailer();
	}
	
	/**
	 * Throws exception on invalid to or from specified
	 */
	function sendMail($from = array() , $to, $message, $subject = '', $options = array() )
	{
		/* Ensure valid from */
		
		if (null === $from )
		{
			$from[0]  = Point_Model_User::getInstance()->email;
			$from[1]  = Point_Model_User::getInstance()->fullname();
			
			if (empty($from))
				throw new Exception('Failed: Invalid \'from\' specified.');
		}
		
		
		if (is_array($from) && count($from) > 1)
		{
			if (!$this->_validate($from[0]))
				throw new Exception('Failed: Invalid \'from\' email specified.');
			$this->setFrom($from[0], $from[1]);
		}
		
		/* Handle the to fields */
			
		if(is_array($to))
		{
			foreach ($to as $this_recepient)
			{
				if($this->_validate($this_recepient))
				{
					if (is_array($this_recepient) && count($this_recepient) > 1)
						$this->addTo($this_recepient[0], $this_recepient[1]);
					else
						$this->addTo($this_recepient);
				}
			}
		}
		else 
			throw new Exception('Invalid to format!');
		
		/* Handle the subject field */
		$this->setSubject($subject);
		
		/* Send the mail (HTML) */
		$message = wordwrap($message, 70);
		$this->setBodyHtml($message);
		
		try{
			$result = $this->send();
		}catch(Exception $e)
		{
			// iterate through rcpt exceptions
			$transport = $this->_transport;
			$msg = $e->getMessage() ;
			
			return array(false, $msg);
		}
//		// iterate through rcpt exceptions
//		$transport = $this->_transport;
//		$msg = null;
		
//		$exceptions       = $transport->getConnection()->getRcptExceptions();
//		if (count($exceptions) > 0)
//		{
//			foreach ($transport->getConnection()->getRcptExceptions() as $key => $exception) 
//			{
//				$msg .= sprintf('Failed to send to %s - server responded "%s"', $key, $exception->getMessage()).  '<br />' ;
//			}
//			
//			// get list of failed recipients
//			
////			$failedRecipients = array_keys($exceptions);
//			
//			return array(false, $msg);
//		}
		$msg = 'Mail sent successfully';
		return array(true, $msg);
	}
	
	protected function setupMailer()
	{
		$g_config 	= Zend_Registry::get('config')->toArray();
		
		$mailOptions= $g_config['app']['mail']['server'];
		
		$config = array();
		if(null != $mailOptions['port'])
			$config['port'] = $mailOptions['port'];

		if(null != $mailOptions['username'])
			$config['username'] = $mailOptions['username'];
		
		if(null != $mailOptions['password'])
			$config['password'] = $mailOptions['password'];
		
		$config['host'] 	= $mailOptions['address'];
		$config['smtp_auth'] 	= true;
		$config['auth'] 		= 'login';
		$config['smtpport'] 	= $mailOptions['port'];
		$config['host'] 		= $mailOptions['address'];
		$config['throwRcptExceptions'] = false;
		$config['pipelining'] 	= true;
//		echo '<pre>', print_r($config, true),'</pre>';exit;		
		$transport 	= new Zend_Mail_Transport_Smtp($mailOptions['address'], $config);
		
		$this->setDefaultFrom($mailOptions['email'], $mailOptions['emailname']);
		$this->setDefaultReplyTo($mailOptions['replytoemail'],$mailOptions['replytoname']);
		$this->setDefaultTransport($transport);
		 	
		$this->_transport = $transport; 	
	}
	
	protected function _validate($email)
	{
		$validator = new Zend_Validate_EmailAddress();
		
		return $validator->isValid($email);
	}
	
}