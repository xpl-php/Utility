<?php

namespace Phpf\Util;

use ArrayAccess;

/**
 * Object wrapper for native PHP sessions.
 */
class Session implements ArrayAccess
{

	public function __construct($id = null) {
		if (isset($id)) {
			session_id($id);
		}
	}

	public function start() {
		if (session_start()) {
			$this->countRequests();
		}
	}

	public function id() {
		return session_id();
	}

	public function name() {
		return session_name();
	}
	
	public function __get($var) {
		return isset($_SESSION[$var]) ? $_SESSION[$var] : null;
	}
	
	public function __set($var, $val) {
		$_SESSION[$var] = $val;
	}

	public function get($var = null) {

		if (empty($var)) {
			return $_SESSION;
		}

		return isset($_SESSION[$var]) ? $_SESSION[$var] : null;
	}

	public function set($var, $val) {
		$_SESSION[$var] = $val;
		return $this;
	}

	public function destroy() {

		$_SESSION = array();

		if (ini_get('session.use_cookies')) {
			$p = session_get_cookie_params();
			setcookie(session_name(), '', time() - 31536000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
		}

		if (isset($_COOKIE[$this->getName()])) {
			unset($_COOKIE[$this->getName()]);
		}

		session_unset();

		if (session_id()) {
			session_destroy();
		}
	}

	/**
	 * @param $index 
	 * @param $newval 
	 * @return void
	 */
	public function offsetSet($index, $newval) {
		$this->__set($index, $newval);
	}

	/**
	 * @param $index 
	 * @return mixed
	 */
	public function offsetGet($index) {
		return isset($_SESSION[$index]) ? $_SESSION[$index] : null;
	}

	/**
	 * @param $index 
	 * @return void
	 */
	public function offsetUnset($index) {
		unset($_SESSION[$index]);
	}

	/**
	 * @param $index 
	 * @return boolean
	 */
	public function offsetExists($index) {
		return isset($_SESSION[$index]);
	}
	
	protected function countRequests() {

		if (isset($_SESSION['request_count'])) {
			$count = $_SESSION['request_count'] + 1;
		} else {
			$count = 1;
		}

		$this->set('request_count', $count);
	}
	
}
