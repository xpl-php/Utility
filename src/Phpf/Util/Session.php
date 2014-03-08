<?php

namespace Phpf\Util;

/**
 * Object wrapper for native PHP sessions.
 */
class Session implements Singleton {
	
	protected static $instance;
	
	public static function instance(){
		if ( !isset(self::$instance) )
			self::$instance = new self();
		return self::$instance;	
	}
	
	private function __construct(){
		$this->start();
	}
	
	public function start(){
			
		if ( session_start() ){
			$this->countRequests();
		}
	}
	
	public function id(){
		return session_id();
	}
	
	public function name(){
		return session_name();
	}
	
	public function get( $var = null ){
		
		if ( empty($var) ){
			return $_SESSION;
		}
		
		return isset($_SESSION[$var]) ? $_SESSION[$var] : null;
	}
	
	public function set( $var, $val ){
	
		$_SESSION[$var] = $val;
	
		return $this;
	}
	
	public function destroy(){
		
		if ( isset($_COOKIE[$this->getName()]) ){
			unset($_COOKIE[$this->getName()]);	
		}
		
		$_SESSION = array();
		
		session_unset();
		
		if ( session_id() ){
			session_destroy();
		}
	}
	
	protected function countRequests(){
			
		if ( isset($_SESSION['request_count']) ){
			$count = $_SESSION['request_count'] + 1;
		} else {
			$count = 1;
		}
		
		$this->set('request_count', $count);	
	}
		
}
