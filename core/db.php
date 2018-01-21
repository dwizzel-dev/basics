<?php
/*
@auth:	dwizzel
@date:	17-01-2018
@info:	database object
*/

class DB{
	
	private $connection;
	private $reg;
	private $bStatus = false;
	private $database;
	private $err = false;
		
	public function __construct($hostname, $username, $password, $database, &$reg) {
		$this->database = $database;	
		$this->reg = $reg;
		$this->connection = new mysqli($hostname, $username, $password, $database);
		if($this->connection->connect_errno != 0){
			$this->setError($this->connection->connect_errno, $this->connection->connect_error);
			return;
		}
		if(!$this->connection->select_db($database)){
			$this->setError(0, 'database "'.$database.'" not found');
			return;
		}
		$this->connection->query("SET CHARACTER_SET_CONNECTION=utf8");
		$this->connection->query("SET SQL_MODE = ''");
		$this->bStatus = true;	
	}

	private function setError($errNum, $errMsg){
		$this->err = array(
			'num' => $errNum,
			'msg' => $errMsg
		);
	}
	
	public function getErrorMsg(){
		return (isset($this->err['msg']))? $this->err['msg'] : 'no message';
	}
		
	public function query($sql, $res = false){
		$resource = false;
		if(!$this->bStatus || !$this->connection || $this->connection->connect_errno != 0 || $this->connection->errno != 0){
			if(!$this->bStatus || !$this->connection){
				$this->setError(0, 'db not connected');
			}else if($this->connection->errno != 0){
				$this->setError($this->connection->errno, $this->connection->error);	
			}else if($this->connection->connect_errno != 0){
				$this->setError($this->connection->connect_errno, $this->connection->connect_error);	
			}
			return false;
		}
		$resource = $this->connection->prepare($sql);
		if(!$resource){
			$this->setError($this->connection->errno, $this->connection->error);	
			return false;
		}
		if(!$resource->execute()){
			$this->setError($this->connection->errno, $this->connection->error);	
			return false;
		}else{
			if($res){
				return $resource;
			}
			$query = new stdClass();
			if($resource->field_count != 0){
				$results = $this->fetchAssocStmt($resource);
				$query->num_rows = count($results);
				$query->row = isset($results[0]) ? $results[0] : array();
				$query->fields = $this->getFields($query->row);
				$query->rows = $results;
				$query->affected_rows = $resource->affected_rows;
			}else{
				$query->affected_rows = $resource->affected_rows;
				$query->insert_id = $resource->insert_id;
			}
			$resource->reset();
			$resource->close();
			unset($resource);
			return $query;	
		}
    }

	private function getFields($arr){
		$arrFields = array();	
		if(count($arr)){
			$arrFields = array();
			foreach($arr as $k=>$v){
				array_push($arrFields, $k);
			}
			return $arrFields;
		}
		return false;	
	}
		
	private function addSlashes($result){
		if(is_array($result)){
			foreach($result as $k=>$v){
				$result[$k] = $this->addSlashes($result[$k]);
			}
		}else{
			$result = stripslashes($result);
		}
		return $result;
	}
	
	public function escape($value){
		if($this->connection){
			return $this->connection->real_escape_string($value);
		}
		return false;
	}

	public function __destruct() {
		if($this->bStatus === true){
			$this->connection->close();
		}	
	}
	
	public function getStatus(){
		return $this->bStatus;
	}	
	
	public function fetchAssocStmt(&$stmt, $buffer = true){
		if($buffer){
			$stmt->store_result();
		}
		$fields = $stmt->result_metadata()->fetch_fields();
		$args = array();
		foreach($fields as $field){
			$key = str_replace(' ', '_', $field->name); 
			$args[$key] = &$field->name; 
		}
		call_user_func_array(array($stmt, 'bind_result'), $args);
		$results = array();
		while($stmt->fetch()){
			$results[] = array_map("self::cp", $args);
		}
		if($buffer){
			$stmt->free_result();
		}
		return $results;
	}

	public function cp($v){
		return $v;
	}
			
		
}



//END SCRIPT