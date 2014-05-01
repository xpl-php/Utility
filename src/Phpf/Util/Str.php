<?php

namespace Phpf\Util;

class Str {
	
	/**
	 * Use htmlentities() with ENT_COMPAT
	 * most aggressive
	 * @var string
	 */
	const ESC_HTML = 'html';
	
	/**
	 * Strip ISO-8851-1 chars (ASCII >= 128).
	 * @var string
	 */
	const ESC_ASCII = 'ascii';
	
	/**
	 * Allow all ISO-8851-1 chars.
	 * least aggressive
	 * @var string
	 */
	const ESC_ISO = 'iso';
	
	/**
	 * Escape a string using filter_var
	 * 
	 * @param string $string The string to sanitize.
	 * @param bool $flags Flags = strip non-ASCII chars
	 * @return string Sanitized string.
	 */
	public static function esc( $string, $flag = self::ESC_ASCII ){
		
		preg_replace('/[\x00-\x08\x0B-\x1F]/', '', $string);
		
		if ($flag === self::ESC_HTML) {
			return htmlspecialchars($string, ENT_QUOTES, false);
		}
		
		if ($flag === self::ESC_ASCII) {
			$flag = FILTER_FLAG_STRIP_HIGH;
		} elseif ($flag === self::ESC_ISO) {
			$flag = FILTER_FLAG_NONE;
		}
		
		return filter_var($string, FILTER_SANITIZE_STRING, $flag);
	}
		
	/**
	 * Strips non-alphanumeric characters from a string.
	 * 
	 * Add characters to $extras to preserve those as well.
	 * Extra chars should be escaped for use in preg_*() functions.
	 * 
	 * @param string $string String to escape.
	 * @param array|null $extras Other characters to strip.
	 * @return string Escaped string.
	 */
	public static function escAlnum($string, array $extras = null) {
		if (! isset($extras) && ctype_alnum($string)) {
			return $string;
		}
		$pattern = '/[^a-zA-Z0-9'. (isset($extras) ? $extras : '') .']/';
		return preg_replace($pattern, '', $string);
	}
			
	/**
	 * Escapes text for SQL LIKE special characters % and _.
	 *
	 * @param string $text The text to be escaped.
	 * @return string text, safe for inclusion in LIKE query.
	 */
	public static function escSqlLike( $text ) {
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
	 * Returns 1st occurance of text between two strings.
	 * 
	 * The "between" strings are not included in output.
	 * 
	 * @param string $source The string in which to search.
	 * @param string $start The starting string
	 * @param string $end The ending string
	 * @return string Text between $start and $end. 
	 */
	public static function between($str, $start, $end) {
		$str1 = explode($start, $str);
		$str2 = explode($end, $str1[1]);
		return trim($str2[0]);
	}
	
	/**
	* Strips unescaped unicode characters (e.g. u00a0). 
	* @uses mb_detect_encoding, mb_convert_encoding
	*/
	public static function stripInvalidUnicode($str) {
		
		$encoding = mb_detect_encoding($str);
		
		if ('UTF-8' !== $encoding && 'ASCII' !== $encoding) {
				
			$mbsub = ini_get('mbstring.substitute_character');
			
			ini_set('mbstring.substitute_character', "none");
			
	  		$str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
	  		
			ini_set('mbstring.substitute_character', $mbsub);
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
	public static function pearClass($str) {
		$strWithSpaces = static::escAlnum(trim(preg_replace('/[A-Z]/', ' $0', $str)));
		return str_replace(' ', '_', ucwords($strWithSpaces));
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
	public static function studlyCaps($str) {
		return str_replace(' ', '', ucwords(trim(preg_replace('/[^a-zA-Z]/', ' ', $str))));
	}
	
	/**
	 * Converts a string to "camelCase"
	 */
	public static function camelCase($str) {
		return lcfirst(static::studlyCaps($str));
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
		for ( $c = 0; $c < $len; $c++ ) {
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

	/**
	 * Serialize data, if needed.
	 *
	 * @param mixed $data Data that might be serialized.
	 * @return mixed A scalar data
	 */
	public static function maybeSerialize($data) {
		if (is_array($data) || is_object($data))
			return serialize($data);
		return $data;
	}
	
	/**
	 * Unserialize value only if it was serialized.
	 *
	 * @param string $value Maybe unserialized original, if is needed.
	 * @return mixed Unserialized data can be any type.
	 */
	public static function maybeUnserialize($value) {
		if (self::isSerialized($value))
			return @unserialize($value);
		return $value;
	}
	
	/**
	 * Check value to find if it was serialized.
	 *
	 * @param mixed $data Value to check to see if was serialized.
	 * @param bool $strict Optional. Whether to be strict about the end of the string. Defaults true.
	 * @return bool False if not serialized and true if it was.
	 */
	public static function isSerialized($data, $strict = true) {
			
		if (! is_string($data))
			return false;
		
		$data = trim($data);
	 	
	 	if ('N;' == $data) 
	 		return true; // serialized null
		
		$length = strlen($data);
		
		if ( $length < 4 || ':' !== $data[1] )
			return false; // no datatype char
		
		if ( $strict ) {
			$lastc = $data[$length-1];
			if ( ';' !== $lastc && '}' !== $lastc )
				return false;
		} else {
			$semicolon = strpos($data, ';');
			$brace     = strpos($data, '}');
			// Either ; or } must exist, but neither 
			// must be in the first X characters.
			if ( (false === $semicolon && false === $brace)
				|| (false !== $semicolon && $semicolon < 3)
				|| (false !== $brace && $brace < 4) ) 
			{
				return false;
			}
		}
		
		$token = $data[0];
		
		switch ($token){
			case 's' :
				if ( ($strict && '"' !== $data[$length-2]) || false === strpos($data, '"') ){
					return false;
				}
				// or else fall through
			case 'a' :
			case 'O' :
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b' :
			case 'i' :
			case 'd' :
				$end = $strict ? '$' : '';
				return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
		}
		
		return false;
	}
	
}
