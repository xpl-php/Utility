<?php

namespace Phpf\Util\Random;

use Phpf\Util\Rand;

class String extends Base {
	
	protected $charlist_id;
	
	public function __construct($length, $charlist_id = Rand::ALNUM) {
		$this->charlist_id = $charlist_id;
		$this->value = $this->generate($length);
	}
	
	public function generate($length) {
		return $this->fromCharlistID($length, $this->charlist_id);
	}
}
