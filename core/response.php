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
		$this->error = array();
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
	
	public function addError($errmsg, $errno = 0) {
		array_push($this->error, array(
			'code' => $errno,
			'message' => $errmsg
		));
	}
	
	public function output($cache = false) {
		if(count($this->error)){
			$this->addHeader('HTTP/1.0 400 Bad Request');
			$this->arr = $this->error;
		}
		$this->sendHeader();
		$rtn = $this->json->encode($this->arr);
		if($rtn === false || is_numeric($rtn)){ 
           	return false;
		}
		if($cache){
			Cache::write($cache, $rtn);	
		}
		return $rtn;
	}
	
	public function sendHeader() {
		if(!headers_sent()) {
			$this->addHeader('Cache-Control:no-cache, private');
			$this->addHeader('Content-Type: application/json; charset=utf-8');
			foreach ($this->headers as $header) {
				header($header, true);
			}
		}
	}

}
	


//END SCRIPT