<?php

namespace Phpf\Util;

interface iEventable {
	
	public function on( $event, $callback );
	
	public function trigger( $event );
	
}
