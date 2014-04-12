<?php

namespace Phpf\Util\Dependency;

abstract class Resource {
	
	protected $resource;
	
	/**
	 * Constructs the resource. Calls validator to check parameter.
	 * 
	 * @param mixed $resource Resource
	 * @return $this
	 * @throws InvalidResourceException if resource is invalid.
	 */
	public function __construct($resource) {
		
		if (! $this->validate($resource)) {
			throw new InvalidResourceException("Resource is invalid - given ". gettype($resource));
		}
		
		$this->resource = $resource;
	}
	
	/**
	 * Validates the resource.
	 * 
	 * @param mixed $resource Resource.
	 * @return boolean True if valid, false otherwise.
	 */
	abstract public function validate($resource);
	
	/**
	 * Called when resource is requested.
	 * Does something depending on resource type.
	 * 
	 * @param array $args Parameters to pass to resource.
	 * @return wild Anything, really.
	 */
	abstract public function resolve(array $args = null);
	
}
