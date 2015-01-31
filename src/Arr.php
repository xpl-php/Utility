<?php

namespace xpl\Utility;

class Arr {
	
	/**
	 * in_array() with case-sensitive option.
	 * 
	 * @param string $needle Needle
	 * @param array $haystack Haystack
	 */
	public static function in($needle, $haystack, $strict = true, $case_insensitive = false) {
		
		if ($case_insensitive) {
			return in_array(strtolower($needle), array_map('strtolower', $haystack), $strict);
		}
		
		return in_array($needle, $haystack, $strict);
	}
	
	/**
	 * Retrieves a value from an array given its path in dot notation.
	 * 
	 * @param array &$array Associative array.
	 * @param string $dotpath Item path given in dot-notation (e.g. "some.nested.item")
	 * @return mixed Value of item if found, otherwise null.
	 */
	public static function get(array &$array, $key) {
		
		if (false === strpos($key, '.')) {
			return isset($array[$key]) ? $array[$key] : null;
		}
		
		$a = &$array;
		
		foreach(explode('.', $key) as $segment) {
		
			if (! isset($a[$segment])) {
				return null;
			}
		
			$a = &$a[$segment];
		}
		
		return $a;
	}
	
	/**
	 * Sets an array value given its path in dot notation.
	 * 
	 * @param array &$array
	 * @param string $key
	 * @param mixed $value
	 * @return array
	 */
	public static function set(array &$array, $key, $value) {
		
		if (false === strpos($key, '.')) {
			$array[$key] = $value;
			return $array;
		}
		
		$a =& $array;
		
		foreach(explode('.', $key) as $segment) {
			
			isset($a[$segment]) or $a[$segment] = array();
			
			$a =& $a[$segment];
		}
		
		$a = $value;
		
		return $array;
	}
	
	/**
	 * Unsets an array item given its path in dot notation.
	 * 
	 * @param array &$array Array to search within.
	 * @param string $key Dot-notated path.
	 * @return void
	 */
	public static function remove(&$array, $key) {
		
		if (false === strpos($key, '.')) {
			unset($array[$key]);
			return $array;
		}
		
		$a =& $array;
		
		$segments = explode('.', $key);
		$n = count($segments);
		$i = 1;
		
		foreach($segments as $segment) {
			
			if (! array_key_exists($segment, $a)) {
				return;
			}
			
			if ($i !== $n) {
				$a =& $a[$segment];
				$i++;
			} else {
				unset($a[$segment]);
			}
		}
		
		return $array;
	}
	
	/**
	 * Checks whether an array item exists with the given path.
	 * 
	 * @param array &$array
	 * @param string $key
	 * @return boolean
	 */
	public static function exists(array &$array, $key) {
		
		if (false === strpos($key, '.')) {
			return array_key_exists($key, $array);
		}
		
		$a =& $array;
		
		foreach(explode('.', $key) as $segment) {
		
			if (! array_key_exists($segment, $a)) {
				return false;
			}
		
			$a = &$a[$segment];
		}
		
		return true;
	}
	
	/**
	 * Filters an array by key.
	 * 
	 * Like array_filter(), except that it operates on keys rather than values.
	 * 
	 * @example
	 * $array = array(1 => 1, 2 => 2, "3" => 3, "Four" => 4);
	 * 
	 * $newArray = array_filter_keys($array, 'is_numeric');
	 * 
	 * // $newArray is = array(1 => 1, 2 => 2, "3" => 3);
	 * 
	 * @param array $input Array to filter by key.
	 * @param callable|null $callback Callback filter. Default null (removes empty keys).
	 * @return array Key/value pairs of $input having the filtered keys.
	 */
	public static function filterKeys(array $input, $callback = null) {
		$filtered = array_filter(array_keys($input), $callback);
		return empty($filtered) ? array() : array_intersect_key($input, array_flip($filtered));
	}
	
	/**
	 * Applies a callback function to each key in an array.
	 * 
	 * @example
	 * $array = array('first' => 1, 'second' => 2, 'third' => 3);
	 * 
	 * $newArray = array_map_keys('ucfirst', $array);
	 * 
	 * $newArray is: array('First' => 1, 'Second' => 2, 'Third' => 3);
	 * 
	 * @param callable $callback Callback to apply to each array key.
	 * @param array $array Associative array.
	 * @return array A new array with the callback applied to each key.
	 */
	public static function mapKeys($callback, array $array) {
		return array_combine(array_map($callback, array_keys($array)), array_values($array));
	}
	
	/**
	 * Applies the callback function to each key/value pair in the array.
	 *
	 * The key and value are passed to the callback as the first and
	 * second arguments, respectively.
	 *
	 * @param callable $callback Callback accepting 2 args: key and value. Returns new value.
	 * @param array $array Associative array to apply callback.
	 * @return array Array with new values, keys preserved.
	 */
	public static function mapAssoc($callback, array $array) {
		$map = array();
		foreach ($array as $key => $value) {
			$map[$key] = $callback($key, $value);
		}
		return $map;
	}
	
	/**
	 * Whether every array value passes the test.
	 *
	 * @param array $array Array.
	 * @param callable $test Callback - must return true or false.
	 * @return boolean True if all values passed the test, otherwise false.
	 */
	public static function validate(array $array, $test) {
		foreach($array as $key => $value) {
			if (! $test($value)) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Returns a new array containing only those items which pass the test.
	 * 
	 * @param array $array Array.
	 * @param callable $test Callback passed key and value; must return boolean.
	 * @return array Array of items that passed the test with keys preserved.
	 */
	public static function where(array $array, $test) {
		$match = array();
		foreach($array as $key => $value) {
			if ($test($key, $value)) {
				$match[$key] = $value;
			}
		}
		return $match;
	}
	
	/**
	 * Returns an array of elements that satisfy the given conditions.
	 * 
	 * @param array $array Array of arrays or objects.
	 * @param array $conditions Associative array of keys/properties and values.
	 * @param string $operator One of 'AND', 'OR', or 'NOT'. Default 'AND'.
	 * @return array Array elements that satisfy the conditions.
	 */
	public static function select(array $array, array $conditions, $operator = 'AND') {
		
		if (empty($conditions)) {
			return $array;
		}
		
		$filtered = array();
		$oper = strtoupper($operator);
		$n = count($conditions);
		
		foreach ($array as $key => $obj) {
			
			$matches = 0;
			
			if (is_array($obj)) {
				foreach($conditions as $mKey => $mVal) {
					if (array_key_exists($mKey, $obj) && $mVal == $obj[$mKey]) {
						$matches++;
					}
				}
			} else if (is_object($obj)) {
				foreach($conditions as $mKey => $mVal) {
					if (isset($obj->$mKey) && $mVal == $obj->$mKey) {
						$matches++;
					}
				}
			}
			
			if (('AND' === $oper && $matches == $n) 
				|| ('OR' === $oper && $matches > 0) 
				|| ('NOT' === $oper && 0 == $matches) 
			) {
				$filtered[$key] = $obj;
	        }
		}
		
		return $filtered;
	}
	
	/**
	 * Retrieves a key from an array given its position - "first", "last", or an integer.
	 * 
	 * If given a positive integer, returns the key in the given position.
	 * e.g. 1 returns the first key, 2 the second, 3 the third, etc.
	 * 
	 * If given a negative integer, returns the key that would correspond to the absolute 
	 * value of the given position working backwards in the array.
	 * e.g. -1 returns the last key, -2 the second to last, -3 the third to last, etc.
	 * 
	 * If given 0, null is returned.
	 * 
	 * @param array $array Associative array.
	 * @param string|int $pos Position of key to return - "first", "last", or non-zero integer.
	 * @return scalar Key in given position, if found, otherwise null.
	 */
	public static function key(array $array, $pos) {
		
		if ("0" == $pos) {
			return null;
		}
		
		if ('first' === $pos) {
			reset($array);
			return key($array);
		} else if ('last' === $pos) {
			end($array);
			return key($array);
		} else if (! is_numeric($pos)) {
			throw new InvalidArgumentException('Position must be "first", "last", or int, given: '.gettype($pos));
		}
		
		$pos = (int) $pos;
		$keys = array_keys($array);
		
		if ($pos < 0) {
			$pos = abs($pos);
			$keys = array_reverse($keys, false);
		}
		
		return isset($keys[$pos-1]) ? $keys[$pos-1] : null;	
	}
	
	/**
	 * Pulls a value from each array in an array by key/index and returns an array of the values.
	 * 
	 * @param array $arrays Array of arrays.
	 * @param string|int $index Index offset or key name to pull from each array.
	 * @param string|null $key_index [Optional] Index/key to use for keys in returned array.
	 * @return array Indexed array of the value pulled from each array.
	 */
	public static function pull(array $arrays, $index, $key_index = null) {
		$return = array();
		foreach($arrays as $key => $array) {
			if (null !== $key_index) {
				$key = $array[$key_index];
			}
			$return[$key] = (null === $index) ? $array : $array[$index];
		}
		return $return;
	}
	
	/**
	 * Calls a method on each object in an array and returns an array of the results.
	 * 
	 * @param array $objects Array of objects.
	 * @param string|null $method Method to call on each object, or null to return whole object.
	 * @param string|null $key_method [Optional] Method used to get keys used in returned array.
	 * @return array Indexed array of values returned from each object.
	 */
	public static function mpull(array $objects, $method, $key_method = null) {
		$return = array();
		foreach($objects as $key => &$obj) {
			if (null !== $key_method) {
				$key = $obj->$key_method();
			}
			$return[$key] = (null === $method) ? $obj : $obj->$method();
		}
		return $return;
	}
	
	/**
	 * Pulls a property from each object in an array and returns an array of the values.
	 * 
	 * @param array $objects Array of objects.
	 * @param string $property Name of property to get from each object, or null for whole object.
	 * @param string|null $key_prop [Optional] Property to use for keys in returned array.
	 * @return array Indexed array of property value from each object.
	 */
	public static function ppull(array $objects, $property, $key_prop = null) {
		$return = array();
		foreach($objects as $key => $obj) {
			if (null !== $key_prop) {
				$key = $obj->$key_prop;
			}
			$return[$key] = (null === $property) ? $obj : $obj->$property;
		}
		return $return;
	}
		
	/**
	 * Filters an array of objects by method.
	 * 
	 * If object returns an empty value, the object is not included in the returned array.
	 * To reverse this behavior (only include those which return empty), pass true
	 * as the third parameter.
	 * 
	 * @author facebook/libphutil
	 * 
	 * @param array $objects Array of objects.
	 * @param string $method Method to call on each object.
	 * @param boolean $negate Whether to return objects which return empty. Default false.
	 * @return array Objects which pass the filter.
	 */
	public static function mfilter(array $objects, $method, $negate = false) {
		$return = array();
		foreach($objects as $key => &$object) {
			$value = $object->$method();
			if (empty($value)) {
				$negate and $return[$key] = $object;
			} else if (! $negate) {
				$return[$key] = $object;
			}
		}
		return $return;
	}
	
	/**
	 * Array property filter
	 */
	public static function pfilter(array $objects, $property, $negate = false) {
		$return = array();
		foreach($objects as $key => &$object) {
			if (empty($object->$property)) {
				$negate and $return[$key] = $object;
			} else if (! $negate) {
				$return[$key] = $object;
			}
		}
		return $return;
	}
	
	/**
	 * Array key filter
	 */
	public static function kfilter(array $arrays, $key, $negate = false) {
		$return = array();
		foreach($arrays as $o_key => $array) {
			if (empty($array[$key])) {
				$negate and $return[$o_key] = $array;
			} else if (! $negate) {
				$return[$o_key] = $array;
			}
		}
		return $return;
	}
	
	/**
	 * Merges a vector of arrays.
	 * 
	 * More performant than using array_merge in a loop.
	 * 
	 * @author facebook/libphutil
	 * 
	 * @param array $arrays Array of arrays to merge.
	 * @return array Merged arrays.
	 */
	public static function mergev(array $arrays) {
		return empty($arrays) ? array() : call_user_func_array('array_merge', $arrays);
	}
	
	/**
	 * Merge arrays into an array by reference.
	 * 
	 * @example
	 * $a = array('One', 'Two');
	 * $b = array('Three', 'Four');
	 * $c = array('Five', 'Six');
	 * 
	 * array_merge_ref($a, $b, $c);
	 * 
	 * $a is now: array('One', 'Two', 'Three', 'Four', 'Five', 'Six');
	 * 
	 * @param array &$array Array to merge other arrays into.
	 * @param ... Arrays to merge.
	 * @return array Given arrays merged into the first array.
	 */
	public static function mergeRef(array &$array /*, $array1 [, ...] */){
		return $array = call_user_func_array('array_merge', func_get_args());
	}
	
	/**
	 * Builds an array from an array or object so that all non-scalar items are arrays.
	 * 
	 * @param array|\Traversable $thing
	 * @return array
	 */
	public static function build($thing) {
		
		$array = array();
		
		foreach($thing as $key => $value) {
			
			if (is_array($value) || is_object($value)) {
				$array[$key] = static::build($value);
				
			} else {
				
				if (! is_scalar($value)) {
					$array[$key] = is_bool($value) ? $value : (string) $value;
				
				} else if (is_numeric($value)) {
					$array[$key] = Str::castNum($value);
			
				} else if ($value === 'true' || $value === 'false') {
					$array[$key] = ($value === 'true');
			
				} else {
					$array[$key] = (string) $value;
				}
			}
		}
	
		return $array;
	}
	
	/**
	 * Converts an array, object, or string to an array.
	 * 
	 * @param mixed $thing Array, object, or string (JSON, serialized, or XML).
	 * @return array
	 */
	public static function to($thing) {
			
		if (is_array($thing)) {
			return static::build($thing);
		}
		
		if (is_object($thing)) {
			return is_callable(array($thing, 'toArray')) ? $thing->toArray() : static::build($thing);
		}
		
		if (is_string($thing)) {
			if (is_json($thing)) {
				return json_decode($thing, true);
			} else if (is_serialized($thing)) {
				return static::to(unserialize($thing));
			} else if (is_xml($thing)) {
				return xml2array($thing);
			}
		}
		
		return (array) $thing;
	}
		
}
