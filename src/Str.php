<?php

namespace xpl\Utility;

class Str {
	
	protected static $loc;
	
	/**
	 * Checks whether given string looks like a valid URL.
	 * 
	 * A valid URL will either start with two slashes ("//") or
	 * contain a protocol followed by a colon and two slashes ("://").
	 * 
	 * @param string $str String to check.
	 * @return boolean
	 */
	public static function isUrl($str) {
		return 0 === strpos($str, '//') || fnmatch('*://*', $str);
	}
	
	/**
	 * Checks whether the given value is a valid JSON string.
	 * 
	 * @param string $str String to test.
	 * @return boolean True if string is JSON, otherwise false.
	 */
	public static function isJson($str) {
		if (! is_string($str)) {
			return false;
		}
		$json = @json_decode($str, true);
		return JSON_ERROR_NONE === json_last_error() ? is_array($json) : false;
	}
	
	/**
	 * Checks whether the given value is a valid serialized string.
	 * 
	 * @param mixed $data Value to check if serialized
	 * @return boolean TRUE If value is a valid serialized string, otherwise false.
	 */
	public static function isSerialized($data) {
		if (! is_string($data) || empty($data)) {
	    	return false;
		}
		return @unserialize($data) !== false;
	}
	
	/**
	 * Checks whether the given value is a valid XML string.
	 * 
	 * @param mixed $data Value to check if XML.
	 * @return boolean TRUE if value is a valid XML string, otherwise false.
	 */
	public static function isXml($data) {
		if (! is_string($data) || '<?xml' !== substr($data, 0, 5)) {
			return false;
		}
		return (simplexml_load_string($data) instanceof \SimpleXMLElement);
	}
	
	/**
	 * Escape a string using filter_var
	 * 
	 * @param string $string The string to sanitize.
	 * @param bool $flags filter_var() flags. Default: FILTER_FLAG_NONE
	 * @return string Sanitized string.
	 */
	public static function esc($string, $flags = 0) {
		$string = preg_replace('/[\x00-\x08\x0B-\x1F]/', '', $string);
		empty($flags) and $flags = FILTER_FLAG_NONE;
		return filter_var($string, FILTER_SANITIZE_STRING, $flags);
	}
	
	/**
	 * Strips non-ASCII (standard) characters from a string.
	 * 
	 * @param string $string The string to sanitize.
	 * @return string Sanitized string.
	 */
	public static function escAscii($string) {
		$string = preg_replace('/[\x00-\x08\x0B-\x1F]/', '', $string);
		return filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_BACKTICK);
	}

	/**
	 * Strips non-alphanumeric characters from a string.
	 * 
	 * Add characters to $extras to preserve those as well.
	 * Extra chars should be escaped for use in preg_*() functions.
	 * 
	 * @param string $string String to escape.
	 * @param string $extras [Optional] Other characters to strip.
	 * @return string Escaped string.
	 */
	public static function escAlnum($string, $extras = null) {
		if (empty($extras) && ctype_alnum($string)) {
			return $string;
		}
		$pattern = '/[^a-zA-Z0-9'.(isset($extras) ? $extras : '').']/';
		return preg_replace($pattern, '', $string);
	}
			
	/**
	 * Escapes text for SQL LIKE special characters % and _.
	 *
	 * @param string $text The text to be escaped.
	 * @return string text, safe for inclusion in LIKE query.
	 */
	public static function escSqlLike($text) {
		return str_replace(array("%", "_"), array("\\%", "\\_"), $text);
	}
		
	/**
	 * Returns true if $haystack starts with $needle.
	 * 
	 * @param string $haystack String to search within.
	 * @param string $needle String to find.
	 * @param boolean $match_case Whether to match case-sensitively. Default true.
	 * @return boolean 
	 */
	public static function startsWith($haystack, $needle, $match_case = true) {
		return $match_case
			? 0 === strpos($haystack, $needle)
			: 0 === stripos($haystack, $needle);
	}
	
	/**
	 * Returns true if $haystack ends with $needle.
	 * 
	 * @param string $haystack String to search within.
	 * @param string $needle String to find.
	 * @param boolean $match_case Whether to match case-sensitively. Default true.
	 * @return boolean 
	 */
	public static function endsWith($haystack, $needle, $match_case = true) {
		return $match_case
			? 0 === strcmp($needle, substr($haystack, -strlen($needle)))
			: 0 === strcasecmp($needle, substr($haystack, -strlen($needle)));
	}
	
	/** 
	 * Returns the text between (the first occurance of) two strings.
	 * 
	 * The "between" strings are not included in output.
	 * 
	 * @param string $string The text string.
	 * @param string $start_str The starting string
	 * @param string $end_str The ending string
	 * @return string Text between $start and $end. 
	 */
	public static function between($string, $start_str, $end_str) {
		$str1 = explode($start_str, $string);
		$str2 = explode($end_str, $str1[1]);
		return trim($str2[0]);
	}
	
	/**
	* Strips unescaped unicode characters (e.g. u00a0). 
	* @uses mb_detect_encoding, mb_convert_encoding
	*/
	public static function stripInvalidUnicode($str) {
			
		if (static::mb()) {
				
			$encoding = mb_detect_encoding($str);
			
			if ('UTF-8' !== $encoding && 'ASCII' !== $encoding) {
				// temporarily unset mb substitute character and convert
				$mb_sub = ini_set('mbstring.substitute_character', "none");
				$str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
				ini_set('mbstring.substitute_character', $mb_sub);
			}
		}
		
		return stripcslashes(preg_replace('/\\\\u([0-9a-f]{4})/i', '', $str));
	}
	
	/**
	 * Get a given number of sentences from a string.
	 *
	 * @param string $text The full string of sentences.
	 * @param integer $num Number of sentences to return.
	 * @param boolean|array $strip Whether to strip abbreviations (they break the function).
	 * Pass an array to account for those abbreviations as well. See function body.
	 * @return string Given number of sentences.
	 */
	public static function limitSentences($text, $num, $strip = false) {
		$text = strip_tags($text);
		// shall we strip?
		if ($strip) {
			// brackets are for uniqueness - if we just removed the 
			// dots, then "Mr" would match "Mrs" when we reconvert.
			$replace = array(
				'Dr.' => '<Dr>',
				'Mrs.' => '<Mrs>',
				'Mr.' => '<Mr>',
				'Ms.' => '<Ms>',
				'Co.' => '<Co>',
				'Ltd.' => '<Ltd>',
				'Inc.' => '<Inc>',
			);
			// add extra strings to strip
			if (is_array($strip)) {
				foreach($strip as $s) {
					$replace[$s] = '<'.str_replace('.', '', $s).'>';	
				}
			}
			// replace with placeholders and set the key/value vars
			$text = str_replace(
				$replace_keys = array_keys($replace), 
				$replace_vals = array_values($replace), 
				$text
			);
		}
		// get given number of strings delimited by ".", "!", or "?"
		preg_match('/^([^.!?]*[\.!?]+){0,'.$num.'}/', $text, $match);
		// replace the placeholders with originals
		return $strip ? str_replace($replace_vals, $replace_keys, $match[0]) : $match[0];
	}

	/**
	 * Formats a string by injecting non-numeric characters into 
	 * the string in the positions they appear in the template.
	 *
	 * @param string $string The string to format
	 * @param string $template String format to apply
	 * @return string Formatted string.
	 */
	public static function format($string, $template) {
		$result = ''; 
		$fpos = 0; 
		$spos = 0;
		while ((strlen($template) - 1) >= $fpos) {
			if (ctype_alnum(substr($template, $fpos, 1))) {
				$result .= substr($string, $spos, 1);
				$spos++;
			} else {
				$result .= substr($template, $fpos, 1);
			}
			$fpos++;
		}
		
		return $result;	
	}
		
	/**
	 * Converts a string to a PEAR-like class name. (e.g. "View_Template_Controller")
	 * e.g.
	 * <code>
	 * $str = 'myCamelClass'
	 * $str = pearclass($str); // now 'My_Camel_Class'
	 * </code>
	 */
	public static function pearCase($str) {
		$with_spaces = preg_replace('/[^a-zA-Z0-9]/', '_', trim(preg_replace('/[A-Z]/', ' $0', $str)));
		return preg_replace('/[_]{2,}/', '_', str_replace(' ', '_', ucwords($with_spaces)));
	}
	
	/**
	 * Converts a string to "snake_case"
	 */
	public static function snakeCase($str) {
		return strtolower(static::pearClass($str));
	}
	
	/**
	 * Converts a string to "StudlyCaps"
	 */
	public static function studlyCase($str) {
		return str_replace(' ', '', ucwords(trim(preg_replace('/[^a-zA-Z0-9]/', ' ', $str))));
	}
	
	/**
	 * Converts a string to "camelCase"
	 */
	public static function camelCase($str) {
		return lcfirst(static::studlyCase($str));
	}
	
	/**
	 * If $val is a numeric string, converts to float or integer depending on 
	 * whether a decimal point is present. Otherwise returns original.
	 * 
	 * @param string $val If numeric string, converted to integer or float.
	 * @return scalar Value as string, integer, or float.
	 */
	public static function castNum($str) {
		if (! is_numeric($val) || ! is_string($val)) {
			return $val;
		}
		$decimal = static::getDecimalPoint();
		return (strpos($val, $decimal) === false) ? (int) $val : (float) $val;
	}
	
	/**
	 * Whether Multibyte string extension is loaded.
	 * 
	 * @return boolean
	 */
	public static function mb() {
		isset(static::$mb) or static::$mb = extension_loaded('mbstring');
		return static::$mb;
	}
	
	/**
	 * Returns the decimal point in localized format.
	 * 
	 * @return string
	 */
	public static function getDecimalPoint() {
		if (! isset(static::$loc)) {
			static::$loc = localeconv();
			if (empty(static::$loc['decimal_point'])) {
				static::$loc['decimal_point'] = '.';
			}
		}
		return static::$loc['decimal_point'];
	}
	
	/**
	 * Serialize data, if needed.
	 *
	 * @param mixed $data Data that might be serialized.
	 * @return mixed A scalar data
	 */
	public static function maybeSerialize($data) {
		if (is_array($data) || is_object($data)) {
			return serialize($data);
		}
		return $data;
	}
	
	/**
	 * Unserialize value only if it was serialized.
	 *
	 * @param string $value Possibly serialized value.
	 * @return mixed
	 */
	public static function maybeUnserialize($value) {
		if (static::isSerialized($value)) {
			return unserialize($value);
		}
		return $value;
	}
	
	/**
	 * Obfuscate an email address to prevent spam-bot harvesting.
	 * 
	 * @author wordpress
	 * 
	 * @param string $email Email address.
	 * @param boolean $hex_encode Whether to hex encode some letters. Default false.
	 * @return string Obfuscated email address.
	 */
	public static function antispamEmail($email, $hex_encode = false) {
		$email_address = '';
		$hex_encoding = 1 + (int)(bool)$hex_encode;
		foreach(str_split($email) as $letter) {
			$j = mt_rand(0, $hex_encoding);
			if ($j == 0) {
				$email_address .= '&#'.ord($letter).';';
			} else if ($j == 1) {
				$email_address .= $letter;
			} else if ($j == 2) {
				$email_address .= '%'.sprintf('%02s', dechex(ord($letter)));
			}
		}
		return str_replace('@', '@', $email_address);
	}
	
	/**
	* Returns pretty-printed JSON string.
	*/
	public static function jsonPrettify( $json ){
		$tab = " ";
		$new_json = "";
		$indent_level = 0;
		$in_string = false;
		$len = strlen($json);
		for ($c = 0; $c < $len; $c++) {
			$char = $json[$c];
			switch ($char) {
				case '{':
				case '[':
					if ( !$in_string ){
						$new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
						$indent_level++;
					} else {
						$new_json .= $char;
					}
					break;
				case '}':
				case ']':
					if ( !$in_string ) {
						$indent_level--;
						$new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
					} else {
						$new_json .= $char;
					}
					break;
				case ',':
					if ( !$in_string ){
						$new_json .= ",\n" . str_repeat($tab, $indent_level);
					} else {
						$new_json .= $char;
					}
					break;
				case ':':
					if ( !$in_string ){
						$new_json .= ": ";
					} else {
						$new_json .= $char;
					}
					break;
				case '"':
					if ($c > 0 && $json[$c-1] != '\\'){
						$in_string = !$in_string;
					}
				default:
					$new_json .= $char;
					break;                                        
			}
		}
		return $new_json;	
	}
	
}
