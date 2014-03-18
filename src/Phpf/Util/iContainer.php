<?php

namespace Phpf\Util;

interface iContainer {
	
	public function __set($var, $val);
	
	public function __get($var);
	
	public function __isset($var);
	
	public function __unset($var);
	
	public function set($var, $val);
	
	public function get($var);
	
	public function exists($var);
	
	public function remove($var);
	
	public function import($data);
	
	public function toArray();
	
}
