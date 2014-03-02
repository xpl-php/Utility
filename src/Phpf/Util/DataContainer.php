<?php

namespace Phpf\Util;

class DataContainer {
	
	protected $data = array();
	
	public function __set($var, $val){
		$this->data[$var] = $val;
	}
	
	public function __get($var){
		return isset($this->data[$var]) ? $this->data[$var] : null;
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
	
}
