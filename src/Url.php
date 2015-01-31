<?php

namespace xpl\Utility;

class Url {
	
	/** 
	 * Current URI path
	 * @var string
	 */
	protected $path;
	
	/**
	 * Current URI query.
	 * @var string
	 */
	protected $query;
	
	/**
	 * Current URL.
	 * @var string
	 */
	protected $url;
	
	/**
	 * Build a URL using the current request path and query.
	 * 
	 * @param string $request_path Request URI path.
	 * @param string $request_query [Optional] Request query string.
	 */
	public function __construct($request_path, $request_query = null) {
		
		$this->path = '/'.trim($request_path, '/');
		$this->query = (string)$request_query;
		
		$this->url = $this->to($this->path, $this->query);
	}
	
	/**
	 * The current request URL, or other information ('path' or 'query').
	 * 
	 * @param string $var [Optional] One of "path", "query" or "url". Default "url".
	 * @return string
	 */
	public function getCurrent($var = null) {
		
		if (empty($var)) {
			return $this->url;
		}
		
		switch(strtolower($var)) {
			case 'path':
			case 'uri':
				return $this->path;
			case 'query':
				return $this->query;
			case 'url':
				return $this->url;
			default:
				return null;
		}
	}
	
	/**
	 * Returns the current URL string.
	 * @return string
	 */
	public function __toString() {
		return $this->url;
	}
	
	/**
	 * Returns the host (subdomain + domain) for an application.
	 * 
	 * @param string $id [Optional] Application ID.
	 * @return string
	 */
	public function getDomain($id = null) {
		
		$domain = env('domain');
		
		if (! isset($id)) {
			$id = env('app');
		}
		
		return ('main' === $id) ? $domain : "{$id}.{$domain}";
	}
	
	/**
	 * URL to a path in the current application.
	 * 
	 * @param string $path [Optional] Relative path.
	 * @param string $query [Optional] URL query.
	 * @return string
	 */
	public function to($path = null, $query = null) {
		return $this->toApp(null, $path, $query);
	}
	
	/**
	 * URL to an application.
	 * 
	 * @param string $id [Optional] Application ID.
	 * @param string $path [Optional] Relative path.
	 * @param string $query [Optional] URL query.
	 * @return string
	 */
	public function toApp($id = null, $path = null, $query = null) {
		
		if (! isset($id)) {
			$id = env('app');
		}
		
		$parts = array(
			'host' => $this->getDomain($id)
		);
		
		if (! empty($path)) {
			$parts['path'] = '/'.trim($path, '/');
		}
		
		if (! empty($query)) {
			$parts['query'] = $query;
		}
		
		return http_build_url('/', $parts);
	}
	
	/**
	 * URL to a file in DOCROOT path.
	 * 
	 * @param string $filepath Absolute path to the file.
	 * @return string
	 */
	public function toFile($filepath) {
		return http_build_url(str_replace(DOCROOT, '', '/'.ltrim($filepath, '/\\')));
	}
	
	/**
	 * URL to Google-hosted jQuery library.
	 * 
	 * @param string $version [Optional] jQuery version string. Default "1.11.1"
	 * @return string
	 */
	public function toJquery($version = '1.11.1') {
		return "//ajax.googleapis.com/ajax/libs/jquery/{$version}/jquery.min.js";
	}

}
