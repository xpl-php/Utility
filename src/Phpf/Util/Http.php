<?php

namespace Phpf\Util;

class Http {
		
	/** 
	* Returns HTTP request headers as array.
	*/
	public static function getRequestHeaders(){
		static $headers;
		if ( isset($headers) ) return $headers; // get once per request
		
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
	public static function getRequestHeader( $name ){
		$headers = self::getRequestHeaders();
		$name = str_replace('_', '-', strtolower($name));
		return isset($headers[ $name ]) ? $headers[ $name ] : null;
	}
	
	/**
	 * Whether using SSL
	 */
	public static function isSsl(){
			
		if ( isset($_SERVER['HTTPS']) 
			&& ('on' == strtolower($_SERVER['HTTPS']) || '1' == $_SERVER['HTTPS']) ){
			return true;
		} elseif ( isset($_SERVER['SERVER_PORT']) && '443' == $_SERVER['SERVER_PORT'] ){
			return true;
		}
		
		return false;	
	}
	
	/**
	 * Returns server HTTP protocol string.
	 */
	public static function getServerProtocol(){
		
		$protocol = $_SERVER['SERVER_PROTOCOL'];
		
		if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
			$protocol = 'HTTP/1.0';
		
		return $protocol;
	}
	
	/**
	 * Set HTTP status header.
	 *
	 * @param int $code HTTP status code.
	 */
	public static function getStatusHeader( $code ) {
		
		$description = self::getStatusHeaderDesc($code);
	
		if ( empty($description) )
			return;
	
		$protocol = self::getServerProtocol();
		
		return "$protocol $code $description";
	}
	
	/**
	 * Gets the header information to prevent caching.
	 *
	 * The several different headers cover the different ways cache prevention is handled
	 * by different browsers
	 *
	 * @return array The associative array of header names and field values.
	 */
	public static function getNocacheHeaders() {
		
		$headers = array(
			'Expires' => 'Wed, 11 Jan 1984 05:00:00 GMT',
			'Cache-Control' => 'no-cache, must-revalidate, max-age=0',
			'Pragma' => 'no-cache',
		);
	
		$headers['Last-Modified'] = false;
		return $headers;
	}
		
	/**
	 * Set the headers for caching for 10 days with JavaScript content type.
	 * 
	 * @return Associative array of cache headers
	 */
	public static function getJsCacheHeaders() {
		$expiresOffset = 10 * DAY_IN_SECONDS;
		$headers = array(
			'Content-Type' => 'text/javascript; charset=UTF-8',
			'Vary' => 'Accept-Encoding', // Handle proxies
			'Expires' => gmdate("D, d M Y H:i:s", time() + $expiresOffset) . ' GMT'
		);
		return $headers;
	}
				
	/**
	 * Retrieve the description for the HTTP status.
	 *
	 * @param int $code HTTP status code.
	 * @return string Empty string if not found, or description if found.
	 */
	public static function getStatusHeaderDesc( $code ) {
		
		$code = abs( intval( $code ) );
		
		$header_to_desc = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
	
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			226 => 'IM Used',
	
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Reserved',
			307 => 'Temporary Redirect',
	
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			426 => 'Upgrade Required',
	
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			510 => 'Not Extended'
		);
	
		return isset($header_to_desc[ $code ]) ? $header_to_desc[ $code ] : '';
	}
	
}
