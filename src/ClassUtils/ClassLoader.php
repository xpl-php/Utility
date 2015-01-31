<?php

namespace xpl\Utility\ClassUtils;

/**
 * PSR-0/4 autoloader.
 */
class ClassLoader
{
	
	/**
	 * Array of PSR-0 namespaces and paths.
	 * 
	 * @var array
	 */
	protected $psr0 = array();

	/**
	 * Array of PSR-4 namespaces and paths.
	 * 
	 * @var array
	 */
	protected $psr4 = array();
	
	/**
	 * Array of fallback PSR-4 directories.
	 * 
	 * @var array
	 */
	protected $fallback_psr4 = array();

	/**
	 * Array of fallback PSR-0 directories.
	 * 
	 * @var array
	 */
	protected $fallback_psr0 = array();

	/**
	 * Whether the autoloader is registered.
	 * 
	 * @var boolean
	 */
	protected $registered = false;
	
	public function addPsr0($namespace, $path) {
		
		if (! $realpath = realpath($path)) {
			throw new \InvalidArgumentException("Invalid autoload path: '$path'.");
		}
		
		// PSR-0: case-insensitive match
		$key = strtolower(trim($namespace, '\\'));
		
		$this->psr0[$key] = $realpath.'/';
		
		return $this;
	}
	
	public function addPsr4($namespace, $path) {
		
		if (! $realpath = realpath($path)) {
			throw new \InvalidArgumentException("Invalid autoload path: '$path'.");
		}
		
		// PSR-4: case-sensitive match
		$key = trim($namespace, '\\');
		
		$this->psr4[$key] = $realpath.'/';
		
		return $this;
	}
	
	public function addFallbackPsr4($path) {
		
		if (! $realpath = realpath($path)) {
			throw new \InvalidArgumentException("Invalid autoload path: '$path'.");
		}
		
		$this->fallback_psr4[] = $realpath.'/';
		
		return $this;
	}

	
	public function addFallbackPsr0($path) {
		
		if (! $realpath = realpath($path)) {
			throw new \InvalidArgumentException("Invalid autoload path: '$path'.");
		}
		
		$this->fallback_psr0[] = $realpath.'/';
		
		return $this;
	}
	
	/**
	 * Finds and loads a class (or interface or trait) in the namespace.
	 *
	 * This is the PSR-0 loader (default).
	 *
	 * @param string $class Classname to load.
	 */
	protected function loadPsr0($class) {
			
		$found = false;
		
		// PSR-0: case-insensitive match
		$namespace = strtolower(strstr($class, '\\', true));

		if (isset($this->psr0[$namespace])) {
			
			$file = $this->psr0[$namespace];
		
			// find last namespace separator
			if ($lastNsPos = strrpos($class, '\\')) {
	
				// extract the middle namespaces
				$localNs = substr($class, 0, $lastNsPos);
	
				// extract the base class name
				$class = substr($class, $lastNsPos + 1);
	
				// replace namespace separators with dir separator in middle namespaces only
				$file .= str_replace('\\', DIRECTORY_SEPARATOR, $localNs).DIRECTORY_SEPARATOR;
			}
	
			// convert underscores in classname to dir separator
			$file .= str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';
	
			if (file_exists($file)) {
				include $file;
				$found = true;
			}
		}
		
		if (! $found) {
			$this->loadFallbacks($class);
		}
	}

	/**
	 * Loads a class a la PSR-4
	 * @param string $class Classname
	 */
	protected function loadPsr4($class) {

		$found = false;
		
		// PSR-4: case-sensitive match
		$namespace = strstr($class, '\\', true);

		if (isset($this->psr4[$namespace])) {
			
			$file = $this->psr4[$namespace];
			
			// strip namespace prefix
			$class = substr($class, strlen($namespace) + 1);
			
			$file .= str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
	
			if (file_exists($file)) {
				include $file;
				$found = true;
			}
		}
		
		if (! $found) {
			$this->loadFallbacks($class);
		}
	}
	
	protected function loadFallbacks($class) {
		
		$found = false;
		
		if (! empty($this->fallback_psr4)) {
			
			$classpath = str_replace('\\', DIRECTORY_SEPARATOR, $class);
			
			foreach($this->fallback_psr4 as $dirpath) {
		
				if (file_exists($file = $dirpath.$classpath.'.php')) {
					include $file;
					$found = true;
					break;
				}
			}
		}
		
		if (! $found && ! empty($this->fallback_psr0)) {
			
			$classpath = '';
			
			// find last namespace separator
			if ($lastNsPos = strrpos($class, '\\')) {
	
				// extract the middle namespaces
				$localNs = substr($class, 0, $lastNsPos);
	
				// extract the base class name
				$class = substr($class, $lastNsPos + 1);
	
				// replace namespace separators with dir separator in middle namespaces only
				$classpath .= str_replace('\\', DIRECTORY_SEPARATOR, $localNs).DIRECTORY_SEPARATOR;
			}
	
			// convert underscores in classname to dir separator
			$classpath .= str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';
			
			foreach($this->fallback_psr0 as $dirpath) {
				if (file_exists($file = $dirpath.$classpath.'.php')) {
					include $file;
					break;
				}
			}
		}
	}

	/**
	 * Whether the autoloader instance is registered.
	 *
	 * @return boolean True if registered, otherwise false.
	 */
	public function isRegistered() {
		return $this->registered;
	}

	/**
	 * Registers the autoloader using spl_autoload_register().
	 *
	 * @throws RuntimeException if no path is set.
	 * @return $this
	 */
	public function register($prepend = false) {

		spl_autoload_register(array($this, 'loadPsr0'), true, $prepend);
		spl_autoload_register(array($this, 'loadPsr4'), true, $prepend);

		$this->registered = true;

		return $this;
	}

	/**
	 * Unregisters the autoloader with spl_autoload_unregister().
	 *
	 * @return $this
	 */
	public function unregister() {
		
		spl_autoload_unregister(array($this, 'loadPsr0'));
		spl_autoload_unregister(array($this, 'loadPsr4'));

		$this->registered = false;

		return $this;
	}

}
