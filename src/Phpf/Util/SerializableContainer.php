<?php

namespace {
	
	if (! interface_exists('JsonSerializable')) {
		
		interface JsonSerializable {
		
			public function jsonSerialize();
		
		}
	}
	
}

namespace Phpf\Util {
	
	use Serializable;
	use JsonSerializable;
	
	class SerializableContainer extends Container implements Serializable, JsonSerializable {
		
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
		
		public function jsonSerialize() {
			return $this->toArray();
		}
		
	}

}