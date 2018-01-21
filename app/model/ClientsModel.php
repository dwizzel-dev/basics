<?php
/*
@auth: dwizzel
@date: 17-01-2018
@info: clients model
*/

class ClientsModel {

	private $reg;
	        
	public function __construct(&$reg){
        $this->reg = $reg;
	}

    public function getAll($order = 'clientId', $page = '', $limit = ''){
        $query =  'SELECT * FROM clients ORDER BY '.$order.';';
        $rtn = $this->reg->get('db')->query($query);
        if($rtn === false){
            exit($this->reg->get('db')->getErrorMsg());            
        }
        return $rtn->rows;
    }

    public function setOne(&$data){
        $fields = '';
        $values = '';
        foreach($data as $k=>$v){
            $fields .= '`'.$k.'`,';
            $values .= '"'.$this->reg->get('db')->escape($v).'",';
        }
        $fields = substr($fields, 0, strlen($fields) - 1);
        $values = substr($values, 0, strlen($values) - 1);
        $query = 'INSERT INTO clients ('.$fields.') VALUES ('.$values.');';
        $rtn = $this->reg->get('db')->query($query);
        if($rtn === false){
            return false;
        }
        return $rtn->insert_id;
    }

    public function updateOne($clientId, &$data){
        $set = 'SET ';
        foreach($data as $k=>$v){
            $set .= '`'.$k.'` = "'.$this->reg->get('db')->escape($v).'",';
        }
        $set = substr($set, 0, strlen($set) - 1);
        $query = 'UPDATE clients '.$set.' WHERE clientId = "'.$clientId.'";';
        $rtn = $this->reg->get('db')->query($query);
        if($rtn === false){
            return false;
        }
        return $clientId;
    }

    public function deleteOne($clientId){
        $query = 'DELETE FROM clients WHERE clientId = "'.$clientId.'";';
        $rtn = $this->reg->get('db')->query($query);
        if($rtn === false){
            return false;
        }
        return $clientId;
    }

    
}


//END SCRIPT