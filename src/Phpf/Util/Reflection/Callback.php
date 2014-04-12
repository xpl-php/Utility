<?php

namespace Phpf\Util\Reflection;

class Callback
{

	const FUNC = 1;

	const CLOSURE = 2;

	const OBJECT_METHOD = 4;

	const STATIC_METHOD = 8;

	const INVOKABLE_OBJECT = 16;

	protected $callable;

	protected $reflection;

	protected $type;

	public function __construct($callable) {

		if (! is_callable($callable)) {
			throw new Exception\Uncallable("Uncallable function/method passed to Phpf\Util\Reflection\Callback.");
		}

		$this->callable = $callable;

		if (is_string($callable)) {
			$this->reflection = new \ReflectionFunction($callable);
			$this->type = self::FUNC;
		} elseif (is_array($callable)) {
			$this->reflection = new \ReflectionMethod($callable[0], $callable[1]);
			if (is_object($callable[0])) {
				$this->type = self::OBJECT_METHOD;
			} else {
				$this->type = self::STATIC_METHOD;
			}
		} elseif ($callable instanceof \Closure) {
			$this->reflection = new \ReflectionFunction($callable);
			$this->type = self::CLOSURE;
		} elseif (is_object($callable) && method_exists($callable, '__invoke')) {
			$this->reflection = new \ReflectionMethod($callable, '__invoke');
			$this->type = self::INVOKABLE_OBJECT;
		} else {
			throw new Exception\UnknownCallableType("Unknown callable type.");
			// this should never happen
		}
	}

	public function reflectParameters(array $params = array()) {

		$parameters = array();

		foreach ( $this->reflection->getParameters() as $_param )
			$ordered[$_param->getPosition()] = $_param;

		if (empty($ordered)) {
			return $this->reflected_params = $parameters;
		}

		ksort($ordered);

		foreach ( $ordered as $i => &$rParam ) {

			$name = $rParam->getName();

			if (isset($params[$name])) {
				$parameters[$name] = $params[$name];
			} elseif (self::CLOSURE === $this->type && isset($params[$i])) {
				$parameters[$name] = $params[$i];
			} elseif ($rParam->isDefaultValueAvailable()) {
				$parameters[$name] = $rParam->getDefaultValue();
			} else {
				throw new Exception\MissingParam("Missing reflection parameter '$name'");
			}
		}

		return $this->reflected_params = $parameters;
	}

	public function invoke() {

		if (! isset($this->reflected_params)) {
			throw new \RuntimeException("Call reflectParameters() before invoking");
		}

		switch($this->type) {

			case self::OBJECT_METHOD :
				return $this->reflection->invokeArgs($this->callable[0], $this->reflected_params);

			case self::CLOSURE :
			case self::FUNC :
				return $this->reflection->invokeArgs($this->reflected_params);

			case self::INVOKABLE_OBJECT :
				return $this->reflection->invokeArgs($this->callable, $this->reflected_params);

			case self::STATIC_METHOD :
				return call_user_func_array($this->callable, $this->reflected_params);
		}

	}

}
