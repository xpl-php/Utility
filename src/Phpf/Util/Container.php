<?php

namespace Phpf\Util;

use ArrayAccess;
use Countable;
use IteratorAggregate;

class Container implements ArrayAccess, Countable, IteratorAggregate, iContainer
{

	/**
	 * Magic __set()
	 */
	public function __set($var, $val) {
		$this->$var = $val;
	}

	/**
	 * Magic __get()
	 */
	public function __get($var) {
		return isset($this->$var) ? $this->result($this->$var) : null;
	}

	/**
	 * Magic __isset()
	 */
	public function __isset($var) {
		return isset($this->$var);
	}

	/**
	 * Magic __unset()
	 */
	public function __unset($var) {
		unset($this->$var);
	}

	/**
	 * Sets a property value.
	 * [iContainer]
	 */
	public function set($var, $val) {
		$this->$var = $val;
		return $this;
	}

	/**
	 * Returns a property value.
	 * [iContainer]
	 */
	public function get($var) {
		return isset($this->$var) ? $this->result($this->$var) : null;
	}

	/**
	 * Returns true if a property exists.
	 * [iContainer]
	 */
	public function exists($var) {
		return isset($this->$var);
	}

	/**
	 * Unsets a property
	 * [iContainer]
	 */
	public function remove($var) {
		unset($this->$var);
		return $this;
	}

	/**
	 * Sets a property value.
	 * [ArrayAccess]
	 */
	public function offsetSet($index, $newval) {
		$this->$index = $newval;
	}

	/**
	 * Returns a property value.
	 * [ArrayAccess]
	 */
	public function offsetGet($index) {
		return isset($this->$index) ? $this->result($this->$index) : $this->__offsetGet($index);
	}

	/**
	 * Returns true if a property exists.
	 * [ArrayAccess]
	 */
	public function offsetExists($index) {
		return isset($this->$index);
	}

	/**
	 * Unsets a property. 
	 * [ArrayAccess]
	 */
	public function offsetUnset($index) {
		unset($this->$index);
	}

	/**
	 * Returns number of data items.
	 * [Countable]
	 */
	public function count() {
		return count($this);
	}

	/**
	 * Returns iterator.
	 * [IteratorAggregate]
	 */
	public function getIterator() {
		return new \ArrayIterator($this);
	}

	/**
	 * Imports an array or object containing data as properties.
	 * [iContainer]
	 */
	public function import($data) {

		if (! is_array($data) && ! $data instanceof \Traversable) {
			$data = (array) $data;
		}

		foreach ( $data as $k => $v ) {
			$this->set($k, $v);
		}

		return $this;
	}

	/**
	 * Returns object properties as array.
	 * [iContainer]
	 * @uses get_object_vars()
	 */
	public function toArray() {
		return get_object_vars($this);
	}
	
	/**
	 * Returns raw value if set. Does not execute if value is a closure.
	 */
	public function getRaw($var) {
		return isset($this->$var) ? $this->$var : null;
	}

	/**
	 * Executes callable properties - i.e. closures or invokable objects.
	 * Hence, methods can be attached as properties.
	 */
	public function __call($fn, $params) {

		if ($this->exists($fn)) {

			$callback = $this->getRaw($fn);
			
			if (is_callable($callback)) {
								
				switch(count($params)) {
					case 0 :
						return $callback();
					case 1 :
						return $callback($params[0]);
					case 2 :
						return $callback($params[0], $params[1]);
					case 3 :
						return $callback($params[0], $params[1], $params[2]);
					case 4 :
						return $callback($params[0], $params[1], $params[2], $params[3]);
					default :
						return call_user_func_array($callback, $params);
				}
			}
		}

		trigger_error("Unknown method '$fn'.", E_USER_NOTICE);
	}

	/**
	 * If value is a closure, executes it before returning.
	 */
	protected function result($var) {
		return ($var instanceof \Closure) ? $var() : $var;
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
	protected function __offsetGet($index) {
		return null;
	}
	
}
