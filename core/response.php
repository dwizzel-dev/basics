<?php
/*
@auth:	dwizzel
@date:	00-00-0000
@info:	response object
*/

class Response {
		
    private $arr;
    private $headers;
	private $json;
	private $error;
	
	public function __construct($json){
		$this->arr = array();
		$this->json = $json;
	}

	public function puts($data){
		if(is_array($data)){
			$this->recursivePut($this->arr, $data);
			return true;	
		}
		return false;
	}
	
	public function put($key, $data){
		if($key != ''){
			$this->arr[$key] = $data;
			return true;	
		}
		return false;
	}
	
	private function recursivePut(&$arr, $data){
		if(is_array($data)){
			foreach($data as $k=>$v){	
				if(is_array($v)){
					$arr[$k] = array();
					$this->recursivePut($arr[$k], $v);
				}else{
					$arr[$k] = $v;
				}
			}
		}
	}
	
	public function addHeader($header) {
		$this->headers[] = $header;
	}

	public static function redirect($url) {
		header('Location: ' . $url);
	    exit();
	}
		
	public function clear() {
		$this->arr = array();
	}
	
	public function addError($err) {
		$this->error = $err;
	}	
	
	public function output() {
		if(!headers_sent()) {
			$this->addHeader('Content-Type: application/json; charset=utf-8');
			foreach ($this->headers as $header) {
				header($header, true);
			}
		}
		if(count($this->error)){
			$this->arr = array('error' => $this->error);
		}
		$rtn = $this->json->encode($this->arr);
		if($rtn === false || is_numeric($rtn)){ 
           	return false;
		}
		return $rtn;
	}	

}
	


//END SCRIPT