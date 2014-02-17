<?php
/**
 * @package Wells.Util
 * @subpackage functions
 */

/** ==============================
		Strings/Arrays
=============================== */

/**
 * Strips non-alphanumeric characters from a string.
 * Add characters to $extras to preserve those as well.
 * Extra chars should be escaped for use in preg_*() functions.
 */
function sanitize_alnum( $str, array $extras = null ){
	
	$pattern = '/[^a-zA-Z0-9 ';
	
	if ( ! empty($extras) ){
		$pattern .= implode('', $extras);
	}
	
	$pattern .= ']/';
	
	return preg_replace($pattern, '', $str);
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
	$strWithSpaces = sanitize_alnum( trim(preg_replace('/[A-Z]/', ' $0', $str)) );
	return str_replace(' ', '_', ucwords( $strWithSpaces ));
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
* Converts a comma-separated value string (e.g. "one, two, three, five") to array.
*/
function csv_to_array($csvStr){
	return array_map( 'trim', explode( ',', $csvStr ) );
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
* Parse an .ini file into an array, nesting with '.'
*
* @param string $file .ini file
* @param bool $show_sections Show .ini sections
* @return array Data
*/
function parse_ini_file_to_array( $file, $show_sections = false ){
	
	$config = array();
	$data = parse_ini_file( $file, $show_sections );
	
	foreach ($data as $k => $v) {
		
		$ks = array_reverse( explode('.', $k) );
		
		foreach ($ks as $kss) {
			$v = array($kss => $v);
		}
		
		$config[] = $v;
	}

	return (0 === count($config)) ? array() : call_user_func_array('array_replace_recursive', $config);
}

/** ==============================
		Function calling
=============================== */

/**
* Calls an object method directly if $callback uses an object.
* Otherwise calls using call_user_func_array()
*/
function call( $callback, array $args = array(), $check_callable = false ){
		
	if ( $check_callable && !is_callable($callback) ){
		trigger_error( "Cannot call." );
		return null;
	}
	
	if ( !is_array($callback) || !is_object($callback[0]) )
		return call_user_func_array( $callback, $args );
	
	$class = $callback[0];
	$method = $callback[1];
	
	switch( count($args) ){
		case 0:
			return $class->$method();
		case 1:
			return $class->$method( $args[0] );
		case 2:
			return $class->$method( $args[0], $args[1] );
		case 3:
			return $class->$method( $args[0], $args[1], $args[2] );
		case 4:
			return $class->$method( $args[0], $args[1], $args[2], $args[3] );
		default:
			return call_user_func_array( array($class, $method), $args );
	}
}

// calls a callback statically.
function call_static( $callback, array $args = array(), $check_callable = false ){
	
	if ( $check_callable && !is_callable($callback) ){
		trigger_error( "Cannot call." );
		return null;
	}
	
	return call_user_func_array( $callback, $args );
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

/**
* Prettyprinted var_dump()
*/ 	
function vardump() {
	$vars = func_get_args();
	if ( 1 === func_num_args() )
		$vars = array_shift( $vars );
	echo '<pre class="prettyprint">';	
	var_dump($vars);
	echo '</pre>';
}