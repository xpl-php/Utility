<?php

namespace Phpf\Util\Random;

use Phpf\Util\Rand;

class Bytes extends Base {
	
	protected $strong;
	
	public function __construct($length, $strong = true) {
		$this->strong = (bool) $strong;
		$this->value = $this->generate($length);
	}
	
	public function generate($length) {
		return Rand::bytes($length, $this->strong);
	}
}
