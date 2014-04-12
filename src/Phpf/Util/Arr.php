<?php

namespace Phpf\Util;

class Arr
{

	/**
	 * Retrieves a value from $array given its path in dot notation
	 */
	public static function dotGet(array &$array, $dotpath) {

		if (false === strpos($dotpath, '.'))
			return isset($array[$dotpath]) ? $array[$dotpath] : null;

		$loc = &$array;

		foreach ( explode('.', $dotpath) as $step ) {
			if (isset($loc[$step]))
				$loc = &$loc[$step];
		}

		return $loc;
	}

	/**
	 * Sets a value in $array given its path in dot notation.
	 */
	public static function dotSet(array &$array, $dotpath, $value) {

		if (false === strpos($dotpath, '.'))
			return $array[$dotpath] = $value;

		$loc = &$array;

		foreach ( explode('.', $dotpath) as $step )
			$loc = &$loc[$step];

		return $loc = $value;
	}

	/**
	 * Unets a value in $array given its path in dot notation.
	 */
	public static function dotUnset(array &$array, $dotpath) {

		if (false === strpos($dotpath, '.'))
			return $array[$dotpath] = $value;

		$loc = &$array;

		foreach ( explode('.', $dotpath) as $step )
			$loc = &$loc[$step];

		unset($loc);
	}

	/**
	 * Merge user defined arguments into defaults array.
	 *
	 * @param string|array|object $args Value to merge with $defaults
	 * @param array $defaults Array that serves as the defaults.
	 * @return array Merged user defined values with defaults.
	 */
	public static function parse($args, $defaults = '') {
		
		if (is_array($args))
			$r = &$args;
		elseif (is_object($args))
			$r = get_object_vars($args);
		else
			parse_str($args, $r);

		return is_array($defaults) ? array_merge($defaults, $r) : $r;
	}

	/**
	 * Filters a list of objects, based on a set of key => value arguments.
	 *
	 * @param array $list An array of objects to filter
	 * @param array $args An array of key => value arguments to match against each
	 * object
	 * @param string $operator The logical operation to perform:
	 *    'AND' means all elements from the array must match;
	 *    'OR' means only one element needs to match;
	 *    'NOT' means no elements may match.
	 *   The default is 'AND'.
	 * @return array
	 */
	public static function filter($list, $args = array(), $operator = 'AND', $keys_exists_only = false) {

		$operator = strtoupper($operator);
		$count = count($args);
		$newarr = array();

		foreach ( $list as $key => $obj ) {
			#$to_match = (array) $obj;
			$matched = 0;

			foreach ( $args as $m_key => $m_value ) {
				if (array_key_exists($m_key, $to_match)) {
					/**
					 * Only match keys if $keys_exist_only = true
					 * @since 2/8/14
					 */
					if ($m_value == $to_match[$m_key] || $keys_exists_only) {
						$matched++;
					}
				}
			}

			if (('AND' == $operator && $matched == $count) || ('OR' == $operator && $matched > 0) || ('NOT' == $operator && 0 == $matched)) {
				$newarr[$key] = $obj;
			}
		}

		return $newarr;
	}

	/**
	 * Pluck a certain field out of each object in a list.
	 *
	 * @param array $list A list of objects or arrays
	 * @param int|string $field A field from the object to place instead of the
	 * entire object
	 * @return array
	 */
	public static function pluck($list, $field) {

		foreach ( $list as $key => $value ) {

			if (is_object($value))
				$list[$key] = $value->$field;
			else
				$list[$key] = $value[$field];
		}

		return $list;
	}

	/**
	 * Pluck a certain field out of each object in a list by reference.
	 * This will change the values of the original array/object list.
	 *
	 * @param array $list A list of objects or arrays
	 * @param int|string $field A field from the object to place instead of the
	 * entire object
	 * @return array
	 */
	public static function pluckRef(&$list, $field) {

		foreach ( $list as &$value ) {

			if (is_object($value))
				$value = $value->$field;
			else
				$value = $value[$field];
		}

		return $list;
	}

	/**
	 * Implode an array into a list of items separated by $separator.
	 * Use $last_separator for the last list item.
	 *
	 * Useful for natural language lists (e.g first, second & third).
	 *
	 * Graciously stolen from humanmade hm-core:
	 * @link https://github.com/humanmade/hm-core/blob/master/hm-core.functions.php
	 *
	 * @param array $array
	 * @param string $separator. (default: ', ')
	 * @param string $last_separator. (default: ', and ')
	 * @return string a list of array values
	 */
	public static function implodeNice($array, $separator = ', ', $last_separator = ', and ') {

		if (1 === count($array))
			return reset($array);

		$end_value = array_pop($array);

		$list = implode($separator, $array);

		return $list.$last_separator.$end_value;
	}

}
