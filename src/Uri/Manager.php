<?php

namespace xpl\Utility\Uri;

class Manager {
	
	protected static $templates = array();
	
	public static function register(Template $uri_template) {
		
		if (! $name = $uri_template->getName()) {
			throw new \RuntimeException("Registered URI templates must have a name.");
		}
		
		static::$templates[$name] = $uri_template;
	}
	
	public static function unregister($name) {
		
		unset(static::$templates[$name]);
	}
	
	public static function build($name, array $args) {
		
		if (isset(static::$templates[$name])) {
			return static::$templates[$name]->build($args);
		}
		
		throw new \RuntimeException("No URI template named: '$name'.");
	}
	
}
