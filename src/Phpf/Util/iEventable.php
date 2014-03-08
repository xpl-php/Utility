<?php

namespace Phpf\Util;

interface iEventable {
	
	public function on( $action, \Closure $callback );
	
	public function trigger( $action, array $args = array() );
	
}
