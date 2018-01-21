<?php
/*
@auth:	Dwizzel
@date:	17-01-2018
@info:	global vars
*/

class Globals {

	private $arr;
	
	public function __construct() {
		$this->arr = array();
	}

	public function get($key) {
		return (isset($this->arr[$key])) ? $this->arr[$key] : false;
	}
	
	public function set($key, $value) {
		$this->arr[$key] = $value;
	}

	public function has($key) {
    	return isset($this->arr[$key]);
	}

	public function getArrValue($key, $name) {
		return (isset($this->arr[$key][$name])) ? $this->arr[$key][$name] : false;
	}	
		
}


//END SCRIPT