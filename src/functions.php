<?php

namespace xpl\Utility {
	class functions {
		// dummy class
	}
}

namespace {
	
if (! function_exists('convert_units')) :
	
	function convert_units($quantity, $unit, $to_unit) {
		return xpl\Utility\Conversion\Unit::convert($quantity, $unit, $to_unit);
	}
	
endif;

if (! function_exists('uri_template')) :
		
	/**
	 * Builds a URI from a registered URI template.
	 * 
	 * @param string $name URI template name.
	 * @param array $args Associative array of template values.
	 * @return string
	 */
	function uri_template($name, array $args) {
		return xpl\Utility\Uri\Manager::build($name, $args);
	}
	
	/**
	 * Creates and registers a new URI template.
	 * 
	 * @param string $name Unique template name.
	 * @param string $uri_template URI template string.
	 * @return \xpl\Utility\Uri\Template
	 */
	function register_uri_template($name, $uri_template) {
		$object = new xpl\Utility\Uri\Template($uri_template, $name);
		xpl\Utility\Uri\Manager::register($object);
		return $object;
	}
	
endif;

/** ============================
	Class Utilities
============================= */

if (! function_exists('alias')) :
		
	/**
	 * Create a (lazy) class alias.
	 * 
	 * @param string $class Fully resolved classname for which to create an alias.
	 * @param string $alias Alias for the class given in first parameter.
	 * @return void
	 */
	function alias($class, $alias) {
		xpl\Utility\ClassUtils\Alias::add($class, $alias);
	}
	
endif;

if (! function_exists('autoloader')) :

	/**
	 * Returns an autoloader instance for a given namespace.
	 * 
	 * @param string $namespace Namespace for which to retrieve an autoloader.
	 * @return \xpl\Utility\ClassUtils\Autoloader Autoloader for the given namespace.
	 */
	function autoloader($namespace) {
		return xpl\Utility\ClassUtils\Autoloader::instance($namespace);
	}
	
endif;

/** ============================
	Tokens
============================= */

if (! function_exists('token_generate')) :
	
	/**
	 * Generates a verifiable token from seed.
	 *
	 * @param string $seed String used to create hash token.
	 * @param string $algo [Optional] Hash algorithm to use. Default "sha1".
	 * @return string Token using hash_hmac().
	 */
	function token_generate($seed, $algo = null) {
		return (string) new xpl\Utility\Token($seed, $algo);
	}

endif;

if (! function_exists('token_verify')) :
	
	/**
	 * Verifies a token with seed.
	 * 
	 * @param string $token Token string to verify.
	 * @param string $seed Token seed used to create the token.
	 * @param string $algo [Optional] Algorithm used to create the token.
	 * @return boolean True if the token validates, otherwise false.
	 */
	function token_verify($token, $seed, $algo = null) {
		$real = new xpl\Utility\Token($seed, $algo);
		return $real->validate($token);
	}

endif;

/** ============================
	Strings/Randomness
============================= */

if (! function_exists('str_slug')) : 
	
	/**
	 * Returns a "slug" for a given string.
	 * 
	 * @param string $string
	 * @param string $separator [Optional] Default "-".
	 * @return string
	 */
	function str_slug($string, $separator = '-') {
		return xpl\Utility\Inflector::title($string, $separator);
	}

endif;

if (! function_exists('str_rand')) :
	
	/**
	 * Generate a random string from one of several of character pools.
	 *
	 * @param int $length Length of the returned random string (default 16)
	 * @param string $charlist Type of characters {@see \xpl\Utility\Rand constants}
	 * @return string A random string
	 */
	function str_rand($length = 16, $charlist = 'alnum') {
		return xpl\Utility\Rand::str($length, $charlist);
	}
	
endif;

if (! function_exists('rand_bytes')) :
	
	/**
	 * Generates a random string with given number of bytes.
	 * 
	 * @param int $length Length of string.
	 * @param boolean $strong [Optional] Whether to use a crypographically strong 
	 * algorithm. Default true.
	 * @return string Random string of given length.
	 */
	function rand_bytes($length = 12, $strong = true) {
		return xpl\Utility\Rand::bytes($length, $strong);
	}
	
endif;

if (function_exists('hash_format') && ! function_exists('generate_uuid')) :
	
	/**
	 * Generate a UUID.
	 * 
	 * 32 characters (a-f and 0-9) in format 8-4-4-4-12.
	 * 
	 * @uses hash_format() {@see wells5609/php-util}
	 * 
	 * @return string A 32-char length (36 with dashes) UUID (v4).
	 */
	function generate_uuid() {
		return hash_format(xpl\Utility\Rand::hex(32));
	}

endif;

}
