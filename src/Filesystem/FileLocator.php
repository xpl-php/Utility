<?php

namespace xpl\Utility\Filesystem;

/**
 * FileLocator uses an array of pre-defined paths to find files.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FileLocator extends Container
{
	
	/**
	 * Returns a full path for a given file name.
	 *
	 * @param mixed   $name        The name of the file to locate, with file extension.
	 * @param bool    $first       Whether to return the first occurrence (default) or an array of filenames.
	 * @param string  $search_path	A path to search within first.
	 *
	 * @return string|array The full path to the file or an array of file paths.
	 */
	public function locateFile($name, $first = true, $search_path = null) {

		$filepaths = array();
		
		if (null !== $search_path) {
			
			$search_path = rtrim($search_path, '/\\').DIRECTORY_SEPARATOR;
			
			if (file_exists($file = $search_path.$name)) {
				
				if (true === $first) {
					return $file;
				}
				
				$filepaths[] = $file;
			}
		}

		foreach ($this->paths as $path) {
			
			if (file_exists($file = $path.$name)) {
				
				if (true === $first) {
					return $file;
				}
				
				$filepaths[] = $file;
			}
		}

		if (! $filepaths) {
			return null;
		}

		return array_values(array_unique($filepaths));
	}

}
