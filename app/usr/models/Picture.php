<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Oct 12, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Model_Picture extends Point_Model_BaseClass
{
	/**
	 * 
	 */
	protected	$_allowed_ext 	   		= 'jpg, gif, png, bmp, jpeg, JPG';

	/**
	 * 
	 */
	protected	$_thumbnail_prefix 	   	= APP_IMAGES_THUMB_PREFIX;
	
	/**
	 * 
	 */
	protected	$_thumbnail_width 	   	= APP_IMAGES_THUMB_WIDTH;
	
	/**
	 * 
	 */
	protected	$_thumbnail_height 	   	= APP_IMAGES_THUMB_HEIGHT;
	
	/**
	 * defined('APP_MEDIA_DIRECTORY') ||
			define ('APP_MEDIA_DIRECTORY', $options['root'] ); 

		defined('APP_IMAGES_DIRECTORY') ||
			define ('APP_IMAGES_DIRECTORY', $options['images'] ); 

		defined('APP_IMAGES_THUMB_DIRECTORY') ||
			define ('APP_IMAGES_THUMB_DIRECTORY', $options['imagesthumb'] ); 

		defined('APP_PROFILE_IMAGES_DIRECTORY') ||
			define ('APP_PROFILE_IMAGES_DIRECTORY', $options['profileimages'] ); 	
	 */
	 
	/**
	 * @var directory all public media
	 */
	protected	$_media_directory = APP_MEDIA_DIRECTORY;
	 
	/**
	 * @var directory to images
	 */
	protected	$_images_directory = APP_IMAGES_DIRECTORY;
	 
	/**
	 * @var directory to thumbnails
	 */
	protected	$_images_thumb_directory = APP_IMAGES_THUMB_DIRECTORY;
	/**
	 * Singleton Instance
	 * @var self instance
	 */
	private static	$_instance;
	
	public static function getInstance()
	{
		if ( !isset(self::$_instance))
	    {
	    	self::$_instance = new self;
	    }
	    return self::$_instance;
	}
	
	/**
	 * Singleton
	 */
	private function __construct()
	{
		/* setup the default the folders */
		$this->_media_directory = realpath(APPLICATION_PATH . DS . '..' . DS . 'public'. DS . $this->_media_directory);
		$this->_images_directory = realpath(APPLICATION_PATH . DS. '..' . DS . 'public'. DS . $this->_images_directory);
			
	}
	
	public function makeThumbnail_($filename, $destination_path = null, $target_width = 50, $target_height = 50)
	{
		

		/* Set up filename and path */
		if (null === $destination_path)
		{	// Use default directory...
			$destination_path	= APPLICATION_PATH . DIRECTORY_SEPARATOR . '../public' . DIRECTORY_SEPARATOR . 'media/photos/thumb';
		}
		if (!file_exists($destination_path) && !is_readable($destination_path))
		{ throw new Exception('Invalid destination_path'); 	}
		
	}

	
	/**
	 * This function takes the source image and saves it in destination folder
	 * 
	 * <notice>
	 * !!!The 'destination_path' is either given or 'album_id'
	 * 
	 * @param array(
	 * 				source 			=> String source
	 * 				destination 	=> String destination filename 
	 * 				destination_path=> String destination path 
	 * 				description 	=> String Image description 
	 * 				album_id 		=> Integer album id 
	 * 				dest_path		=> Integer album uri 
	 * 				keep_ext		=> Boolean Retain image file extension 
	 * 				store_in_db		=> Boolean Whether to store inside database 
	 * @param Array new size | array()
	 * throws Exception
	 * 
	 * @return array(boolean , $msg)
	 */
	public function saveImage($data, $newsize = array() )
	{
		$errors = array(); // For holding errors
		$src 	= $dest = $album_id = $description = $keep_ext = null;

		$user 	= Point_Model_User::getInstance();

		if(!$user->isLoggedIn())
		{
			$msg	= 'Failed: Please login to add image.';
			return array(false, $msg);
		}

		/* Extract the parameters */
		$src		=	getArrayVar($data, 'source', 		null);
		$dest		=	getArrayVar($data, 'destination', 	null);
		$album_id	=	getArrayVar($data, 'album_id', 		null);
		$dest_path	=	getArrayVar($data, 'dest_path', 	null);
		$description=	getArrayVar($data, 'description', 	null);
		$keep_ext	=	getArrayVar($data, 'keep_ext', 		false);
		$skip_db	=	getArrayVar($data, 'skip_db',		false);
		
//		echo '<pre>', print_r($data, true), '</pre>'; exit;
//		echo $store_in_db ? 'True' : 'False'; exit;
			
			
		/* Check if src is readable*/
		if (!readable($src))
			throw new Exception('Failed: Source folder not readable!<pre>'.$src.'</pre>');
			
		/* Check if dest is readable*/
		if (!is_dir($dest) && is_file($dest))
		{
			if (!writable(dirname($dest)) )
				throw new Exception('Failed: Destination folder not writable!<pre>'.dirname($dest).'</pre>');
		}
		else if (is_dir($dest))
		{
			if (!writable($dest) )
				throw new Exception('Failed: Destination folder not writable!<pre>'.$dest.'</pre>');
		}
		
		/* Get allowed extensions */
		$allowed_paths = explode( ', ' , $this->_allowed_ext);
		
		/* Get the image extension */ 
		$extension = pathinfo($src);
		if (!array_key_exists('extension', $extension))
		{
			$msg	= 'Failed: No known extension to use.';
			return array(false, $msg);
		}
		
		/* Let's get the album info */
		if (null === $dest_path && is_numeric($album_id ))
		{
			$album_table = new Zend_Db_Table('albums_table');
			$album_info  = $album_table->select()->where('album_id = ? ', $album_id )->limit(1)->query()->fetch();
			if (empty($album_info))
			{
				$msg	= 'Failed: Unable to fetch album.';
				return array(false, $msg);
			}
			$dest_path = remove_trailing_slash($this->_images_directory) . DS . $album_info['album_uri'];
		}
		
		if (null === $dest_path)
		{
			throw new Exception('Failed: Couldn\'t get suitable album.');
		}
		
		$extension = strtolower($extension['extension']);
			
		if(in_array($extension, $allowed_paths))
		{ 
			/* Get source image height x width */
			list($src_width, $src_height, $src_type, $src_attr) = @getimagesize($src);
			
			$new_height = $src_height;
			$new_width 	= $src_width;
			
			/* Check if we are to resize the image */
			if (!empty($newsize))
			{
				$dWidth = $dHeight = null;
				if(array_key_exists('width', $newsize))
					$dWidth = $newsize['width']; 
					
				if(array_key_exists('height', $newsize))
					$dHeight = $newsize['height']; 
				
				/* compute the ratio */
				$imgratio = ($src_width / $src_height); 
		
		        if ($imgratio	>	1)
		        { 
		          $new_width 	= $dWidth; 
		          $new_height 	= intval($dWidth / $imgratio); 
		        } else { 
		          $new_height 	= $dHeight; 
		          $new_width 	= intval($dHeight * $imgratio); 
		        }
	        
			}    	
			
			//$extension = $extension['extension'];
			$tmp_image = null;
		
			/* Try to create an image resource */
	        if($extension == 'jpg' || $extension == 'jpeg' )
	        { 
	        	$extension = 'jpg'; /* normalize the extension :D */
	        	$tmp_image = @imagecreatefromjpeg($src); 
	        } 
	
	        if($extension == 'png') 
	        { 
				$tmp_image = @imagecreatefrompng($src); 
	        } 
	
	        if($extension == 'gif') 
	        { 
				$tmp_image = @imagecreatefromgif($src); 
	        }
	    	
	    	if (!is_resource($tmp_image))
	    	{
				$msg	= 'Failed to load image.';
				return array(false, $msg);
			}
	    
			/* Create the destination image */
			 $new_image = imagecreatetruecolor($new_width,$new_height);
	        if (!is_resource($new_image))
	    	{
				$msg	= 'Failed while creating image.';
				return array(false, $msg);
			}
	               
	        @ImageCopyResized($new_image, $tmp_image,0,0,0,0, $new_width, $new_height, $src_width, $src_height); 
	         
	        /* save the image accordingly */
	        $src_info 		= pathinfo($src);
	        
	        /* Determine which extension to save image */
	        $dest_ext 		= $extension; 
	        if (!$keep_ext)
	        	$dest_ext 	= 'jpg';
	        
	        /* use the destination filename OR some random number */
	        $img_filename	= $dest != null ? $dest : genRandomNumString(6) . '_' . genRandomNumString(6);
	        
	        $final_dest = remove_trailing_slash($dest_path) . DS . $img_filename;
	        $final_dest .= '.' . $dest_ext;
	        
//	        echo 'image filename: ',$img_filename, '<br />Destination File: ', $dest_path, '<br />Final Dest: ', $final_dest; exit;
	     
	        /* Check if dest is writable */
	        if (!writable( dirname($final_dest) ) )
	        {
	        	/* Attempt to create final destination possibly */
	        	try{
	        		@mkdir(dirname($final_dest), 0777, true) ;
	        	}
			    catch(Exception $e)
				{
				   throw new Exception('Failed: Destination album folder not writable!<pre>'.dirname($final_dest).'</pre>' . $e);
				}
	        }
	        
	        try{
		        switch ( $dest_ext ) {
			        case 'jpg':	
			        {
				        
				        @imagejpeg( $new_image, $final_dest );			
			        }					
			        break;
			        
			        case 'png':	
			        {
				        @imagepng( $new_image, $final_dest );
			        }					
			        break;
			        
			        case 'gif':	
			        {
				        @imagegif( $new_image, $final_dest );
			        }					
			        break;
			        
			        default:
			        {	
				        @imagejpeg( $new_image, $final_dest );
			        }
			        break;
		        }
	        }
	        catch(Exception $e)
			{
		        throw new Exception ('Failed to create new image' . $e);
			}
	     
	        /* Make thumnails */
	        if (!$thumbnail_fname = $this->makeThumbnail($final_dest))
	        {
		        $msg	= 'Failed to make thumbnail.';
		        return array(false, $msg);
	        }

	        $thumb_rel_pathname = substr($thumbnail_fname , strlen($this->_images_directory) + 1);
	        $image_rel_pathname = substr($final_dest , strlen($this->_images_directory) + 1);
	        
	        if (!$skip_db && null !== $album_id)
	        {
		        /* let's store everything in database */
		        $images_table 	= new Zend_Db_Table('images_table');
		        $new_image_data	= array(
			        'album_id' 					=> $album_id,
					'user_id' 					=> $user->getUserId(),
					'image_desc' 				=> $description,
					'image_rel_path' 			=> $image_rel_pathname,
					'image_rel_thumbnail_path' 	=> $thumb_rel_pathname,
					'add_date' 					=> new Zend_Db_Expr('NOW()')
		        );
		        /* Store it inside DB */
		        if (false === $images_table->insert($new_image_data))
		        {
			        /* TODO: Remove image & thumbnail */
			        throw new Exception ('Failed: Unable to new store image info inside db');
		        }
	        }
	        
	        
	        /* Free the resources */
	        @ImageDestroy($new_image); 
	        @ImageDestroy($tmp_image);
	        
	       
	        
		}
		else
		{
			$msg	= 'File extension not supported yet.';
			return array(false, $msg);
		}
		
		
		/* RETURN HAPPY */
		$msg	= $final_dest;
		return array(true, $msg);
		
	}
	
	public function getImagesDir()
	{
		return remove_trailing_slash($this->_images_directory). DS;
	}
	
	/**
	 * This gets the image info
	 * 
	 * @return array array($width, $height, $type, $attr)
	 */
	public function getImageInfo( $image_filename, $img_info = array() )
	{
		return @getimagesize($image_filename, $img_info);
	}
	
	/**
	 * This gets the resized dimension for an image
	 * 
	 * @param int src_width
	 * @param int src_height
	 * @param int dest_width	[optional]
	 * @param int dest_height [optional]
	 * 
	 * @return array array($width, $height)
	 */
	public function getResizeDimension($src_width, $src_height, $dest_width = APP_IMAGES_THUMB_WIDTH, $dest_height = APP_IMAGES_THUMB_HEIGHT)
	{
		/* compute the ratio */
		$imgratio = ($src_width / $src_height); 
		
		if ($imgratio	>	1)
		{ 
			$new_width 	= $dest_width; 
			$new_height 	= intval($dest_width / $imgratio); 
		} else { 
			$new_height 	= $dest_height; 
			$new_width 	= intval($dest_height * $imgratio); 
		}
		
		return array($new_width, $new_height);
	}
	
	/**
	 * @param: path/to/image | image buffer
	 * 		   $options Array
	 * 				- width
	 * 				- destDir
	 * 				- height
	 * 				- prefix
	 * 				- realname	| bool
	 * 
	 * @return string path to image | $destDir/thumbXXXXXX.jpg
	 */
	public function makeThumbnail( $imageFilename, array $options = array())
	{
		$errors = array();
		
		// Extract the needed parameters
		if( !empty( $options ) )
		{
			foreach ( $options as $var => $value )
			$$var = $value;
		}
		if (!isset($width))
			$width = $this->_thumbnail_width;
	
		if (!isset($height))
			$height = $this->_thumbnail_height;
		
		if (!isset($prefix))
			$prefix = $this->_thumbnail_prefix;
		
		/* Get image info */
		if (!readable($imageFilename) && is_file($imageFilename))
		{
			throw new Exception ('Invalid filename specified image');
		}
		
		if (is_string($imageFilename) && !empty($imageFilename) && readable($imageFilename))
		{	
			
			list($im_width, $im_height, $type, $attr) = getimagesize($imageFilename);
			
			/* compute the ratio */
			$imgratio = ($im_width / $im_height); 
			
			if ($imgratio>1) { 
				$new_width = $width; 
				$new_height = intval($width / $imgratio); 
			} else { 
				$new_height = $height; 
				$new_width = intval($height * $imgratio); 
			}
			
			$tmp_image = $this->loadImage($imageFilename);
			
			if(is_resource($tmp_image))
			{
				$new_image = imagecreatetruecolor($new_width,$new_height);
				$image_info= pathinfo($imageFilename);       
				@ImageCopyResized($new_image, $tmp_image,0,0,0,0, $new_width, $new_height, $im_width, $im_height);
				
				 
				
				// compute new thumbnail's filename
				if (!isset($destDir))
					$destDir = realpath (	dirname($imageFilename) ) . DS . 'thumb';
					
				/* Ensure directory exists!! */
				if (!writable($destDir))
				{
					/* try to create it */
					try{
						@mkdir($destDir, 0777, true) ;
					}
					catch (Exception $e)
					{
						throw new Exception('Unable to write thumbnail to destination folder<pre>"' . $destDir .'"</pre>' . $e);
					}
				}
				
				$thumb_fname 	= $prefix . $image_info['filename']. '_'. $new_width. 'x' . $new_height . '.jpg';
				$thumb_pathname = $destDir . DS . $thumb_fname;
				
				@imagejpeg( $new_image, $thumb_pathname );  
				
				@ImageDestroy($new_image); 
				@ImageDestroy($tmp_image);
			
				/* Exit in peace */
				return $thumb_pathname;
			}
			
			if (is_resource($tmp_image))
				@ImageDestroy($tmp_image);
		}
		else
			throw new Exception ( 'Failed: Invalid filename given <pre>'.$imageFilename.'</pre>');
		
		
		return false;
	}
	
	
	/**
	 * 	Loads image from filename
	 * 
	 * 	Throws exception
	 */
	public function loadImage( $filename )
	{
		/* Ensure we have a valid filename else use own filename */
		
		if( null === $filename)
			throw new Exception ('Failed to load image');
		
		// Get the type of image
		$target_path = realpath($filename ); 
		$extensionA = pathinfo($target_path); 
		$extension = strtolower($extensionA['extension']); 
		$allowed_paths = explode( ', ' , $this->_allowed_ext);
		
		if(in_array($extensionA['extension'], $allowed_paths))
		{ 
			
			if($extension == 'jpg' || $extension == 'jpeg' || $extension == 'JPG'){ 
				$tmp_imageFilename = @imagecreatefromjpeg($target_path); 
			} 
			
			if($extension == 'png') { 
				$tmp_imageFilename = @imagecreatefrompng($target_path); 
			} 
			
			if($extension == 'gif') { 
				$tmp_imageFilename = @imagecreatefromgif($target_path); 
			}
			
			if (!is_resource($tmp_imageFilename))
				throw new Exception('Failed to load image');
				
			return $this->_imageBuffer = $tmp_imageFilename;
		}
		
    }
    
    /**
     * Adds new album
     * @param Album name albumname
     * @param Album desc albumname
     * 
     * @return array(success , msg)
     */
	public function addAlbum($albumName, $albumDesc)
	{
		$user		= Point_Model_User::getInstance();
		
		/* Ensure user is logged on	 */
		if(!$user->isLoggedIn())
		{
			$msg 	= 'Unknown user'; 
			return array(false, $msg);
		}
		
		/* Get user id */
		$user_id	= $user->getUserId();
		
		/* Ensure we have valid album name and desc */
		if (!is_string($albumName) || null == $albumName)
		{
			$msg 	= 'Invalid character specified as Album name'; 
			return array(false, $msg);
		}
		
		$albumPath		= 'albums'. DS .strtolower(substr(md5($user->getLastname()), 0, 5). DS . substr(md5(cleanReplace($albumName, '')), 0, 6) );

		$fullpath		= $this->_images_directory . DS . $albumPath;
		if (!file_exists($fullpath) && is_dir($fullpath))
		{ 
			if (!mkdir( $fullpath, 0777, true))
			{
				$msg	= 'Failed to create album directory';
				return array(false, $msg);
			}
		}
		
		if (!writable($fullpath))
		{
			$result = mkdir( $fullpath, 0777, true);
			if (false === $result)
			{
				$msg	= 'The new album directory was not successfully created.<br /><br />'. $fullpath;
				return array(false, $msg);
			}
			
		}
		/* try the addition */
		$album_table = new Zend_Db_Table('albums_table');
		
		/* Ensure such table doesn't exist yet !!*/
		$check = $album_table->select()->where('album_uri = ?' , $albumPath)->query()->fetch();
		if ($check)
		{
			$msg	= 'An album <strong>already exists</strong> with the name \''. $albumName.'\'. <br /><br />Consider using another album title.';
			return array(false, $msg);
		}
		// album_id	album_name	album_desc	album_uri	user_id	modification_date	creation_date
		
		$new_album_data = array(
							'album_name' 		=> $albumName,
							'album_desc' 		=> $albumDesc,
							'album_uri'  		=> $albumPath,
							'user_id'  			=> $user_id,
							'modification_date' => new Zend_Db_Expr('NOW()'),
							'creation_date' 	=> new Zend_Db_Expr('NOW()')
						);
						
		$insert_id = $album_table->insert($new_album_data);
		if ($insert_id === false)
		{
			$msg	= 'Failed to store album directory';
			return array(false, $msg);
		}
		
		return array(true, 'Album added successfully');
	}
	
	/**
	 * This function retrieves all images that this user has
	 */
	public function getAllUserImages($skip_profile_album = true)
	{
		$user 		= Point_Model_User::getInstance();
		if ($user->isLoggedIn())
		{
			$db			= new Zend_Db_Table('images_table');
			$db_adapter = $db->getAdapter();
			
			$result 	= $db_adapter->select()
								->from		(array('it'=> 'images_table'))
								->joinInner	(array('at'=> 'albums_table'), 'it.album_id = at.album_id')
								->where		('it.user_id = ? ', $user->getUserId() )
								->query()->fetchAll();
			
			if (!empty($result))
				return $result;
		}
			
	}
	
	
	/**
	 * This function retrieves all albums
	 */
	public function getAllUserAlbums()
	{
		$user 		= Point_Model_User::getInstance();
		if ($user->isLoggedIn())
		{
			$db			= new Zend_Db_Table('albums_table');
			
			$result 	= $db->select()->where('user_id = ? ', $user->getUserId() )
								->query()->fetchAll();
			
			if (!empty($result))
				return $result;
		}
			
	}
}