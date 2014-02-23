<?php
/**
 * PSR-0 autoloader.
 *
 * @package Phpf.Util
 * @subpackage Autoloader
 */

namespace Phpf\Util;
 
class Autoloader {
	
	public $namespace;
	
	public $path;
	
	public $namespaceSeparator = '\\';
	
	public $isRegistered = false;
	
	protected $namespaceStrlen;
	
	protected static $_instances = array();
	
	public static function instance( $namespace, $path = null ){
		if ( ! isset(self::$_instances[ $namespace ]) )
			self::$_instances[ $namespace ] = new self( $namespace, $path );
		return self::$_instances[ $namespace ];
	}
	
	function __construct( $namespace, $path = null ){
		$this->setNamespace($namespace);
		$this->setPath($path);
	}
	
	public function setPath( $dirpath ){
		$this->path = rtrim($dirpath, '/\\');
		return $this;
	}
		
	public function setNamespace( $namespace ){
		$this->namespace = ltrim($namespace, '\\_');
		$this->namespaceStrlen = strlen($this->namespace);
		return $this;
	}
	
	public function setNamespaceSeparator( $sep ){
		$this->namespaceSeparator = $sep;
		return $this;
	}
	
	public function isRegistered(){
		return $this->isRegistered;
	}
		
	/**
	* Finds and loads a class (or interface or trait).
	*/
	public function load( $class ){
		
		$class = ltrim( $class, '\\' );
		
		if ( 0 !== stripos($class, $this->namespace) ){
			return;
		}
		
		$file = '';
		$fullNs = '';
		
		if ( $lastNsPos = strrpos($class, $this->namespaceSeparator) ) {
	    		
	    	$fullNs = substr($class, 0, $lastNsPos);
	    	$class = substr($class, $lastNsPos+1);
	    
	    	$file = str_replace($this->namespaceSeparator, DIRECTORY_SEPARATOR, $fullNs) . DIRECTORY_SEPARATOR;
	    }
		
		$file .= str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
		
		include $this->path . DIRECTORY_SEPARATOR . $file;
	}
	
	public function register(){
		spl_autoload_register( array($this, 'load') );
		$this->isRegistered = true;
		return $this;
	}
	
	public function unregister(){
		spl_autoload_unregister( array($this, 'load') );
		$this->isRegistered = false;
		return $this;
	}
	
	public function getInstances(){
		return self::$_instances;
	}	
}
