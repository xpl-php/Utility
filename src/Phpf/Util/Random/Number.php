<?php

namespace Phpf\Util\Random;

use Phpf\Util\Rand;

class Number extends Base {
	
	public function generate($length) {
		return $this->fromCharlistID($length, Rand::NUM);
	}
}
