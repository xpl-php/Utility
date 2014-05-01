<?php

namespace Phpf\Util\Random;

use Phpf\Util\Rand;

class Salt extends Base {
	
	public function generate($length) {
		return $this->fromCharlistID($length, Rand::SALT);
	}
}
