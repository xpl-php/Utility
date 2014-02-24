<?php
/**
 * @package Phpf.Util
 * @subpackage Service.Provider
 */

namespace Phpf\Util\Service;

class Provider implements \Phpf\Service\Provider {
  
	protected $provided = false;
	
	public function isProvided(){
		return $this->provided;
	}
	
	public function provide(){
		
		require dirname(__DIR__) . '/functions.php';
		
		$this->provided = true;
	}
	
}
