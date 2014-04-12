<?php
/**
 * Dependencies:
 * 	Type	    Stored		Description
 * 	===========	=========== ====================
 * 	1. Objects	(Object)	Given as an object, returns same. Nothing special.
 * 	2. Factory	(Callable)	Returns a new instance of the requested resource when invoked.
 *  3. Service	(Class)		Lazily-instantiated; loads a service when invoked.
 */

namespace Phpf\Util\Dependency;

use RuntimeException;
use InvalidArgumentException;
use Closure;

class Container {
	
	/**
	 * Objects.
	 * @var array
	 */
	protected $object;
	
	/**
	 * Factories.
	 * @var array
	 */
	protected $factory;
	
	/**
	 * Services.
	 * @var array
	 */
	protected $service;
	
	/**
	 * Constructor
	 */
	public function __construct(){
		$this->object = array();
		$this->factory = array();
		$this->service = array();
	}
	
	/**
	 * Register a Service
	 */
	public function registerService($name, $provider) {
		$resource = new Service($provider);
		$this->service[$service] = $resource;
		return $this;
	}
	
	/**
	 * Register a Factory
	 */
	public function registerFactory($name, $callback) {
		$resource = new Factory($callback);
		$this->factory[$name] = $resource;
		return $this;
	}
	
	/**
	 * Register an Object
	 */
	public function registerObject($name, $object) {
		$resource = new Object($object);
		$this->object[$name] = $resource;
		return $this;
	}
	
	public function service($name, array $args = array()) {
			
		if (! isset($this->service[$name])) {
			return null;
		}
		
		return $this->service[$name]->resolve($args);
	}
	
	public function factory($name, array $args = array()) {
			
		if (! isset($this->factory[$name])) {
			return null;
		}
		
		return $this->factory[$name]->resolve($args);
	}
	
	public function object($name, array $args = array()) {
			
		if (! isset($this->object[$name])) {
			return null;
		}
		
		return $this->object[$name]->resolve($args);
	}
	
	/**
	 * Register a something
	 */
	public function register($name, $resource_type, $resource) {
		
		$methodName = 'register'.ucfirst($resource_type);
		
		if (! method_exists($this, $methodName)) {
			throw new InvalidArgumentException("Unknown resource type '$resource_type'.");
		}
		
		$this->{$resource_type}[$name] = $this->{$methodName}($resource);
		
		return $this;
	}
	
	public function resolve($resource, array $args = array()) {
		
		if (isset($this->objects[$resource])) {
			return $this->objects[$resource];
		}
		
		if (isset($this->resources[$resource])) {
			$res = $this->resources[$resource];
			return $res($args);
		}
		
		if (isset($this->factories[$resource])) {
			$factory = $this->factories[$resource];
			return $factory($args);
		}
		
		if (class_exists($resource, true)) {
			return new $resource($args);
		}
		
		throw new \RuntimeException("Could not resolve dependency $resource.");
	}
	
	public function set( $id, $value, $asSingleton = false ){
		
		if (! is_object($value)) {
			$msg = "Must pass closure or object as value to set() - " . gettype($value) . " given.";
			throw new InvalidArgumentException($msg);
		}
		
		if ( $value instanceof Closure ){
			$this->closures[$id] = $value;
		} elseif ( $asSingleton ){
			$this->singletons[$id] = $value;
		} else {
			$this->objects[$id] = $value;
		}
		
		if ( $asSingleton ){
			$this->singletonIds[] = $id;
		}
	}
	
	public function get( $id, $args = array(), $asSingleton = false ){
		
		if ( $asSingleton ){
			
			if (! isset($this->singletons[$id])) {
					
				if (! isset($this->closures[$id])) {
					throw new RuntimeException("Unknown singleton $id");
					return null;
				}
				
				$this->singletons[$id] = call_user_func_array($this->closures[$id], (array) $args);
			}
			
			return $this->singletons[$id];
		}
		
		if ( isset($this->objects[$id]) )
			return $this->objects[$id];
		
		if ( isset($this->closures[$id]) )
			return call_user_func_array($this->closures[$id], (array) $args);
		
		throw new RuntimeException("Unknown resource $id.");
	}
	
	public function setSingleton( $id, $value ){
		$this->set($id, $value, true);
	}
	
	public function getSingleton($id, $args = array()){
		return $this->get($id, $args, true);
	}
	
	public function singleton( $id, $args = array() ){
		return $this->get($id, $args, true);
	}
	
	public function singletonExists( $id ){
		return in_array($id, $this->singletonIds);
	}

	public function instanceExists( $id ){
			
		if ( $this->singletonExists($id) )
			return isset($this->singletons[$id]);
		
		return isset($this->objects[$id]);
	}
}
