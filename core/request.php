<?php
/*
@auth: dwizzel
@date: 17-01-2018
@info: request object
*/

class Request {
        
	private $arr;
	private $method;
	private $locale = LOCALE;
	private $lang = LANG;
	
	public function __construct(){
		$this->arr = $this->recursiveBuild(
			$_POST,
			$this->recursiveBuild($_GET)
		);
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->setLocale($this->get('locale'));
	}

	public function getLocale(){
		return $this->locale;
	}

	public function getLang(){
		return $this->lang;
	}

	public function getMethod(){
		return $this->method;		
	}

	public function get($key){
		return (isset($this->arr[$key]))? $this->arr[$key] : false;
	}
	
	public function set($key, $value){
		$this->arr[$key] = $value;
	}

	public function setLocale($locale){
		if($locale === true && preg_match('/^[a-z]{2}_[A-Z]{2}$/', $locale) && in_array($locale, explode(',', LOCALE_ACCEPTED))){
			$this->locale = $locale;
			$this->lang = mb_strtolower(substr($this->locale, 0, 2));
		}
	}

	private function recursiveBuild($postdata, $arr = array()){
		foreach($postdata as $k=>$v){
			if(is_array($v)){
				$arr = $this->recursiveBuild($v, $arr);
			}else{
				$arr[$k] = $v;
			}
		}
		return $arr;
	}	
}


//END SCRIPT