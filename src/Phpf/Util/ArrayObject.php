<?php

namespace Phpf\Util;

use ArrayAccess;
use Countable;
use IteratorAggregate;

class ArrayObject implements ArrayAccess, Countable, IteratorAggregate {
	
	public function __construct($data = null) {
		
		if (isset($data)){
			$this->import($data);
		}
	}
	
	public function filter( \Closure $call ){
		$return = array();
		foreach($this as $k => $v) {
			if (1 <= $call($k, $v)) {
				$return[$k] = $v;
			}
		}
		return $return;
	}
	
	public function push($item1 /*[, $item2 [, ...]]*/){
		foreach(func_get_args() as $item){
			$this[] = $item;
		}
		return $this;
	}
	
	public function import($data) {
		if (! is_array($data) && ! $data instanceof \Traversable){
			$data = (array) $data;
		}
		foreach($data as $k => $v){
			$this->$k = $v;
		}
	}
	
	public function ksort() {
		$arr = $this->asArray();
		$sort = ksort($arr);
		$this->import($sort);
		return $this;
	}
	
	public function merge(array $arr) {
		foreach($arr as $k => $v) {
			if (! isset($this->$k)){
				$this->$k = $v;
			}
		}
	}
	
	public function join($sep = ',') {
		return implode($sep, $this->asArray());
	}
	
	public function nextIndex(){
		return max(array_keys($this->asArray())) + 1;
	}
	
	public function asArray(){
		return get_object_vars($this);
	}
	
	public function keys(){
		return array_keys($this->asArray());
	}
	
	public function values(){
		return array_values($this->asArray());
	}
	
	public function in($val, $strict = true){
		return in_array($val, $this->asArray(), $strict);
	}
	
	/**
	 * Returns a property value [ArrayAccess].
	 */
	public function offsetGet( $index ){
		return isset($this->$index) ? $this->$index : null;
	}
	
	/**
	 * Sets a property value [ArrayAccess].
	 */
	public function offsetSet( $index, $newval ){
		
		if (empty($index)) {
			$index = $this->nextIndex();
		}
		
		$this->{$index} = $newval;
	}
	
	/**
	 * Returns true if a property exists [ArrayAccess].
	 */
	public function offsetExists( $index ){
		return isset($this->{$index});
	}
	
	/**
	 * Unsets a property [ArrayAccess].
	 */
	public function offsetUnset( $index ){
		unset($this->{$index});
	}
	
	/**
	 * Returns count of object properties [Countable].
	 */
	public function count(){
		return count($this);
	}
	
	/**
	 * Returns ArrayIterator [IteratorAggregate].
	 */
	public function getIterator() {
		return new \ArrayIterator($this);
	}
}
