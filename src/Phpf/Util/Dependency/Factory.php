<?php

namespace Phpf\Util\Dependency;

class Factory extends Resource {
	
	public function validate($resource) {
		return is_callable($resource);
	}
	
	public function resolve(array $args = array()) {
		return call_user_func_array($this->resource, $args);
	}
	
}
