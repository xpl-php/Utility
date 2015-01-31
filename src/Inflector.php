<?php

namespace xpl\Utility;

class Inflector {
	
	/**
	 * Converts a camelcased string to lowercased underscored string.
	 * 
	 * @param string
	 * @return string
	 */
	public static function underscore($camelCased) {
		
		// Insert whitespace where a lowercase character meets an uppercase character.
		// Convert the whitespace to underscores.
		$string = str_replace(' ', '_', preg_replace('/([a-z\d]+)([A-Z])/', '$1_$2', $camelCased));
		
		// Insert underscores in between sequential uppercase characters.
		// Lowercase the string.
		return strtolower(preg_replace('/([A-Z]+)([A-Z])/', '$1_$2', $string));
	}
	
	/**
	 * Converts an underscored string to camelcase.
	 * 
	 * @param string
	 * @return string
	 */
	public static function camelcase($string) {
		
		// Lowercase the string.
		// Replace underscores and dashes with whitespace.
		$string = str_replace(array('_', '-'), ' ', strtolower($string));
		
		// Strip non alphanumeric characters.
		// Upper the first letter in each word.
		$string = preg_replace('/[^a-z0-9]/i', '', ucwords(trim($string)));
		
		// Remove whitespace and lowercase the first letter.
		return lcfirst(str_replace(' ', '', $string));
	}
	
	/**
	 * Converts a string to dashed (like a URL slug).
	 * 
	 * @param string
	 * @return string
	 */
	public static function dashed($string) {
		return strtolower(preg_replace('/([A-Z])/', '-$1', static::camelcase($string)));
	}
	
	/**
	 * Converts a string to a URL slug-like string.
	 * @param string
	 * @return string
	 */
	public static function title($string, $sep = '-') {
		$sep = $sep === '_' ? '_' : '-';
		$str = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_BACKTICK);
		$str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
		$str = preg_replace("#[\"\'\’]#", '', $str);
		$str = preg_replace("#[^a-z0-9]#i", $sep, $str);
		$str = preg_replace("#[/_|+ -]+#u", $sep, $str);
		return strtolower(trim($str, $sep));
	}
	
}
