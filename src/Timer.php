<?php

namespace xpl\Utility;

class Timer 
{	
	protected $id;
	protected $start;
	protected $last;
	protected $end;
	protected $touches;
	protected $use_milliseconds = false;
	
	public function __construct($id = null, $start_time = null) {
			
		$this->touches = array();
		
		if (isset($id)) {
			$this->id = $id;
		}
		
		if (isset($start_time)) {
			$this->start($start_time);
			$this->touch("__CREATED__");
		}
	}
	
	public function useMilliseconds($value) {
		$this->use_milliseconds = (bool)$value;
	}
	
	public function start($time = null) {
		return $this->start = $this->last = isset($time) ? $time : microtime(true);
	}
	
	public function stop() {
		return $this->end = $this->last = microtime(true);
	}
	
	public function touch($tag) {
		
		$this->last = microtime(true);
		
		$this->touches[$tag] = array(
			'time' => $this->last,
			'elapsed' => $this->last - $this->start
		);
		
		return $this->last;
	}
	
	public function elapsed() {
		return $this->value(microtime(true) - $this->start);
	}
	
	public function sinceLast() {
		return $this->value(microtime(true) - $this->last);
	}
	
	public function sinceTouch($tag) {
		$time = microtime(true);
		return isset($this->touches[$tag]) 
			? $this->value($time - $this->touches[$tag]) 
			: null;
	}
	
	public function totalElapsed() {
		return $this->value($this->end - $this->start);
	}
	
	public function __toString() {
		
		$str = "<h2>Timer: `$this->id`</h2>";
		
		if (! empty($this->touches)) {
			
			$str .= '<h4>Touches</h4><ul>';
			$last = $this->start;
			
			foreach($this->touches as $tag => $arr) {
			
				$el = round(($arr['time'] - $last)*1000, 6);
				$elt = round(($arr['time'] - $this->start)*1000, 6);
			
				$str .= "<li><b>'$tag'</b> ";
				$str .= "<br>&nbsp; +{$el} ms since last";
				$str .= "<br>&nbsp; +{$elt} ms since start";
				$str .= "</li>";
			
				$last = $arr['time'];
			}
			
			$str .= '</ul>';
		}
		
		$str .= "<h4>Total elapsed time: ".(1000*($this->end - $this->start))." ms</h4>";
		
		return $str;
	}
	
	
	protected function value($num) {
		if ($this->use_milliseconds) {
			return $num*1000;
		}
		return $num;
	}
	
}
