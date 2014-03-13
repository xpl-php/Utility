<?php

namespace Phpf\Util;

class Str {
	
	/**
	 * Use htmlentities() with ENT_COMPAT
	 */
	const ESC_HTML = 'html';
	
	/**
	 * Strip ISO-8851-1 chars (above 128).
	 */
	const ESC_ASCII = 'ascii';
	
	/**
	 * Allow all ISO-8851-1 chars.
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
		
		if ( $flag == self::ESC_HTML ){
			return htmlentities(strip_tags($string), ENT_QUOTES, false);
		}
		
		if ( $flag == self::ESC_ASCII ){
			$flag = FILTER_FLAG_STRIP_HIGH;
		} elseif ( $flag == self::ESC_ISO ){
			$flag = FILTER_FLAG_NONE;
		}
			
		return filter_var($string, FILTER_SANITIZE_STRING, $flag);
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
	 * Strips non-alphanumeric characters from a string.
	 * Add characters to $extras to preserve those as well.
	 * Extra chars should be escaped for use in preg_*() functions.
	 */
	public static function escAlnum( $str, array $extras = null ){
		
		$pattern = '/[^a-zA-Z0-9 ';
		
		if ( !empty($extras) ){
			$pattern .= implode('', $extras);
		}
		
		$pattern .= ']/';
		
		return preg_replace($pattern, '', $str);
	}
		
	/**
	* Returns true if $haystack starts with $needle.
	*/
	public static function startsWith( $haystack, $needle ) {
		return 0 === strpos($haystack, $needle);
	}
	
	/**
	* Returns true if $haystack ends $needle.
	*/
	public static function endsWith( $haystack, $needle ) {
		return $needle === substr($haystack, -strlen($needle));
	}
	
	/**
	 * Returns true if $haystack contains $needle.
	 */
	public static function contains( $haystack, $needle ){
		return false !== strpos($haystack, $needle);
	}
		
	/** 
	 * Returns 1st occurance of text between two strings. 
	 * The "between" strings are not included in output.
	 * @param string $source The string in which to search.
	 * @param string $start The starting string
	 * @param string $end The ending string
	 * @return string Text between $start and $end. 
	 */
	public static function between( $source, $start, $end ){
		$str1 = explode($start, $source);
		$str2 = explode($end, $str1[1]);
		return trim($str2[0]);
	}
	
	/**
	* Strips unescaped unicode characters (e.g. u00a0). 
	* @uses mb_detect_encoding, mb_convert_encoding
	*/
	public static function stripInvalidUnicode( $str ){
		
		$encoding = mb_detect_encoding($str);
		
		if ( 'UTF-8' !== $encoding || 'ASCII' !== $encoding ){
			$str = mb_convert_encoding($str, 'UTF-8');
		}
		
		return stripcslashes(preg_replace('/\\\\u([0-9a-f]{4})/i', '', $str));
	}
	
	/**
	 * Limit string to a given number of sentences.
	 *
	 * @param string $text The full string of sentences.
	 * @param integer $count Number of sentences to return.
	 * @return string Given number of sentences.
	 */
	public static function limitSentences( $text, $count ){
		preg_match('/^([^.!?]*[\.!?]+){0,'. $count .'}/', strip_tags($text), $excerpt);
		return $excerpt[0];
	}

	/**
	 * Formats a string by injecting non-numeric characters into 
	 * the string in the positions they appear in the template.
	 *
	 * @param string $string The string to format
	 * @param string $template String format to apply
	 * @return string Formatted string.
	 */
	public static function format( $string, $template ){
		
		$result = ''; $fpos = 0; $spos = 0;
		
		while ( (strlen($template) - 1) >= $fpos ){
			
			if ( ctype_alnum(substr($template, $fpos, 1)) ){
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
	 * Formats a phone number based on string length.
	 */
	public static function formatPhone( $phone ){
			
		// remove any pre-existing formatting characters
		$string = str_replace(array('(',')','+','-',' '), '', $phone);
		
		switch( strlen($string) ){
			case 7:
				$tmpl = '000-0000';
				break;
			case 10:
				$tmpl = '(000) 000-0000';
				break;
			case 11:
				$tmpl = '+0 (000) 000-0000';
				break;
			case 12:
				$tmpl = '+00 00 0000 0000';
				break;
		}
		
		return self::format($string, $tmpl);
	}
	
	/**
	 * Formats a hash/digest based on string length.
	 */
	public static function formatHash( $hash ){
		
		// remove any pre-existing formatting characters
		$string = str_replace(array('(',')','+','-',' '), '', $hash);
		
		switch( strlen($string) ){
			case 16:
				$tmpl = '00000000-0000-0000';
				break;
			case 24:
				$tmpl = '00000000-0000-0000-00000000';
				break;
			case 32:
				$tmpl = '00000000-0000-0000-0000-000000000000';
				break;
			case 40:
				$tmpl = '00000000-0000-0000-00000000-0000-0000-00000000';
				break;
			case 48:
				$tmpl = '00000000-0000-00000000-0000-0000-00000000-0000-00000000';
				break;
		}
		
		return self::format($string, $tmpl);
	}
		
	/**
	* Generate a random string from one of several of character pools.
	*
	* @param int $length Length of the returned random string (default 16)
	* @param string $type The type of characters to use to generate string.
	* @return string A random string
	*/
	public static function rand( $length = 16, $pool_type = 'alnum' ){
		$str = '';
		
		switch ( $pool_type ) {
			case 'alnum':
			case 'alphanumeric':
			default:
				$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case 'complex':
				$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
				break;
			case 'salt':
				$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_[]{}<>~`+=,.;:/?';
				break;
			case 'hexdec':
			case 'hexadecimal':
				$pool = '0123456789abcdef';
				break;
			case 'numeric':
			case 'num':
				$pool = '0123456789';
				break;
			case 'alpha':
				$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			case 'nonzero':
				$pool = '123456789';
				break;
			case 'distinct':
				$pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
				break;
			case 'allchars':
				$pool = '~!@#$%^&*()_-+=[]{};:,<.>/?';
				break;
			case 'chars':
				$pool = '!@#$%^&*()';
				break;
			case 'extrachars':
			case 'chars2':
				$pool = '-_ []{}<>~`+=,.;:/?';
				break;
		}
		
		for ( $i=0; $i < $length; $i++ ){
			$str .= substr( $pool, mt_rand(0, strlen($pool) - 1), 1 );
		}
		
		return $str;	
	}
	
	/**
	 * Serialize data, if needed.
	 *
	 * @param mixed $data Data that might be serialized.
	 * @return mixed A scalar data
	 */
	public static function maybeSerialize( $data ) {
		if ( is_array($data) || is_object($data) )
			return serialize($data);
		return $data;
	}
	
	/**
	 * Unserialize value only if it was serialized.
	 *
	 * @param string $value Maybe unserialized original, if is needed.
	 * @return mixed Unserialized data can be any type.
	 */
	public static function maybeUnserialize( $value ) {
		if ( self::isSerialized($value) )
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
	public static function isSerialized( $data, $strict = true ) {
			
		if ( ! is_string($data) )
			return false;
		
		$data = trim($data);
	 	
	 	if ( 'N;' == $data ) 
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
	
	/**
	 * Writes an array of data as CSV to a file.
	 */
	public static function writeCsv( array $data, $filepath ){
	    
	    if ( !is_writable($filepath) ){
	    	throw new \InvalidArgumentException("File given is not writable - $filepath.");
	    }
		
	    $output = fopen($filepath, "w");
	    
	    foreach ($data as $row) {
	        fputcsv($output, $row);
	    }
	    
	    fclose($output);
	}
		
	/**
	 * Converts a string to a PEAR-like class name. (e.g. "View_Template_Controller")
	 * e.g.
	 * <code>
	 * $str = 'myCamelClass'
	 * $str = pearclass($str); // now 'My_Camel_Class'
	 * </code>
	 */
	public static function pearClass( $str ){
		$strWithSpaces = self::escAlnum( trim(preg_replace('/[A-Z]/', ' $0', $str)) );
		return str_replace(' ', '_', ucwords($strWithSpaces));
	}
	
	/**
	 * Converts a string to "snake_case"
	 */
	public static function snakeCase( $str ){
		return strtolower( self::pearClass($str) );
	}
	
	/**
	 * Converts a string to "StudlyCaps"
	 */
	public static function studlyCaps( $str ){
		return str_replace(' ', '', ucwords( trim(preg_replace('/[^a-zA-Z]/', ' ', $str)) ));
	}
	
	/**
	 * Converts a string to "camelCase"
	 */
	public static function camelCase( $str ){
		return lcfirst( self::studlyCaps($str) );
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
		for ( $c = 0; $c < $len; $c++ ){
			$char = $json[$c];
			switch ($char){
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
