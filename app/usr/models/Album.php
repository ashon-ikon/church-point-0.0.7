<?php
/*--------------------------------------------
 * @project:	ChurchPoint
 * @package:	package_declaration
 * Created by ashon on Nov 19, 2011
 * (c) 2010 - 2011 Copyright
 * -------------------------------------------
 */
class Point_Model_Album extends Zend_Db_Table_Abstract
{
	protected	$_name = 'albums_table';
	
	/**
	 * This creates new album
	 * throws Exception
	 * @return boolean
	 */
	public function createAlbum($album_name, $album_desc = null)
	{
		/* Ensure we have a valid album name */
		if (!is_string($album_name))
		{
			throw new Exception('Invalid album name given');
		}
		
		// Create a new folder
		$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$root_folder = $bootstrap->getOption('album_folder');
		$root_folder = realpath($root_folder);
		if ( !writable($root_folder) )
			throw new Exception('Cannot write to folder directory!');
		$album_folder = $root_folder . DS . strtolower(cleanReplace($album_name,'-'));
		
		mkdir($album_folder, 0755, true);
		
		//	album_id	album_name	album_desc	album_uri	user_id	modification_date	creation_date
		$newAlbum	  = $this->createRow();
		$newAlbum->album_name = $album_name;
		$newAlbum->album_desc = $album_desc;
		$newAlbum->album_uri = $album_folder;
		$newAlbum->user_id= Point_Model_User::getInstance()->getUserId();
		$newAlbum->modification_date = new Zend_Db_Expr('NOW()');
		$newAlbum->creation_date = new Zend_Db_Expr('NOW()');
		$newAlbum->save();
		
		return $this;
	}
	
	private function createNewAlbumForm()
	{
		$form = new Zend_Form();
		
		
	}
	
	/**
	 * This gets the album
	 * @param $pointer: numeric id | string name of album
	 */
	public function getAlbum($pointer)
	{
		
	}
	
	/**
	 * 
	 */
	
}