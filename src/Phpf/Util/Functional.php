<?php

namespace Phpf\Util {

	class Functional
	{
		// dummy class
	}

}

namespace {

	use Phpf\Util\Str;

	function html_a($content, $href, $attributes = array()) {
		return \Phpf\Util\Html::a($content, $href, $attributes);
	}

	function html_script($url, $attrs = array()) {
		return \Phpf\Util\Html::script($url, $attrs);
	}

	function html_link($url, $attrs = array()) {
		return \Phpf\Util\Html::link($url, $attrs);
	}

	/** ======================
	 Autoloader
	 ====================== */

	/**
	 * Registers an autoloader for given namespace.
	 */
	function autoloader_register($namespace, $path) {

		$al = \Phpf\Util\Autoloader::instance($namespace, rtrim($path, '/\\'));

		if (! $al->isRegistered()) {
			$al->register();
		}
	}

	/** ======================
	 Registry
	 ====================== */

	/**
	 * Registers an object with Registry.
	 */
	function register($key, $object) {
		\Phpf\Util\Registry::set($key, $object);
	}

	/**
	 * Returns an object from Registry given its key.
	 */
	function registry($key) {
		return \Phpf\Util\Registry::get($key);
	}

	/** ======================
	 Session
	 ====================== */

	/**
	 * Sets a session variable.
	 */
	function session_set($var, $val) {
		$session = \App::instance()->get('session');
		return $session->set($var, $val);
	}

	/**
	 * Returns a session variable.
	 */
	function session_get($var) {
		$session = \App::instance()->get('session');
		return $session->get($var);
	}

	/** ==============================
	 Directories/Files
	 =============================== */

	/** ======================
	 Strings
	 ======================= */

	/**
	 * Escape a string using fairly aggressive rules.
	 * Strips all tags and converts to html entities.
	 *
	 * @param string $string The string to sanitize.
	 * @param string $flag Strip or do nothing with high ASCII chars. (default:
	 * strip)
	 * @return string Sanitized string.
	 */
	function str_esc($string, $flag = Str::ESC_ASCII) {
		return Str::esc($string, $flag);
	}

	/**
	 * Strips non-alphanumeric characters from a string.
	 * Add characters to $extras to preserve those as well.
	 * Extra chars should be escaped for use in preg_*() functions.
	 */
	function str_esc_alnum($str, array $extras = null) {
		return Str::escAlnum($str, $extras);
	}

	/**
	 * Escapes text for SQL LIKE special characters % and _.
	 *
	 * @param string $text The text to be escaped.
	 * @return string text, safe for inclusion in LIKE query.
	 */
	function str_esc_sql_like($string) {
		return Str::escSqlLike($string);
	}

	/**
	 * Returns true if string ends with given $needle.
	 */
	function str_endswith($haystack, $needle) {
		return Str::endsWith($haystack, $needle);
	}

	/**
	 * Wraps a string in <![CDATA[ {{$str}} ]]>.
	 */
	function str_cdata($str) {
		return '<![CDATA[ '.$str.' ]]>';
	}

	/**
	 * Formats a string by injecting non-numeric characters into
	 * the string in the positions they appear in the template.
	 *
	 * @param string $string The string to format
	 * @param string $template String format to apply
	 * @return string Formatted string.
	 */
	function str_format($string, $template) {
		return Str::format($string, $template);
	}

	/**
	 * Generate a random string from one of several of character pools.
	 *
	 * @param int $length Length of the returned random string (default 16)
	 * @param string $type The type of characters to use to generate string.
	 * @return string A random string
	 */
	function str_rand($length = 16, $pool_type = 'alnum') {
		return Str::rand($length, $pool_type);
	}

	/**
	 * Converts a string to a PEAR-like class name. (e.g. "View_Template_Controller")
	 */
	function str_pearclass($str) {
		return Str::pearClass($str);
	}

	/**
	 * Converts a string to "snake_case"
	 */
	function str_snakecase($str) {
		return Str::snakeCase($str);
	}

	/**
	 * Converts a string to "StudlyCaps"
	 */
	function str_studlycaps($str) {
		return Str::studlyCaps($str);
	}

	/**
	 * Converts a string to "camelCase"
	 */
	function str_camelcase($str) {
		return Str::camelCase($str);
	}

	/**
	 * Formats a phone number based on string lenth.
	 */
	function phone_format($phone) {
		return Str::formatPhone($phone);
	}

	/**
	 * Formats a hash/digest based on string length.
	 */
	function hash_format($hash) {
		return Str::formatHash($hash);
	}

	/**
	 * Serialize data, if needed.
	 *
	 * @param mixed $data Data that might be serialized.
	 * @return mixed A scalar data
	 */
	function maybe_serialize($data) {
		return Str::maybeSerialize($data);
	}

	/**
	 * Unserialize value only if it was serialized.
	 *
	 * @param string $value Maybe unserialized original, if is needed.
	 * @return mixed Unserialized data can be any type.
	 */
	function maybe_unserialize($value) {
		return Str::maybeUnserialize($value);
	}

	/**
	 * Check value to find if it was serialized.
	 *
	 * @param mixed $data Value to check to see if was serialized.
	 * @param bool $strict Optional. Whether to be strict about the end of the
	 * string. Defaults true.
	 * @return bool False if not serialized and true if it was.
	 */
	function is_serialized($data, $strict = true) {
		return Str::isSerialized($data, $strict);
	}

	/** ====================
	 Paths
	 ===================== */

	/**
	 * Url-safe Base64 encode.
	 */
	function base64_encode_urlsafe($str) {
		return \Phpf\Util\Path::safeBase64Encode($str);
	}

	/**
	 * Url-safe Base64 decode.
	 */
	function base64_decode_urlsafe($str) {
		return \Phpf\Util\Path::safeBase64Decode($str);
	}

	/**
	 * Converts a path to URL
	 */
	function path_url($path, $protocol = 'http') {
		return \Phpf\Util\Path::url($path, $protocol);
	}

	/** ====================
	 Security
	 ===================== */

	/**
	 * Generates a random string with given number of bytes.
	 * If $strong = true (default), must use one of:
	 * 		openssl_random_pseudo_bytes() PHP >= 5.3.4 (not Windows)
	 * 		mcrypt_create_iv() PHP >= 5.3.7 (not Windows)
	 * 		/dev/urandom
	 */
	function rand_bytes($length = 12, $strong = true) {
		return \Phpf\Util\Security::randBytes($length, $strong);
	}

	/**
	 * Generate a UUID
	 * 32 characters (a-f and 0-9) in format 8-4-4-12.
	 */
	function generate_uuid() {
		return \Phpf\Util\Security::generateUuid();
	}

	/**
	 * Generates a 32-byte base64-encoded random string.
	 */
	function generate_csrf_token() {
		return \Phpf\Util\Security::generateCsrfToken();
	}

	/** ====================
	 Arrays
	 ===================== */

	/**
	 * Retrieves a value from $array given its path in dot notation
	 */
	function array_get(array &$array, $dotpath) {
		return \Phpf\Util\Arr::dotGet($array, $dotpath);
	}

	/**
	 * Sets a value in $array given its path in dot notation.
	 */
	function array_set(array &$array, $dotpath, $value) {
		return \Phpf\Util\Arr::dotSet($array, $dotpath, $value);
	}

	/**
	 * Merge user defined arguments into defaults array.
	 *
	 * @param string|array $args Value to merge with $defaults
	 * @param array $defaults Array that serves as the defaults.
	 * @return array Merged user defined values with defaults.
	 */
	function parse_args($args, $defaults = '') {
		return \Phpf\Util\Arr::parse($args, $defaults);
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
	function list_filter($list, $args = array(), $operator = 'AND', $keys_exist_only = false) {
		return \Phpf\Util\Arr::filter($list, $args, $operator, $keys_exist_only);
	}

	/**
	 * Pluck a certain field out of each object in a list.
	 *
	 * @param array $list A list of objects or arrays
	 * @param int|string $field A field from the object to place instead of the
	 * entire object
	 * @return array
	 */
	function list_pluck($list, $field) {
		return \Phpf\Util\Arr::pluck($list, $field);
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
	function list_pluck_ref(&$list, $field) {
		return \Phpf\Util\Arr::pluckRef($list, $field);
	}
	
}
