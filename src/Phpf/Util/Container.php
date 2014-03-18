<?php

namespace Phpf\Util;

use ArrayAccess;
use Countable;

class Container implements ArrayAccess, Countable, iContainer {
	
	/**
	 * Magic __set()
	 */
	public function __set($var, $val){
		$this->$var = $val;
	}
	
	/**
	 * Magic __get()
	 */
	public function __get($var){
		return $this->$var;
	}
	
	/**
	 * Magic __isset()
	 */
	public function __isset($var){
		return isset($this->$var);
	}
	
	/**
	 * Magic __unset()
	 */
	public function __unset($var){
		unset($this->$var);
	}
	
	/**
	 * Returns a property value. [ArrayAccess]
	 */
	public function get( $var ){
		return $this->__get($var);
	}
	
	/**
	 * Sets a property value.
	 */
	public function set( $var, $val ){
		$this->__set($var, $val);
		return $this;
	}
	
	/**
	 * Returns true if a property exists.
	 */
	public function exists($var){
		return $this->__isset($var);
	}
	
	/**
	 * Unsets a property
	 */
	public function remove($var){
		$this->__unset($var);
		return $this;
	}
	
	/**
	 * Returns a property value [ArrayAccess].
	 */
	public function offsetGet( $index ){
		return isset($this->$index) ? $this->$index : $this->magicOffsetGet($index);
	}
	
	/**
	 * Sets a property value [ArrayAccess].
	 */
	public function offsetSet( $index, $newval ){
		$this->$index = $newval;
	}
	
	/**
	 * Returns true if a property exists [ArrayAccess].
	 */
	public function offsetExists( $index ){
		return isset($this->$index);
	}
	
	/**
	 * Unsets a property [ArrayAccess].
	 */
	public function offsetUnset( $index ){
		unset($this->$index);
	}
	
	/**
	 * Returns count of object properties [Countable].
	 */
	public function count(){
		return count($this);
	}
	
	/**
	 * Imports an array or object containing data as properties.
	 */
	public function import( $data ){
		
		if (!is_array($data) && !$data instanceof \Traversable){
			$data = (array) $data;
		}
		
		foreach($data as $k => $v){
			$this->$k = $v;
		}
		
		return $this;
	}
	
	/**
	 * Returns object properties as array.
	 * @uses get_object_vars()
	 */
	public function toArray(){
		return get_object_vars($this);
	}
	
	/**
	 * __get()-like magic method for ArrayAccess.
	 * 
	 * Subclasses could use this to, for example, allow access
	 * to protected or private properties.
	 * 
	 * This function also works as a setter, e.g.
	 * $object['nonexistant'] = 'this works'
	 */
	private function magicOffsetGet($index){
		
		if (method_exists($this, '__offsetGet')) {
			return $this->__offsetGet($index);
		}
		
		return null;
	}
	
}
