<?php

namespace xpl\Utility\ClassUtils;

class ClassMapLoader {
	
	protected $classmap;
	protected $registered;
	
	protected function __construct() {
		$this->classmap = array();
		$this->registered = false;
	}
	
	public function setClasses(array $class_map) {
		$this->classmap = $class_map;
		return $this;
	}
	
	public function addClasses(array $class_map) {
		$this->classmap = array_merge($this->classmap, $class_map);
		return $this;
	}
	
	public function getClassPath($class) {
		return isset($this->classmap[$class]) ? $this->classmap[$class] : null;
	}
	
	public function register() {
		if (! $this->registered) {
			spl_autoload_register($this);
			$this->registered = true;
		}
		return $this;
	}
	
	public function unregister() {
		if ($this->registered) {
			spl_autoload_unregister($this);
			$this->registered = false;
		}
		return $this;
	}
	
	public function __invoke($class) {
		if (isset($this->classmap[$class]) ) {
			include $this->classmap[$class];
		}
	}
	
}
