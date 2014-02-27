<?php

namespace Phpf\Util;

class Path {
		
	/**
	* Converts backslashes into forward slashes and strips trailing
	*/
	public static function normalize( $path ){
		return rtrim(str_replace(array('/', '\\'), '/', $path), '/' );
	}
	
	/**
	* Returns file extension if exists, otherwise false.
	*/
	public static function extension( $path ){
		$ext = pathinfo(rtrim($path, '/\\'), PATHINFO_EXTENSION);
		return $ext ? $ext : false;	
	}
	
	/**
	 * Return a filename only if its a file.
	 *  
	 * Using pathinfo() with PATHINFO_FILENAME  flag will return the 
	 * directory name if not a file (not what we want).
	 */
	public static function filename( $path ){
		return self::extension($path) ? pathinfo(rtrim($path, '/\\'), PATHINFO_FILENAME) : false;	
	}
	
	/**
	 * Returns server base url path from which to build URLs.
	 */
	public static function baseUrl( $server = null ){
		
		if ( empty($server) ){
			$server =& $_SERVER;
		}
		
		return dirname($server['HTTP_HOST'].$server['SCRIPT_NAME']) . '/';
	}
	
	/**
	 * Returns URL to given file or directory path.
	 * If no path is given, attempts to return current URL.
	 */
	public static function url( $path = '', $protocol = 'http' ){
		
		if ( empty($path) ){
			$path = $_SERVER['REQUEST_URI'];
		}
		
		$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', self::normalize($path));
		
		$url = self::normalize(self::baseUrl());
		
		if ( $pos = strpos($path, $url) ){
			$url = trim(substr($path, $pos), '/');
		} else {
			$url .= '/' . trim($path, '/');
		}
		
		if ( false === $protocol ){
			return $url;
		}
		
		if ( class_exists('Phpf\Http\Http') ){
			$protocol .= \Phpf\Http\Http::isSsl() ? 's' : '';
		}
		
		$url = rtrim($protocol, ':/') . '://' . trim($url, '/');
		
		if ( false === self::extension($url) ){
			$url .= '/';
		}
		
		return $url;
	}
		
	/**
	 * Base64 encode data safe for URLs.
	 */
	public static function safeBase64Encode( $data ){
		
		$b64 = base64_encode($data);
	    
		return str_replace(
	        array('+', '/', '\r', '\n', '='),
	        array('-', '_'),
	        $b64
	    );	
	}
	
	/**
	 * Decode a URL-safe base64-encoded string.
	 */
	public static function safeBase64Decode( $b64 ){
	    
		$b64 = str_replace(
	        array('-', '_'),
	        array('+', '/'),
	        $b64
	    );
	    
		return base64_decode($b64);
	}
		
}
