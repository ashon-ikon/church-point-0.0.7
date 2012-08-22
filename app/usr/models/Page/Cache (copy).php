<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 23, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Model_Page_Cache 
{
	protected 	$_cacheDir	= null; 
	
	protected 	$_page_age	= 7200; // 2hours
	
	protected static $instance 	= null;
	
	/**
	 *  @var bool Whether or not to disable caching
     */
    public static $doNotCache = false;

    /**
     * @var Zend_Cache_Frontend
     */
    public $cache;

    /**
     * @var string Cache key
     */
    public $key;

    /**
     * Constructor: initialize cache
     * 
     * @param  array|Zend_Config $options 
     * @return void
     * @throws Exception
     */
    private function __construct()
    {    
    	
    }
	
	
	public function setup ($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }
        
        if (!is_array($options)) {
            throw new Exception('Invalid cache options; must be array or Zend_Config object');
        }

        if (array('frontend', 'backend', 'frontendOptions', 'backendOptions') != array_keys($options)) {
            throw new Exception('Invalid cache options provided');
        }

        $this->cache = Zend_Cache::factory(
						            $options['frontend'],
						            $options['backend'],
						            $options['frontendOptions'],
						            $options['backendOptions']
										        );
	
	}
	
	// Singleton
	private function __construct ()
	{
		$this->setCacheDir( APPLICATION_PATH . DS . 'cache');
	}
	
	public static function getInstance()
	{
		if ( null === self::$instance )
		{
			self::$instance = new self; 
		}
		
		return self::$instance;
	}
	
	
    /**
     * Start caching
     *
     * Determine if we have a cache hit. If so, return the response; else,
     * start caching.
     * 
     * @param  Zend_Controller_Request_Abstract $request 
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
    	// POST or someother sort.
        if (!$request->isGet()) {
            self::$doNotCache = true;
            return;
        }

        $path = $request->getPathInfo();

        $this->key = md5($path);
        if (false !== ($response = $this->getCache())) {
//            $response->sendResponse();
//            exit;
        }
    }
    
    public function getCache()
    {
    	return $this->cache;
    } 
    
    /**
     *  Cache shall be able to read / write 
	 * 
	 */
	public function fetch($cache)
	{
		$cache 		= remove_trailing_slash($cache);
		$base_dir 	= remove_trailing_slash($this->getCacheDir());
		$requested_filename = $base_dir . DS . md5($cache) . '_'. '.phtml';
		
		// Check if file exists
		if (readable($requested_filename))
		{
			// Check age of file
			if ( ( time() - filemtime($requested_filename)) < $this->_page_age){
				return file_get_contents($requested_filename);
			}
			else
			{	// file is too old; delete it
				unlink ($requested_filename);
			}  
		}
		
		echo ( $requested_filename);
		return false;
	}
	
}