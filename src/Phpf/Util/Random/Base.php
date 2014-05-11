<?php

namespace Phpf\Util\Random;

use Phpf\Util\Rand;

abstract class Base {
	
	protected $value;
		
	abstract public function generate($length);
	
	public function __construct($length) {
		$this->value = $this->generate($length);
	}
	
	public function __get($var) {
		if ('value' === $var) {
			return $this->value;
		}
		return null;
	}
	
	final public function getValue() {
		return $this->value;
	}
	
	final public function __toString() {
		return $this->value;
	}
	
	final public function regenerate($length) {
		$this->value = $this->generate($length);
		return $this;
	}
	
	protected function fromCharlistID($length, $charlist_id) {
		return Rand::fromCharlist($length, Rand::getChars($charlist_id));
	}
	
}
