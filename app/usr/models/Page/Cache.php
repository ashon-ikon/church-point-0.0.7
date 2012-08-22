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
	/**
	 *  @var bool Whether or not to disable caching
     */
    protected static $_noCache = false;

    /**
     * @var Zend_Cache_Frontend
     */
    protected 	$_cache	= null;

    /**
     * @var string Cache key
     */
    private 	$_key;
	
	protected 	$_cacheDir	= null; 
	
	protected static $instance 	= null;
    /**
     * Prevent further cloning
     * ----------------------
     */
    private function __construct()
    {    
    	
    }
	
	/**
     * Setup: initialize cache
     * 
     * @param  array|Zend_Config $options 
     * @return void
     * @throws Exception
     */
	public function setup ( $options )
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }
        
        if (!is_array( $options )) {
            throw new Exception('Invalid cache options; must be array or Zend_Config object');
        }

        if (array('frontend', 'backend', 'frontendOptions', 'backendOptions') != array_keys( $options )) {
            throw new Exception('Invalid cache options provided');
        }

        $this->_cache = Zend_Cache::factory(
						            $options['frontend'],
						            $options['backend'],
						            $options['frontendOptions'],
						            $options['backendOptions']
										        );
		return $this;
	}
	
	public static function getInstance()
	{
		if ( null === self::$instance )
		{
			self::$instance = new self; 
		}
		
		return self::$instance;
	}
    
    public function getCache()
    {
    	// Ensure we have a valid cache
    	if ( $this->_cache instanceof Zend_Config)
    	{
 			throw new Exception('Invalid cache object');   		
    	}
    	return $this->_cache;
    } 
    
    
	public function fetch($request, $noCache = false)
	{
		$content = null;
		
		if (self::$_noCache != $noCache)
			self::$_noCache = $noCache;
					
		if (!self::$_noCache)
			$content =  $this->getCache()->load($request);
			
		return $content;
	}
	
}