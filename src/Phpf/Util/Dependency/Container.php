<?php

namespace Phpf\Util\Dependency;

use Exception;
use Closure;

class Container {
	
	protected $closures;
	
	protected $objects;
	
	protected $singletons;
	
	protected $singletonIds;
	
	protected static $_instance;
	
	public static function i(){
		if ( ! isset(self::$_instance) )
			self::$_instance = new self();
		return self::$_instance;
	}
	
	protected function __construct(){
		$this->closures = array();
		$this->objects = array();
		$this->singletons = array();
		$this->singletonIds = array();
	}
	
	public function set( $id, $value, $asSingleton = false ){
		
		if ( !is_object($value) ){
			throw new Exception("Must pass closure or object as value to set() - " . gettype($value) . " given.");
		}
		
		if ( $value instanceof Closure ){
			$this->closures[$id] = $value;
		} elseif ( $asSingleton ){
			$this->singletons[$id] = $value;
		} else {
			$this->objects[$id] = $value;
		}
		
		if ( $asSingleton ){
			$this->singletonIds[] = $id;
		}
	}
	
	public function get( $id, $args = array(), $asSingleton = false ){
		
		if ( $asSingleton ){
				
			if ( ! isset($this->singletons[$id]) ){
					
				if ( ! isset($this->closures[$id]) ){
					throw new Exception("Unknown singleton $id");
					return null;
				}
				
				$this->singletons[$id] = call_user_func_array($this->closures[$id], (array) $args);
			}
			
			return $this->singletons[$id];
		}
		
		if ( isset($this->objects[$id]) )
			return $this->objects[$id];
		
		if ( isset($this->closures[$id]) )
			return call_user_func_array($this->closures[$id], (array) $args);
		
		throw new Exception("Unknown resource $id.");
	}
	
	public function setSingleton( $id, $value ){
		$this->set($id, $value, true);
	}
	
	public function getSingleton($id, $args = array()){
		return $this->get($id, $args, true);
	}
	
	public function singleton( $id, $args = array() ){
		return $this->get($id, $args, true);
	}
	
	public function singletonExists( $id ){
		return in_array($id, $this->singletonIds);
	}

	public function instanceExists( $id ){
			
		if ( $this->singletonExists($id) )
			return isset($this->singletons[$id]);
		
		return isset($this->objects[$id]);
	}
}
