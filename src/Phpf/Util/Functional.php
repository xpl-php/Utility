<?php

namespace Phpf\Util {
	
	class Functional {
		// dummy class
	}
}

namespace {
	
	require_once __DIR__ . '/functions.php';
	
	function autoloader_register($namespace, $path){
			
		$al = \Phpf\Util\Autoloader::instance($namespace, rtrim($path, '/\\'));
		
		if ( !$al->isRegistered() ){
			$al->register();
		}
	}
	
	function dependency( $name, \Closure $closure = null, $is_singleton = false ){
			
		$di = \Phpf\Util\Dependency\Container::i();
		
		if ( isset($closure) ){
			if ( $is_singleton ){
				$di->setSingleton($name, $closure);
			} else {
				$di->set($name, $closure);
			}
		} else {
			if ( $is_singleton ){
				return $di->singleton($name);
			}
			return $di->get($name);
		}
	}
	
	function singleton( $name, \Closure $closure = null ){
			
		$di = \Phpf\Util\Dependency\Container::i();
		
		if ( isset($closure) ){
			$di->setSingleton($name, $closure);
		} else {
			return $di->singleton($name);
		}
	}
	
	function register( $key, $object ){
		\Phpf\Util\Registry::set($key, $object);
	}
	
	function registry( $key ){
		return \Phpf\Util\Registry::get($key);
	}
		
}
