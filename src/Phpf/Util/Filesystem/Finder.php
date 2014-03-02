<?php
/**
* @package Phpf.Util
* @subpackage Filesystem.Finder
*/

namespace Phpf\Util\Filesystem;

class Finder {
	
	protected $dirs = array();
	
	protected $workingGroup;
	
	protected $defaultGroup = '__DEFAULT__';
	
	protected $fileExtension = 'php';
	
	protected $globbed = array();
	
	protected $files = array();
	
	/**
	 * Set default group and file extension
	 */
	public function __construct( $default_group = null, $file_extension = null ){
		if ( !empty($default_group) )
			$this->setDefaultGroup($default_group);
		if ( !empty($file_extension) )
			$this->setFileExtension($file_extension);
	}
	
	/**
	 * Register a directory path, optionally under a non-default group.
	 */
	public function registerDirectory( $path, $group = '' ){
		
		$path = rtrim($path, '/\\');
		
		if ( empty($group) ){
			$group = $this->getGroup();
		}
		
		if ( !isset($this->dirs[ $group ]) )
			$this->dirs[ $group ] = array();
		
		$this->dirs[ $group ][ $path ] = $path;
		
		return $this;
	}
	
	/**
	 * Locate a file, optionally in a non-default group.
	 */
	public function locateFile( $name, $group = '', $ext = '' ){
		
		if ( empty($group) ){
			$group = $this->getGroup();
		}
		
		if ( empty($ext) ){
			if ( false !== $pos = strrpos($name, '.') ){
				$ext = substr($name, $pos);
				$name = str_replace('.'.$ext, '', $name);
			} else {
				$ext = $this->fileExtension;
			}
		} else {
			$ext = ltrim($ext, '.');
		}
		
		if ( isset($this->files[$group]["$name.$ext"]) ){
			return $this->files[$group]["$name.$ext"];
		}
		
		if ( !empty($this->dirs[$group]) ){
			
			foreach( $this->dirs[$group] as $dir ){
				
				$this->maybeGlobDir($dir);
				
				if ( $this->fileExists("$name.$ext", $dir) ){
					return $this->files[$group]["$name.$ext"] = "$dir/$name.$ext";
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Match files matching pattern, optionally in a non-default group.
	 */
	public function matchFiles( $shell_pattern = "*", $group = '' ){
		
		if (empty($group))
			$group = $this->getGroup();
		
		$files = array();
		
		foreach( $this->dirs[$group] as $dir ){
			
			$this->maybeGlobDir($dir);
			
			foreach($this->getGlob($dir) as $item){
				
				if ( fnmatch($shell_pattern, $item) ){
					$files[] = $item;
				}
			}
		}
		
		return $files;
	}
	
	/**
	 * Set the current working group.
	 */
	public function setWorkingGroup( $group ){
		$this->workingGroup = $group;
		return $this;
	}
	
	/**
	 * Get the current working group.
	 */
	public function getWorkingGroup(){
		return isset($this->workingGroup) ? $this->workingGroup : null;
	}
	
	/**
	 * Reset the current working group.
	 */
	public function resetWorkingGroup(){
		unset($this->workingGroup);
		return $this;
	}
	
	/**
	 * Set the default group.
	 */
	public function setDefaultGroup( $group ){
		$this->defaultGroup = $group;
		return $this;
	}
	
	/**
	 * Get the default group.
	 */
	public function getDefaultGroup(){
		return $this->defaultGroup;
	}
	
	/**
	 * Set the file extension.
	 */
	public function setFileExtension( $ext ){
		$this->fileExtension = ltrim($ext, '.');
		return $this;
	}
	
	/**
	 * Get the file extension.
	 */
	public function getFileExtension(){
		return $this->fileExtension;
	}
	
	/**
	 * Return whether dir has been globbed.
	 */
	protected function isGlobbed( $dir ){
		return isset($this->globbed[$dir]);
	}
	
	/**
	 * Glob a dir
	 */
	protected function globDir( $dir ){
		$this->globbed[ $dir ] = glob("$dir/*");
		return $this;
	}
	
	/**
	 * Glob a dir if unglobbed
	 */
	protected function maybeGlobDir( $dir ){
		if ( !$this->isGlobbed($dir) )
			$this->globDir($dir);
	}
	
	/**
	 * Get the results of a glob
	 */
	protected function getGlob( $dir ){
		return $this->globbed[$dir];
	}
	
	/**
	 * Does file exist in a globbed dir?
	 */
	protected function fileExists( $file, $dir ){
		return in_array($dir . '/' . $file, $this->globbed[$dir]);
	}
	
	/**
	 * Get the current group.
	 */
	protected function getGroup(){
		if ( !empty($this->workingGroup) )
			return $this->workingGroup;
		return $this->defaultGroup;
	}
	
}
