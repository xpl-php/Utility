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
	
	public function __construct( $default_group = null, $file_extension = null ){
		if ( !empty($default_group) )
			$this->setDefaultGroup($default_group);
		if ( !empty($file_extension) )
			$this->setfileExtension($file_extension);
	}
	
	public function setWorkingGroup( $group ){
		$this->workingGroup = $group;
		return $this;
	}
	
	public function resetWorkingGroup(){
		unset($this->workingGroup);
		return $this;
	}
	
	public function setDefaultGroup( $group ){
		$this->defaultGroup = $group;
		return $this;
	}
	
	public function getDefaultGroup(){
		return $this->defaultGroup;
	}
	
	public function setfileExtension( $ext ){
		$this->fileExtension = ltrim($ext, '.');
		return $this;
	}
	
	public function getfileExtension(){
		return $this->fileExtension;
	}
	
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
	
	public function locateFile( $name, $group = '', $ext = '' ){
		
		if ( empty($group) ){
			$group = $this->getGroup();
		}
		
		if ( empty($ext) ){
			$ext = $this->fileExtension;
		} else {
			$ext = ltrim($ext, '.');
		}
		
		if ( isset($this->files[$group]["$name.$ext"]) ){
			return $this->files[$group]["$name.$ext"];
		}
		
		foreach( $this->dirs[$group] as $dir ){
			
			if ( !$this->isGlobbed($dir) ){
				$this->globDir($dir);
			}
			
			if ( $this->fileExists("$name.$ext", $dir) ){
				return $this->files[$group]["$name.$ext"] = "$dir/$name.$ext";
			}
		}
		
		return false;
	}
	
	protected function isGlobbed( $dir ){
		return isset($this->globbed[$dir]);
	}
	
	protected function globDir( $dir ){
		$this->globbed[ $dir ] = glob("$dir/*");
		return $this;
	}
	
	protected function fileExists( $file, $dir ){
		return in_array($dir . '/' . $file, $this->globbed[$dir]);
	}
	
	protected function getGroup(){
		if ( isset($this->workingGroup) )
			$group = $this->workingGroup;
		else
			$group = $this->defaultGroup;
		return $group;
	}
		
	protected function __construct(){}
	
}
