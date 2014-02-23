<?php
/**
 * @package Phpf.Util
 * @subpackage functions
 */

/** ==============================
		Strings/Arrays
=============================== */

/**
 * Generates a random string with given number of bytes.
 * If $strong = true (default), must use one of:
 * 		openssl_random_pseudo_bytes() PHP >= 5.3.4
 * 		mcrypt_create_iv() PHP >= 5.3.7
 * 		/dev/urandom
 * 		mt_rand()
 */
function str_rand_bytes( $length = 12, $strong = true ){

	if ( function_exists('openssl_random_pseudo_bytes') && version_compare(PHP_VERSION, '5.3.4') >= 0 ){
        $bytes = openssl_random_pseudo_bytes($length, $usable);
		if (true === $usable) {
            return $bytes;
        }
    }
	
	if ( function_exists('mcrypt_create_iv') && version_compare(PHP_VERSION, '5.3.7') >= 0 ){
        $bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
		if ( $bytes !== false && $length === strlen($bytes) ) {
            return $bytes;
        }
    }
	
	$buffer = '';
	$bl = strlen($buffer);
    
	if ( @is_readable('/dev/urandom') ){
	    $f = fopen('/dev/urandom', 'r');
	    while ($bl < $length) {
	        $buffer .= fread($f, $length - $bl);
	        $bl = strlen($buffer);
	    }
	    fclose($f);
	} else {
		for ($i = 0; $i < $length; $i++) {
			if ($i < $bl) {
	            $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
	        } else {
	            $buffer .= chr(mt_rand(0, 255));
	        }
	    }
	}
	
	if ( $length === strlen($buffer) ){
		return $buffer;
	} elseif ( true === $strong ){
        throw new RuntimeException('Unable to generate sufficiently strong random bytes - no source with sufficient entropy.');
    }
	
	$result = '';
    $entropy = '';
    $msec_per_round = 100;
    $bits_per_round = 2;
    $total = $length;
    $bytes = 0;
    $hash_length = 20;
    $rounds = 0;
    while (strlen($result) < $length) {
        $bytes = ($total > $hash_length)? $hash_length : $total;
        $total -= $bytes;
        for ($i=1; $i < 3; $i++) {
            $t1 = microtime(true);
            $seed = mt_rand();
            for ($j=1; $j < 50; $j++) {
                $seed = sha1($seed);
            }
            $t2 = microtime(true);
            $entropy .= $t1 . $t2;
        }
        $divisor = (int) (($t2 - $t1) * 1000000);
        if ($divisor == 0) {
            $divisor = 400;
        }
        $rounds = (int) ($msec_per_round * 50 / $divisor);
        $iter = $bytes * (int) (ceil(8 / $bits_per_round));
        for ($i = 0; $i < $iter; $i ++)
        {
            $t1 = microtime();
            $seed = sha1(mt_rand());
            for ($j = 0; $j < $rounds; $j++)
            {
               $seed = sha1($seed);
            }
            $t2 = microtime();
            $entropy .= $t1 . $t2;
        }
        $result .= sha1($entropy, true);
    }
    return substr($result, 0, $length);
}

/**
* Generate a random string from one of several of character pools.
*
* @param int $length Length of the returned random string (default 16)
* @param string $type The type of characters to use to generate string.
* @return string A random string
*/
function str_rand( $length = 16, $pool_type = 'alnum' ){
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
 * Generate a UUID
 * 32 characters (a-f and 0-9) in format 8-4-4-12.
 */
function generate_uuid(){
	return format_hash(str_rand(32, 'hexdec'));
}

/**
 * Generates a 32-byte base64-encoded random string.
 */
function generate_crsf_token(){
    return base64_encode(str_rand(32));
}

/**
 * Generates a verifiable token from seed.
 */
function generate_token( $seed, $algo = 'sha1' ){
	return hash_hmac($algo, $seed, '1<Kjia6~?qxg*|!RLg<E!*TwB%yq)Fa77O:F))>%>Lp/vw-T1QF!Qm6rFWz1X3bQ');
}

/**
 * Verifies a token using seed.
 */
function verify_token( $token, $seed, $algo = 'sha1' ){
	return $token === generate_token($seed, $algo);
}

/**
 * Formats a phone number based on string lenth.
 */
function format_phone( $phone ){
		
	// remove any pre-existing formatting characters
	$string = str_replace(array('(',')','+','-',' '), '', $phone);
	
	switch( strlen( $string ) ){
		case 7:
			$tmpl = '000-0000';
			break 2;
		case 10:
			$tmpl = '(000) 000-0000';
			break 2;
		case 11:
			$tmpl = '+0 (000) 000-0000';
			break 2;
		case 12:
			$tmpl = '+00 00 0000 0000';
			break 2;
	}
	
	return str_format($string, $tmpl);
}

/**
 * Formats a hash/digest based on string length.
 */
function format_hash( $hash ){
	
	// remove any pre-existing formatting characters
	$string = str_replace(array('(',')','+','-',' '), '', $hash);
	
	switch( strlen( $string ) ){
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
	
	return str_format($string, $tmpl);
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
	
	$result = ''; $fpos = 0; $spos = 0;
	
	while ( (strlen($template) - 1) >= $fpos ){
		
		if ( ctype_alnum( substr($template, $fpos, 1) ) ){
			$result .= substr( $string, $spos, 1 );
			$spos++;
		} else {
			$result .= substr( $template, $fpos, 1 );
		}
		
		$fpos++;
	}
	
	return $result;	
}

/**
 * Strips non-alphanumeric characters from a string.
 * Add characters to $extras to preserve those as well.
 * Extra chars should be escaped for use in preg_*() functions.
 */
function esc_alnum( $str, array $extras = null ){
	
	$pattern = '/[^a-zA-Z0-9 ';
	
	if ( ! empty($extras) ){
		$pattern .= implode('', $extras);
	}
	
	$pattern .= ']/';
	
	return preg_replace($pattern, '', $str);
}

/**
 * Do nothing to ASCII chars.
 */
define('ESC_STR_NONE', 0);
/**
 * Strip low and high ASCII chars.
 */
define('ESC_STR_STRIP', 1);
/**
 * Encode low and high ASCII chars.
 */
define('ESC_STR_ENCODE', 2);

/**
 * Escape a string using fairly aggressive rules.
 * Strips all tags and converts to html entities.
 * 
 * @param string $string The string to sanitize.
 * @param bool $encode Whether to encode or strip high & low ASCII chars. (default: false = strip)
 * @return string Sanitized string.
 */
function esc_str( $string, $flag = ESC_STR_STRIP ){
	
	$str = htmlentities( strip_tags($string), ENT_COMPAT, 'UTF-8' );
	
	preg_replace( '/[\x00-\x08\x0B-\x1F]/', '', $str );
	
	$str = str_replace( array('javascript:', 'document.write'), '', $str );
	
	switch( $flag ){
		
		case ESC_STR_STRIP:
		default:
			$flags = FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH;
			break;
		
		case ESC_STR_ENCODE:
			$flags = FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_ENCODE_HIGH;
			break;
		
		case ESC_STR_NONE:
			$flags = FILTER_FLAG_NONE;
			break;
	}
		
	return filter_var($str, FILTER_SANITIZE_STRING, $flags);
}

/**
 * Escapes text for SQL LIKE special characters % and _.
 *
 * @param string $text The text to be escaped.
 * @return string text, safe for inclusion in LIKE query.
 */
function esc_sql_like( $text ) {
	return str_replace(array("%", "_"), array("\\%", "\\_"), $text);
}

/**
 * Converts a string to a PEAR-like class name. (e.g. "View_Template_Controller")
 * e.g.
 * <code>
 * $str = 'myCamelClass'
 * $str = pearclass($str); // now 'My_Camel_Class'
 * </code>
 */
function pearclass( $str ){
	$strWithSpaces = esc_alnum( trim(preg_replace('/[A-Z]/', ' $0', $str)) );
	return str_replace(' ', '_', ucwords($strWithSpaces));
}

/**
 * Converts a string to "snake_case"
 */
function snakecase( $str ){
	return strtolower( pearclass($str) );
}

/**
 * Converts a string to "StudlyCaps"
 */
function studlycaps( $str ){
	return str_replace(' ', '', ucwords( trim(preg_replace('/[^a-zA-Z]/', ' ', $str)) ));
}

/**
 * Converts a string to "camelCase"
 */
function camelcase( $str ){
	return lcfirst( studlycaps($str) );
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

/**
* Retrieves a value from $array given its path in dot notation
*/
function array_get( array &$array, $dotpath ) {
	
	if ( false === strpos( $dotpath, '.' ) )
		return isset( $array[ $dotpath ] ) ? $array[ $dotpath ] : null;
	
	$loc =& $array;
	
	foreach( explode('.', $dotpath) as $step ){
		if ( isset( $loc[ $step ] ) )
			$loc =& $loc[ $step ];
	}
	
	return $loc;
}

/**
* Sets a value in $array given its path in dot notation.
*/
function array_set( array &$array, $dotpath, $value ){
	
	if ( false === strpos( $dotpath, '.' ) )
		return $array[ $dotpath ] = $value;
	
	$loc =& $array;
	
	foreach( explode('.', $dotpath) as $step )
		$loc =& $loc[ $step ];
	
	return $loc = $value;
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
	if ( is_object($args) )
		$r = get_object_vars($args);
	elseif ( is_array($args) )
		$r =& $args;
	else
		parse_str($args, $r);

	if ( is_array($defaults) )
		return array_merge($defaults, $r);
	return $r;
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
	if ( ! is_array($list) )
		return array();

	if ( empty($args) )
		return $list;

	$operator = strtoupper($operator);
	$count = count($args);
	$filtered = array();

	foreach ( $list as $key => $obj ) {
		$to_match = (array) $obj;
		$matched = 0;
		
		foreach ( $args as $m_key => $m_value ) {
			if ( array_key_exists($m_key, $to_match) ){
				/**
				* Only match keys if $keys_exist_only = true
				* @since 2/8/14
				*/ 
				if ( $m_value == $to_match[ $m_key ] || $keys_exist_only )
					$matched++;
			}
		}

		if ( ( 'AND' == $operator && $matched == $count )
		  || ( 'OR' == $operator && $matched > 0 )
		  || ( 'NOT' == $operator && 0 == $matched ) )
		{
			$filtered[ $key ] = $obj;
		}
	}

	return $filtered;
}

/**
 * Pluck a certain field out of each object in a list.
 *
 * @param array $list A list of objects or arrays
 * @param int|string $field A field from the object to place instead of the entire object
 * @return array
 */
function list_pluck( $list, $field ) {
	
	foreach ( $list as $key => $value ) {
		
		if ( is_object( $value ) )
			$list[ $key ] = $value->$field;
		else
			$list[ $key ] = $value[ $field ];
	}

	return $list;
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
	
	foreach ( $list as &$value ) {
		
		if ( is_object( $value ) )
			$value = $value->$field;
		else
			$value = $value[ $field ];
	}

	return $list;
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
		Request Headers
=============================== */

/** 
* Returns HTTP request headers as array.
*/
function get_request_headers(){
	static $headers;
	if ( isset($headers) ) {// get once per request
		return $headers;
	}
	if ( function_exists('apache_request_headers') ){
		$_headers = apache_request_headers();
	} elseif ( extension_loaded('http') ){
		$_headers = http_get_request_headers();
	} else { // Manual labor
		$_headers = array();
		$misfits = array('CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5', 'PHP_AUTH_USER', 'PHP_AUTH_PW', 'PHP_AUTH_DIGEST', 'AUTH_TYPE');
		foreach ( $_SERVER as $key => $value ) {
			if ( 0 === strpos( $key, 'HTTP_' ) ){
				$_headers[ $key ] = $value;
			} elseif ( in_array( $key, $misfits ) ){
				$_headers[ $key ] = $value;
			}
		}
	}
	// Normalize header keys
	$headers = array();
	foreach ( $_headers as $key => $value ) {
		$key = str_replace('http-', '', str_replace('_', '-', strtolower($key)));
		$headers[ $key ] = $value;
	}
	
	return $headers;
}
	
/**
* Returns a single HTTP request header if set.
*/
function get_request_header( $name ){
	$headers = get_request_headers();
	$name = str_replace('_', '-', strtolower($name));
	return isset( $headers[ $name ] ) ? $headers[ $name ] : null;
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

