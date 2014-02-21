<?php
/**
 * @package Wells.Util
 * @subpackage Log
 */

namespace Wells\Util;

class FileLog extends Log {
	
	public $file;

	public function __construct($file){
			
		if ( !is_writable($file) ){
			throw new \Exception("Unwritable file passed to Wells\Util\FileLogger: $file");
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

abstract class Log {
	
	abstract public function error($message);
	
	abstract public function log($message);
	
	abstract public function info($message);

}
