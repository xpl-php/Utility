<?php

namespace xpl\Utility\Uri;

class Template {
	
	protected $template;
	protected $vars;
	protected $name;
	
	public function __construct($template, $name = null) {
		
		$this->template = trim($template, '/');
		
		foreach(explode('/', $this->template) as $part) {
			if (0 === strpos($part, '{')) {
				$this->vars[] = rtrim(ltrim($part, '{'), '}');
			}
		}
		
		if (isset($name)) {
			$this->setName($name);
		}
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getName() {
		return isset($this->name) ? $this->name : null;
	}
	
	public function build(array $args) {
		
		$search = $replace = array();
		
		foreach($this->vars as $var) {
			
			if (isset($args[$var])) {
				$srch = '{'.$var.'}';
				$repl = $args[$var];
			
			} else if ('?' === substr($var, -1)) {
				
				$var = rtrim($var, '?');
				
				if (isset($args[$var])) {
					$srch = '{'.$var.'}?';
					$repl = $args[$var];
				} else {
					$srch = '/{'.$var.'}?';
					$repl = '';
				}
			
			} else {
				return null;
			}
			
			$search[] = $srch;
			$replace[] = $repl;
		}
		
		return str_replace($search, $replace, $this->template);
	}
	
}
