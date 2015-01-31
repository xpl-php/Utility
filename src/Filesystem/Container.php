<?php

namespace xpl\Utility\Filesystem;

/**
 * Container is a set of pre-defined paths.
 */
class Container
{
	
	/**
	 * @var string
	 */
	protected $root;
	
	/**
	 * @var array
	 */
	protected $paths;
	
	/**
	 * @var boolean
	 */
	protected $isWindows;

	/**
	 * Constructor.
	 *
	 * @param string|array $paths Root directory path for this locator or an array of absolute directory paths.
	 */
	public function __construct($paths = null) {
			
		$this->isWindows = DIRECTORY_SEPARATOR === '\\';
		$this->paths = array();
		
		if (isset($paths)) {
			if (is_array($paths)) {
				$this->setPaths($paths);
			} else {
				$this->setRootPath($paths);
			}
		}
	}
	
	/**
	 * Set the root directory path for this file locator instance.
	 * 
	 * @param string $path Absolute root directory path (top-most directory).
	 */
	public function setRootPath($path) {
		$this->root = realpath($path).DIRECTORY_SEPARATOR;
	}
	
	/**
	 * Returns the root directory path for this locator instance, if set.
	 * 
	 * @return string Root directory path.
	 */
	public function getRootPath() {
		return isset($this->root) ? $this->root : null;
	}
	
	/**
	 * Sets a named directory path.
	 * 
	 * If a root path is set, paths can be given as relative to the root directory and
	 * will be changed to absolute paths when added.
	 * 
	 * @param string $name Directory identifier.
	 * @param string $path Absolute directory path, or relative path if root path set.
	 * 
	 * @throws \InvalidArgumentException if given a relative path and no root path is set.
	 */
	public function setPath($name, $path) {
		
		if (! $this->isAbsolutePath($path)) {
			
			if (null === $this->root) {
				throw new \InvalidArgumentException("Cannot set relative path without root path set.");
			}
			
			$path = $this->root.ltrim($path, '/\\');
		}
		
		if ($realpath = realpath($path)) {
			$this->paths[$name] = $realpath.DIRECTORY_SEPARATOR;
		}
	}
	
	/**
	 * Sets an array of named directory paths.
	 * 
	 * @param array $paths
	 */
	public function setPaths(array $paths) {
		foreach($paths as $name => $path) {
			$this->setPath($name, $path);
		}
	}
	
	/**
	 * Returns a named path, if it exists.
	 * 
	 * @param string $name Path identifier.
	 * @return string|null Directory path, if set, otherwise null.
	 */
	public function getPath($name) {
		return isset($this->paths[$name]) ? $this->paths[$name] : null;
	}
	
	/**
	 * Returns an array of all directory paths.
	 * 
	 * @return array
	 */
	public function getPaths() {
		return $this->paths;
	}

	/**
	 * Returns whether the file path is an absolute path.
	 *
	 * @param string $file A file path
	 *
	 * @return bool
	 */
	public function isAbsolutePath($file) {
		
		if ('/' === $file[0]) {
			return true;
		}
		
		if (! $this->isWindows) {
			return null !== parse_url($file, PHP_URL_SCHEME);
		}
		
		return (
			$file[0] == '\\' 
			|| (strlen($file) > 3 && ctype_alpha($file[0]) && $file[1] == ':' && ($file[2] == '\\' || $file[2] == '/')) 
			|| null !== parse_url($file, PHP_URL_SCHEME)
		);
	}
	
	/**
	 * Returns the root path as a string, if set.
	 * 
	 * @return string
	 */
	public function __toString() {
		return isset($this->root) ? $this->root : '';
	}

}
