<?php

namespace Phpf\Util;

/**
 * Pluggable helper objects and functions.
 *
 * @example Loading a set of functions
 * Helper::myHelper() loads file {application_path}/helpers/{MyHelper}.php
 *
 * @example Returning an object
 * Above, if file defines class MyHelper_Helper, a new instance is returned.
 *
 * @example Plugging a helper
 * Helper::provide('myfunc', function ($name){
 * 	return "hello $name";
 * });
 * Helper::myfunc('David') returns "Hello David"
 */
class Helper
{

	protected static $helpers = array();

	/**
	 * Provides a helper in various ways.
	 *
	 * @return mixed Could be object, boolean, or callback result.
	 */
	public static function __callStatic($fn, $args = array()) {

		if (isset(static::$helpers[$fn]) || static::load($fn)) {
			return \result(static::$helpers[$fn], $args);
		}

		trigger_error("Cannot load helper $fn", E_USER_NOTICE);
	}

	/**
	 * Provide a helper via callback.
	 *
	 * @param string $name The helper name.
	 * @param callable $callback Function to call when helper invoked.
	 * @return void
	 */
	public static function provide($name, $callback) {
		static::$helpers[$name] = $callback;
	}

	/**
	 * Attempts to load a helper file by name.
	 * If success, also tries to load a class like "{Name}_Helper".
	 *
	 * @param string $helper
	 * @return boolean
	 */
	protected static function load($helper) {

		$path = APP.'helpers/'.ucfirst($helper).'.php';

		if (file_exists($path)) {

			require $path;

			$class = $helper.'_Helper';

			if (class_exists($class, false)) {
				static::$helpers[$helper] = new $class;
			} else {
				static::$helpers[$helper] = true;
			}

			return true;
		}

		return false;
	}

}
