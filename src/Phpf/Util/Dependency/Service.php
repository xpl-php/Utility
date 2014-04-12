<?php

namespace Phpf\Util\Dependency;

class Service extends Resource {
	
	public function validate($resource) {
			
		if (is_string($resource)) {
			return in_array('Phpf\\Util\\Dependency\\ServiceInterface', class_implements($resource, true), true);
		}
		
		return $resource instanceof ServiceInterface;
	}
	
	public function resolve(array $args = array()) {
		
		if (is_string($this->resource)) {
			$class = $this->resource;
			$this->resource = $object = new $class();
			return $object::start($args);
		}
		
		if (! $this->resource->isStarted()) {
			$object = $this->resource;
			return $object::start($args);
		}
		
		return $this->resource;	
	}
}
