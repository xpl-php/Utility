<?php
/**
 * @package Phpf.Util
 * @subpackage Log
 */

namespace Phpf\Util;

class FileLog implements iLogger {
	
	public $file;

	public function __construct($file){
			
		if ( !is_writable($file) ){
			throw new \Exception("Unwritable file passed to Phpf\Util\FileLogger: $file");
		}
		
		$this->file = $file;
	}

	public function write($message, $severity) {
			
		$message = date('Y-m-d H:i:s') . "\t$severity\t$message\n";
		
		file_put_contents($this->file, $message, FILE_APPEND | LOCK_EX);

		return true;
	}

	public function error($message) {
		return $this->write($message, 'error');
	}

	public function log($message) {
		return $this->write($message, 'log');
	}

	public function info($message) {
		return $this->write($message, 'info');
	}
	
	public function deprecated($message){
		return $this->write($message, 'deprecated');
	}

}

interface iLogger {
	
	public function write($message, $severity);
	
	public function error($message);
	
	public function log($message);
	
}
