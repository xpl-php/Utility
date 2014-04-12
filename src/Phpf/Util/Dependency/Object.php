<?php

namespace Phpf\Util\Dependency;

class Object extends Resource {
	
	public function validate($resource) {
		return is_object($resource);
	}
	
	public function resolve(array $args = null) {
		return $this->resource;
	}
}
