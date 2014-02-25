<?php
/**
 * @package Phpf.Util
 * @subpackage functions
 */

use Phpf\Util\Http;
use Phpf\Util\Str;
use Phpf\Util\Security;
use Phpf\Util\Arr;

/** ==============================
		Request Headers
=============================== */

/** 
* Returns HTTP request headers as array.
*/
function get_request_headers(){
	return Http::getRequestHeaders();
}
	
/**
* Returns a single HTTP request header if set.
*/
function get_request_header( $name ){
	return Http::getRequestHeader($name);
}

/** ======================
		Strings
======================= */

/**
 * Converts a string to a PEAR-like class name. (e.g. "View_Template_Controller")
 */
function pearclass( $str ){
	return Str::pearClass($str);
}

/**
 * Converts a string to "snake_case"
 */
function snakecase( $str ){
	return Str::snakeCase($str);
}

/**
 * Converts a string to "StudlyCaps"
 */
function studlycaps( $str ){
	return Str::studlyCaps($str);
}

/**
 * Converts a string to "camelCase"
 */
function camelcase( $str ){
	return Str::camelCase($str);
}

/**
 * Escape a string using fairly aggressive rules.
 * Strips all tags and converts to html entities.
 * 
 * @param string $string The string to sanitize.
 * @param bool $encode Whether to encode or strip high & low ASCII chars. (default: false = strip)
 * @return string Sanitized string.
 */
function esc_str( $string, $flag = Str::ESC_STRIP ){
	return Str::esc($string, $flag);
}

/**
 * Strips non-alphanumeric characters from a string.
 * Add characters to $extras to preserve those as well.
 * Extra chars should be escaped for use in preg_*() functions.
 */
function esc_alnum( $str, array $extras = null ){
	return Str::escAlnum($str, $extras);
}

/**
 * Escapes text for SQL LIKE special characters % and _.
 *
 * @param string $text The text to be escaped.
 * @return string text, safe for inclusion in LIKE query.
 */
function esc_sql_like( $string ) {
	return Str::escSqlLike($string);
}

/**
 * Formats a phone number based on string lenth.
 */
function format_phone( $phone ){
	return Str::formatPhone($phone);
}

/**
 * Formats a hash/digest based on string length.
 */
function format_hash( $hash ){
	return Str::formatHash($hash);
}

/**
 * Formats a string by injecting non-numeric characters into 
 * the string in the positions they appear in the template.
 *
 * @param string $string The string to format
 * @param string $template String format to apply
 * @return string Formatted string.
 */
function str_format( $string, $template ){
	return Str::format($string, $template);
}

/**
 * Generates a random string with given number of bytes.
 * If $strong = true (default), must use one of:
 * 		openssl_random_pseudo_bytes() PHP >= 5.3.4
 * 		mcrypt_create_iv() PHP >= 5.3.7
 * 		/dev/urandom
 * 		mt_rand()
 */
function str_rand_bytes( $length = 12, $strong = true ){
	return Str::randBytes($length, $strong);
}

/**
* Generate a random string from one of several of character pools.
*
* @param int $length Length of the returned random string (default 16)
* @param string $type The type of characters to use to generate string.
* @return string A random string
*/
function str_rand( $length = 16, $pool_type = 'alnum' ){
	return Str::rand($length, $pool_type);
}

/**
 * Generates a verifiable token from seed.
 */
function generate_token( $seed, $algo = Security::DEFAULT_HASH_ALGO ){
	return Security::generateToken($seed, $algo);
}

/**
 * Verifies a token using seed.
 */
function verify_token( $token, $seed, $algo = Security::DEFAULT_HASH_ALGO ){
	return Security::verifyToken($token, $seed, $algo);
}

/**
 * Generate a UUID
 * 32 characters (a-f and 0-9) in format 8-4-4-12.
 */
function generate_uuid(){
	return Security::generateUuid();
}

/**
 * Generates a 32-byte base64-encoded random string.
 */
function generate_crsf_token(){
    return Security::generateCsrfToken();
}

/** ====================
		Arrays
===================== */

/**
* Retrieves a value from $array given its path in dot notation
*/
function array_get( array &$array, $dotpath ) {
	return Arr::dotGet($array, $dotpath);
}

/**
* Sets a value in $array given its path in dot notation.
*/
function array_set( array &$array, $dotpath, $value ){
	return Arr::dotSet($array, $dotpath, $value);
}

/**
 * Merge user defined arguments into defaults array.
 *
 * This function is used throughout WordPress to allow for both string or array
 * to be merged into another array.
 *
 * @param string|array $args Value to merge with $defaults
 * @param array $defaults Array that serves as the defaults.
 * @return array Merged user defined values with defaults.
 */
function parse_args( $args, $defaults = '' ) {
	return Arr::parse($args, $defaults);
}

/**
 * Filters a list of objects, based on a set of key => value arguments.
 *
 * @param array $list An array of objects to filter
 * @param array $args An array of key => value arguments to match against each object
 * @param string $operator The logical operation to perform:
 *    'AND' means all elements from the array must match;
 *    'OR' means only one element needs to match;
 *    'NOT' means no elements may match.
 *   The default is 'AND'.
 * @return array
 */
function list_filter( $list, $args = array(), $operator = 'AND', $keys_exist_only = false ) {
	return Arr::filter($list, $args, $operator, $keys_exist_only);
}

/**
 * Pluck a certain field out of each object in a list.
 *
 * @param array $list A list of objects or arrays
 * @param int|string $field A field from the object to place instead of the entire object
 * @return array
 */
function list_pluck( $list, $field ) {
	return Arr::pluck($list, $field);
}

/**
 * Pluck a certain field out of each object in a list by reference.
 * This will change the values of the original array/object list.
 *
 * @param array $list A list of objects or arrays
 * @param int|string $field A field from the object to place instead of the entire object
 * @return array
 */
function list_pluck_ref( &$list, $field ){
	return Arr::pluckRef($list, $field);
}

/**
 * Attempts to convert given value to a scalar value.
 * 
 * If $val is a file path, returns file contents using get_file_contents_clean().
 * If $val is any other other scalar value, returns as-is.
 * If $val is a callable, returns result if scalar.
 * If $val is an object, tries to call __toString() and returns result if scalar.
 * If none of the above worked, returns empty string.
 */
function scalarize( $val ){
	
	if ( is_scalar($val) ){
		return is_file($val) ? @get_file_contents_clean($val) : $val;
	}
	
	if ( is_callable($val) ){
		$value = call_user_func($val);
		return is_scalar($value) ? $value : '';
	}
	
	if ( is_object($val) ){
		
		try {
			$value = $val->__toString();
		} catch(Exception $e){
			$value = '';
		}
		
		return $value;
	}
	
	return '';
}

/**
 * Converts a comma-separated value string to array.
 * Trims whitespace from each array item.
 */
function csv_to_array( $csvStr ){
	return array_map('trim', explode(',', $csvStr));
}

/** ==============================
		Serialization
=============================== */

/**
 * Check value to find if it was serialized.
 *
 * If $data is not an string, then returned value will always be false.
 *
 * @param mixed $data Value to check to see if was serialized.
 * @param bool $strict Optional. Whether to be strict about the end of the string. Defaults true.
 * @return bool False if not serialized and true if it was.
 */
function is_serialized( $data, $strict = true ) {
	if ( !is_string($data) )
		return false;
	$data = trim($data);
 	if ( 'N;' == $data )
		return true; // null
	$length = strlen($data);
	if ( $length < 4 || ':' !== $data[1] )
		return false; // no datatype char
	if ( $strict ) {
		$lastc = $data[$length-1];
		if ( ';' !== $lastc && '}' !== $lastc )
			return false;
	} else {
		$semicolon = strpos( $data, ';' );
		$brace     = strpos( $data, '}' );
		// Either ; or } must exist, but neither 
		// must be in the first X characters.
		if (	(	false === $semicolon && false === $brace	)
			||	(	false !== $semicolon && $semicolon < 3		)
			||	(	false !== $brace && $brace < 4				) 
		){
			return false;
		}
	}
	$token = $data[0];
	switch ($token){
		case 's' :
			if ($strict) {
				if ('"' !== $data[$length-2])
					return false;
			} elseif (false === strpos($data, '"')){
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
 * Unserialize value only if it was serialized.
 *
 * @param string $value Maybe unserialized original, if is needed.
 * @return mixed Unserialized data can be any type.
 */
function maybe_unserialize( $value ) {
	if ( is_serialized($value) )
		return @unserialize($value);
	return $value;
}

/**
 * Serialize data, if needed.
 *
 * @param mixed $data Data that might be serialized.
 * @return mixed A scalar data
 */
function maybe_serialize( $data ) {
	if ( is_array($data) || is_object($data) )
		return serialize($data);
	return $data;
}

/** ==============================
		Function calling
=============================== */

/**
 * Calls callables faster, most of the time.
 * If callable is a closure, function (string), or an array with 
 * an object and method, it is called directly.
 * Otherwise, calls call_user_func_array().
 */
function call( $callback, array $args = array(), $check_callable = false ){
		
	if ( $check_callable && !is_callable($callback) ){
		throw new Exception("Uncallable function given to call().");
	}
	
	if ( is_array($callback) ){
		
		if ( !is_object($callback[0]) ){
			return call_user_func_array(array($callback[0], $callback[1]), $args);
		}
		
		return call_object($callback[0], $callback[1], $args);
	}
	
	switch( count($args) ){
		case 0:
			return $callback();
		case 1:
			return $callback( $args[0] );
		case 2:
			return $callback( $args[0], $args[1] );
		case 3:
			return $callback( $args[0], $args[1], $args[2] );
		case 4:
			return $callback( $args[0], $args[1], $args[2], $args[3] );
		case 5:
			return $callback( $args[0], $args[1], $args[2], $args[3], $args[4] );
		default:
			return call_user_func_array( $callback, $args );
	}
}

/**
 * Calls object method directly.
 */
function call_object( $object, $method, array $args = array() ){
	
	if ( !is_object($object) ){
		throw new Exception('call_object() requires object - '. gettype($object) .' given.');
	}
	
	switch( count($args) ){
		case 0:
			return $object->$method();
		case 1:
			return $object->$method( $args[0] );
		case 2:
			return $object->$method( $args[0], $args[1] );
		case 3:
			return $object->$method( $args[0], $args[1], $args[2] );
		case 4:
			return $object->$method( $args[0], $args[1], $args[2], $args[3] );
		case 5:
			return $object->$method( $args[0], $args[1], $args[2], $args[3], $args[4] );
		default:
			return call_user_func_array( array($object, $method), $args );
	}
}

// calls a callback statically.
function call_static( $callback, array $args = array() ){
	
	if ( is_array($callback) && is_object($callback[0]) ){
		$callback[0] = get_class($callback[0]);
	}
	
	return call( $callback, $args );
}

function invoke_closure( Closure $closure, array $params = array() ){
    	
    // treat as a function; cf. https://bugs.php.net/bug.php?id=65432
    $reflect = new ReflectionFunction($closure);
    
    // sequential arguments when invoking
    $args = array();
    
    // match params with arguments
    foreach ($reflect->getParameters() as $i => $param) {
        if (isset($params[$param->name])) {
            // a named param value is available
            $args[] = $params[$param->name];
        } elseif (isset($params[$i])) {
            // a positional param value is available
            $args[] = $params[$i];
        } elseif ($param->isDefaultValueAvailable()) {
            // use the default value
            $args[] = $param->getDefaultValue();
        } else {
            // no default value and no matching param
            $message = "Closure($i : \${$param->name})";
            throw new Exception($message);
        }
    }
    
	return $reflect->invokeArgs($args);
}

/** ==============================
		Directories/Files
=============================== */

/**
* Return directories and files in a directory.
*/
function scan( $dir ){
	return array_diff(scandir(rtrim($dir,'/\\') . '/'), array('..', '.'));	
}

/**
 * Returns directories in a given directory.
 * Faster than using scandir() with is_dir()
 * because we only check if there's a dot (".")
 * in the path, indicating a file extension.
 */
function dirs( $dir ){
	
	$dirs = array();
	
	foreach( scandir(rtrim($dir,'/\\') . '/') as $item ){
		if (false === strpos($item, '.')){
			$dirs[] = $item;
		}
	}
	
	return $dirs;
}

/**
 * Returns file contents string, executing PHP using $vars 
 * as local variables (@uses extract())
 * 
 * @param string $__FILE__ Path to file
 * @param array $vars Assoc. array of variables to localize.
 * @return string File contents.
 */
function get_file_contents_clean( $__FILE__, array $vars = array() ){
	
	ob_start();
	
	extract($vars, EXTR_REFS);

	unset($vars);
	
	include $__FILE__;
	
	return ob_get_clean();
}

/**
* Returns absolute path from relative path where called.
*/
function abspath( $relative_path ){
	return dirname(func_file_where_called()) . '/' . trim($relative_path, '/\\');
}

/**
* Returns file where the calling function was called.
* e.g. myfunc.php:
*		function myfunc(){
*			return func_file_where_called( 0 );
*		}
* 	a.php:
*		function yourfunc(){
*			return myfunc();	
*		}
*	b.php:
*		echo yourfunc(); // prints full path to b.php
*/
function func_file_where_called($offset=0){
	$trace = debug_backtrace(false);
	return $trace[1+$offset]['file'];
}

