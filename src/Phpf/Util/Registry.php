<?php
/**
* @package Phpf.Util
*/

namespace Phpf\Util;

class Registry {
	
	protected static $data = array();
	
	/**
	 * Sets an object by dot-notated path.
	 * Each dot ('.') indicates a child array dimension.
	 */
	public static function set( $dotpath, $object ){
		
		if ( false === strpos($dotpath, '.') ){
			return self::$data[$dotpath] = $object;
		}
		
		return Arr::dotSet(self::$data, $dotpath, $object);
	}
	
	/**
	 * Returns a registered object by its dot-notated path.
	 */
	public static function get( $dotpath ){
		
		if ( false === strpos($dotpath, '.') ){
			return isset(self::$data[$dotpath]) ? self::$data[$dotpath] : null;
		}
		
		return Arr::dotGet(self::$data, $dotpath);
	}
	
	/**
	 * Returns true if a object exists, given by its dot-notated path.
	 */
	public static function exists( $dotpath ){
		return (bool) self::get($dotpath);
	}
	
	/**
	 * Returns all registered objects.
	 * Mostly for debugging.
	 */
	public static function getAll(){
		return self::$data;	
	}
	
	/**
	 * Adds an object to a group.
	 * 
	 * Optionally specify a $uid to access it by such (Default: classname).
	 * 
	 * This is basically identical to: Registry::set( '{{group}}.{{uid}}', $object );
	 */
	public static function addToGroup( $group, $object, $uid = null ){
			
		if ( empty($uid) ) 
			$uid = get_class($object);
			
		self::$data[$group][$uid] = $object;		
	}
	
	/**
	 * Returns an object by uid/class in a particular group.
	 *
	 * Same as: Registry::get( '{{group}}.{{uid}}' );
	 */
	public static function getFromGroup( $group, $uid ){
		return isset(self::$data[$group][$uid]) ? self::$data[$group][$uid] : null;
	}
	
	/**
	 * Returns array of objects registered to a particular group.
	 */
	public static function getGroup( $group ){
		return self::$data[$group];
	}
	
}
