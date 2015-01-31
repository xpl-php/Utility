<?php

namespace xpl\Utility\Traits;

trait OptionsTrait {
	
	/**
	 * @var array
	 */
	protected $_options = array();
	
	/**
	 * Sets an array of options.
	 * 
	 * @param array $options
	 */
	public function setOptions(array $options) {
		$this->_options = $options;
	}
	
	/**
	 * Sets an option.
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function setOption($key, $value) {
		$this->_options[$key] = $value;
	}
	
	/**
	 * Returns an array of options.
	 * 
	 * @return array
	 */
	public function getOptions() {
		return $this->_options;
	}
	
	/**
	 * Returns an option value if set.
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function getOption($key) {
		return isset($this->_options[$key]) ? $this->_options[$key] : null;
	}
	
	public function hasOption($key) {
		return isset($this->_options[$key]);
	}
	
	public function unsetOption($key) {
		unset($this->_options[$key]);
	}
	
}
