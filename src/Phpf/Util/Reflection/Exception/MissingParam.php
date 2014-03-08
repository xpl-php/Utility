<?php

namespace Phpf\Util\Reflection\Exception;

class MissingParam extends \ReflectionException 
{
	public $missing_param;
	
	public function setMissingParam($var){
		$this->missing_param = $var;
	}
	
	public function getMissingParam(){
		return $this->missing_param;
	}
	
}
