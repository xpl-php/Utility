<?php

namespace xpl\Utility\ClassUtils;

/**
 * PSR-0/4 autoloader.
 */
class Autoloader
{
	
	const PSR_0 = 0;
	const PSR_4 = 1;
	
	/**
	 * Namespace for classes which this autoloader handles.
	 * @var string
	 */
	protected $namespace;

	/**
	 * Directory path for namespace classes.
	 * @var string
	 */
	protected $path;

	/**
	 * Namespace separator.
	 * @var string
	 */
	protected $separator = '\\';

	/**
	 * Whether the autoloader is registered.
	 * @var boolean
	 */
	protected $registered = false;

	/**
	 * Whether to check if files exist before including them.
	 * @var boolean
	 */
	protected $checkFilesExist = false;
	
	/**
	 * Autoload mode. One of PSR_* class constants.
	 * @var integer
	 */
	protected $mode;
	
	/**
	 * The number of characters in the namespace.
	 * Used for PSR-4.
	 * @var int
	 */
	protected $namespaceStrlen;

	/**
	 * Instances of this class.
	 * @var array
	 */
	protected static $instances = array();

	/**
	 * Returns autoloader instance for given namespace.
	 *
	 * @param string $namespace Namespace
	 * @return \xpl\Utility\ClassUtils\Autoloader
	 */
	public static function instance($namespace) {
		if (! isset(static::$instances[$namespace]))
			static::$instances[$namespace] = new static($namespace);
		return static::$instances[$namespace];
	}

	/**
	 * Returns all autoloader instances.
	 * @return array Autoloader instances.
	 */
	public static function getInstances() {
		return static::$instances;
	}

	/**
	 * Constructs autoloader for given namespace.
	 *
	 * @param string $namespace Namespace.
	 * @return void
	 */
	protected function __construct($namespace) {
		$this->namespace = ltrim($namespace, '\\_');
		$this->namespaceStrlen = strlen($this->namespace);
		$this->mode = static::PSR_0;
	}

	/**
	 * Finds and loads a class (or interface or trait) in the namespace.
	 *
	 * This is the PSR-0 loader (default).
	 *
	 * @param string $class Classname to load.
	 */
	protected function loadPsr0($class) {

		// case-insensitive match, retain prefix
		if (0 === stripos($class, $this->namespace)) {
			$file = '';
			// find last occurance of the namespace separator
			if ($lastNsPos = strrpos($class, $this->separator)) {
	
				// extract the middle namespaces
				$localNs = substr($class, 0, $lastNsPos);
	
				// extract the base class name
				$class = substr($class, $lastNsPos + 1);
	
				// replace namespace separators with dir separator in middle namespaces only
				$file .= str_replace($this->separator, DIRECTORY_SEPARATOR, $localNs).DIRECTORY_SEPARATOR;
			}
	
			// convert underscores in classname to dir separator
			$file .= str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';
	
			$this->includeFile($file);
		}
	}

	/**
	 * Loads a class a la PSR-4
	 * @param string $class Classname
	 */
	protected function loadPsr4($class) {

		// PSR-4: case-sensitive match
		if (0 === strncmp($this->namespace, $class, $this->namespaceStrlen)) {
			
			// strip namespace prefix
			$class = substr($class, $this->namespaceStrlen + 1);
	
			$file = str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
			
			$this->includeFile($file);
		}
	}

	/**
	 * Prepends the namespace's base path and (possibly) includes a file.
	 *
	 * @param string $file Relative file path from loadPsr0() or loadPsr4().
	 */
	protected function includeFile($file) {

		$filepath = $this->path.DIRECTORY_SEPARATOR.$file;

		if (! $this->checkFilesExist || file_exists($filepath)) {
			include $filepath;
		}
	}

	/**
	 * Returns the autoloader instance's namespace.
	 *
	 * @return string Namespace for autoloader instance.
	 */
	public function getNamespace() {
		return $this->namespace;
	}

	/**
	 * Sets the path from which to load classes of the instance's namespace.
	 *
	 * @param string $dirpath Absolute path to directory.
	 * @return $this
	 */
	public function setPath($dirpath) {
		$this->path = rtrim($dirpath, '/\\');
		return $this;
	}

	/**
	 * Returns the directory path for the autoloader instance.
	 *
	 * @return string Directory path.
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Sets the namespace separator - one of "\" (default) or "_".
	 *
	 * @param string $sep Namespace separator.
	 * @return $this
	 */
	public function setSeparator($sep) {
		$this->separator = $sep;
		return $this;
	}

	/**
	 * Returns the namespace separator being used for the instance.
	 *
	 * @return string Namespace separator - usually '\', maybe '_'.
	 */
	public function getSeparator() {
		return $this->separator;
	}

	/**
	 * Sets whether to check if files exist before including them.
	 *
	 * @param boolean $value True to check if files exist, false to not check.
	 * @return $this
	 */
	public function setCheckFilesExist($value) {
		$this->checkFilesExist = (bool)$value;
		return $this;
	}
	
	/**
	 * Set the autoloader mode, one of the PSR_* class constants.
	 *
	 * @param integer $mode
	 * @return $this
	 * @throws InvalidArgumentException if mode is invalid.
	 * @throws RuntimeException if autoloader is already registered.
	 */
	public function setMode($mode) {
		
		if (! in_array($mode, array(static::PSR_0, static::PSR_4), true)) {
			throw new \InvalidArgumentException('Mode must be one of PSR_* class constants.');
		}
		
		if ($this->isRegistered()) {
			throw new \RuntimeException("Cannot change autoloader mode - already registered.");
		}
		
		$this->mode = $mode;
		
		return $this;
	}

	/**
	 * Returns true if autoloader is PSR-0.
	 *
	 * @return boolean True if PSR-0, otherwise false.
	 */
	public function isPsr0() {
		return static::PSR_0 === $this->mode;
	}

	/**
	 * Returns true if autoloader is PSR-4.
	 *
	 * @return boolean True if PSR-4, otherwise false.
	 */
	public function isPsr4() {
		return static::PSR_4 === $this->mode;
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

		if (! isset($this->path)) {
			throw new \RuntimeException("Cannot register autoloader - no path set.");
		}

		$func = 'load'.($this->isPsr4() ? 'Psr4' : 'Psr0');
		
		spl_autoload_register(array($this, $func), true, $prepend);

		$this->registered = true;

		return $this;
	}

	/**
	 * Unregisters the autoloader with spl_autoload_unregister().
	 *
	 * @return $this
	 */
	public function unregister() {

		$func = 'load'.($this->isPsr4() ? 'Psr4' : 'Psr0');
		
		spl_autoload_unregister(array($this, $func));

		$this->registered = false;

		return $this;
	}

}
