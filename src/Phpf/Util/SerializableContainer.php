<?php

namespace Phpf\Util;

use Serializable;

class SerializableContainer extends Container implements Serializable {
	
	/**
	 * Returns serialized array of object vars.
	 */
	public function serialize(){
		return serialize($this->toArray());
	}
	
	/**
	 * Unserializes and then imports vars.
	 */
	public function unserialize( $serialized ){
		$this->import(unserialize($serialized));
	}
	
}
