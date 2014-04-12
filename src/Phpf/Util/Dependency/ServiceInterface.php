<?php

namespace Phpf\Util\Dependency;

interface ServiceInterface {
	
	/**
	 * Constructor.
	 * 
	 * @return $this
	 */
	public function __construct();
	
	/**
	 * Starts the service.
	 * 
	 * @param array $args
	 * @return $this
	 */
	public function start(array $args);
	
	/**
	 * Whether service is started.
	 * 
	 * @return boolean True if service started, otherwise false.
	 */
	public function isStarted();
	
}
