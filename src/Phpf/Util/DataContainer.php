<?php

namespace Phpf\Util;

class DataContainer implements \ArrayAccess, \Countable, iContainer {
	
	protected $data = array();
	
	public function __set($var, $val){
		$this->data[$var] = $val;
	}
	
	public function __get($var){
		return $this->data[$var];
	}
	
	public function __isset($var){
		return isset($this->data[$var]);
	}
	
	public function __unset($var){
		unset($this->data[$var]);
	}
	
	public function set( $var, $val ){
		$this->__set($var, $val);
		return $this;
	}
	
	public function get( $var ){
		return $this->__get($var);
	}
	
	public function exists($var){
		return $this->__isset($var);
	}
	
	public function remove($var){
		$this->__unset($var);
		return $this;
	}
	
	public function setData( array $data ){
		$this->data = $data;
		return $this;
	}
	
	public function addData( array $data ){
		$this->data = array_merge($this->data, $data);
		return $this;
	}
	
	public function getData(){
		return $this->data;
	}
	
	public function offsetGet( $index ){
		return $this->data[$index];
	}
	
	public function offsetSet( $index, $newval ){
		$this->data[$index] = $newval;
	}
	
	public function offsetExists( $index ){
		return isset($this->data[$index]);
	}
	
	public function offsetUnset( $index ){
		unset($this->data[$index]);
	}
	
	public function count(){
		return count($this->data);
	}
	
	/**
	 * Imports an array or object containing data as properties.
	 */
	public function import( $data ){
		
		if (!is_array($data) && !$data instanceof \Traversable){
			$data = (array) $data;
		}
		
		foreach($data as $k => $v){
			$this->data[$k] = $v;
		}
		
		return $this;
	}
	
	/**
	 * Returns data array.
	 */
	public function toArray(){
		return $this->data;
	}
	
}
