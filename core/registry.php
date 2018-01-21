<?php
/*
@auth:	dwizzel
@date:	17-01-2018
@info:	registry for passing arguments has object
*/

class Registry {
    
    private $arr;
    
	public function __construct(){
		$this->arr = array();
	}
	
	public function get($key){
		return (isset($this->data[$key]) ? $this->data[$key] : false);
	}
    
    public function set($key, $value){
		$this->data[$key] = $value;
	}
    
    public function has($key){
    	return isset($this->data[$key]);
    }

}

//END SCRIPT