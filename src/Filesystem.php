<?php

namespace xpl\Utility;

class Filesystem
{

	public static function copy($file, $dest) {
		return copy($file, $dest);
	}
	
	public static function unlink($file) {
		return unlink($file);
	}

	public static function mkdir($dir, $recursive = true, $mode = 0777) {
		return mkdir($dir, $mode, $recursive);
	}
	
	public static function scandir($dir, $sorting_order = SCANDIR_SORT_NONE) {
		return scandir($dir, $sorting_order);
	}
	
	public static function realpath($path) {
		return realpath($path);
	}

	public static function rmdir($dir, $recursive = true) {
		
		if (! $path = realpath($dir)) {
			return true;
		}
		
		$path .= DIRECTORY_SEPARATOR;
			
		if ($recursive) {
			
			foreach (static::scandir($path) as $item) {
				
				if ($item !== "." && $item !== "..") {
					
					if (is_dir($path.$item)) {
						static::rmdir($path.$item, true);
					} else {
						static::unlink($path.$item);
					}
				}
			}
		}
		
		return rmdir($path);
	}
	
	public static function mkdirIfNotExists($dirpath) {
		
		if (! realpath($dirpath)) {
			return static::mkdir($dirpath, true);
		}
		
		return true;
	}
	
	public static function getDirFiles($dir, $recursive = true, array $exclude = array(), array &$files = array()) {
		
		$path = realpath($dir).DIRECTORY_SEPARATOR;
		
		foreach(scandir($path) as $item) {
			
			if ($item !== "." && $item !== "..") {
				
				if (is_file($path.$item)) {
					
					if (empty($exclude) || ! in_array($item, $exclude)) {
						$files[] = $path.$item;
					}
					
				} else if ($recursive && is_dir($path.$item)) {
					static::getDirFiles($path.$item, $recursive, $exclude, $files);
				}
			}
		}
		
		return $files;
	}

	public static function moveDirContents($dir_path, $dest_path) {

		if (! static::mkdirIfNotExists($dest_path)) {
			throw new \RuntimeException("Could not create destination directory: '$dest_path'.");
		}
		
		$dest = realpath($dest_path).DIRECTORY_SEPARATOR;
		$source = realpath($dir_path).DIRECTORY_SEPARATOR;
		
		foreach(static::getDirFiles($source, true) as $file) {
			
			if (! static::mkdirIfNotExists(dirname($file))) {
				throw new \RuntimeException(sprintf('Could not create directory: "%s".', dirname($file)));
			}
			
			static::copy($file, $dest.str_replace($source, '', $file));
		}
	}

}
