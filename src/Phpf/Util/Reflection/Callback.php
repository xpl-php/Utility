<?php

namespace Phpf\Util\Reflection;

use Exception;
use ReflectionFunction;
use ReflectionMethod;
use Closure;

class Callback {
	
	const FUNC = 1;
	
	const CLOSURE = 2;
	
	const OBJECT_METHOD = 4;
	
	const STATIC_METHOD = 8;
	
	const INVOKABLE_OBJECT = 16;
	
	protected $reflection;
	
	protected $type;
	
	public function __construct( $callable ){
		
		if ( ! is_callable($callable) ){
			throw new Exception("Uncallable function/method passed to Phpf\Reflection\Factory");
		}
		
		if ( is_string($callable) ){
			$this->reflection = new ReflectionFunction($callable);
			$this->type = self::FUNC;
		} elseif ( is_array($callable) ){
			$this->reflection = new ReflectionMethod($callable[0], $callable[1]);
			if ( is_object($callable[0]) ){
				$this->type = self::OBJECT_METHOD;
			} else {
				$this->type = self::STATIC_METHOD;
			}
		} elseif ( $callable instanceof Closure ){
			$this->reflection = new ReflectionFunction($callable);
			$this->type = self::CLOSURE;
		} elseif ( is_object($callable) && method_exists($callable, '__invoke') ){
			$this->reflection = new ReflectionMethod($callable, '__invoke');
			$this->type = self::INVOKABLE_OBJECT;
		} else {
			throw new Exception("Unknown callable type."); // this should never happen
		}
	}
	
	public function reflectParameters( array $params = array() ){
		
		$parameters = array();
		
		foreach( $this->reflection->getParameters() as $_param )
			$ordered[ $_param->getPosition() ] = $_param;
		
		ksort($ordered);
		
		foreach( $ordered as $i => &$rParam ){
			
			$name = $rParam->getName();
			
			if ( isset($params[ $name ]) ){
				$parameters[ $name ] = $params[ $name ];
			} elseif ( self::CLOSURE === $this->type && isset($params[$i]) ){
				$parameters[ $name ] = $params[ $i ];
			} elseif ( $rParam->isDefaultValueAvailable() ){
				$parameters[ $name ] = $rParam->getDefaultValue();
			} else {
				throw new Exception("Missing required parameter $name.");
			}
		}
		
		return $parameters;
	}
	
}
