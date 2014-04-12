<?php

namespace Phpf\Util;

class DataContainer extends Container
{

	protected $data = array();
	
	/**
	 * Magic __set()
	 */
	public function __set($var, $val) {
		$this->data[$var] = $val;
	}
	
	/**
	 * Magic __get()
	 */
	public function __get($var) {
		return isset($this->data[$var]) ? $this->result($this->data[$var]) : null;
	}

	/**
	 * Magic __isset()
	 */
	public function __isset($var) {
		return isset($this->data[$var]);
	}

	/**
	 * Magic __unset()
	 */
	public function __unset($var) {
		unset($this->data[$var]);
	}

	/**
	 * Gets a property value.
	 * [iContainer]
	 */
	public function get($var) {
		return isset($this->data[$var]) ? $this->result($this->data[$var]) : null;
	}

	/**
	 * Sets a property value.
	 * [iContainer]
	 */
	public function set($var, $val) {
		$this->data[$var] = $val;
		return $this;
	}

	/**
	 * Returns true if a property exists.
	 * [iContainer]
	 */
	public function exists($var) {
		return isset($this->data[$var]);
	}

	/**
	 * Unsets a property.
	 * [iContainer]
	 */
	public function remove($var) {
		unset($this->data[$var]);
		return $this;
	}

	/**
	 * Gets a property value.
	 * [ArrayAccess]
	 */
	public function offsetGet($index) {
		return isset($this->data[$index]) ? $this->result($this->data[$index]) : null;
	}

	/**
	 * Sets a property value.
	 * [ArrayAccess]
	 */
	public function offsetSet($index, $newval) {
		$this->data[$index] = $newval;
	}

	/**
	 * Returns true if a property exists.
	 * [ArrayAccess]
	 */
	public function offsetExists($index) {
		return isset($this->data[$index]);
	}

	/**
	 * Unsets a property.
	 * [ArrayAccess]
	 */
	public function offsetUnset($index) {
		unset($this->data[$index]);
	}

	/**
	 * Returns number of data items.
	 * [Countable]
	 */
	public function count() {
		return count($this->data);
	}
	
	/**
	 * Returns iterator.
	 * [IteratorAggregate]
	 */
	public function getIterator() {
		return new \ArrayIterator($this->data);
	}

	/**
	 * Returns data array.
	 * [iContainer]
	 */
	public function toArray() {
		return $this->data;
	}
	
	/**
	 * Returns raw value without executing closures.
	 */
	public function getRaw($var) {
		return isset($this->data[$var]) ? $this->data[$var] : null;
	}
	
	/**
	 * Sets data array, replacing existing array.
	 */
	public function setData(array $data) {
		$this->data = $data;
		return $this;
	}
	
	/**
	 * Adds array of to existing array.
	 */
	public function addData(array $data) {
		$this->data = array_merge($this->data, $data);
		return $this;
	}
	
	/**
	 * Returns the array of data.
	 * Identical to toArray()
	 */
	public function getData() {
		return $this->data;
	}
	
}
