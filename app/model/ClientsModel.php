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
        $rtn = $this->reg->get('db')->query(
            'SELECT * FROM clients ORDER BY '.$order.';'
        );
        if($rtn === false){
            exit($this->reg->get('db')->getErrorMsg());            
        }
        return $rtn->rows;
    }

    public function setOne($data){
        $fields = '';
        $values = '';
        foreach($data as $k=>$v){
            $fields .= '`'.$k.'`,';
            $values .= '"'.$this->reg->get('db')->escape($v).'",';
        }
        $fields = substr($fields, 0, strlen($fields) - 1);
        $values = substr($values, 0, strlen($values) - 1);
        $query = 'INSERT INTO clients ('.$fields.') VALUES ('.$values.')';
        $rtn = $this->reg->get('db')->query($query);
        if(!$rtn){
            return false;
        }
        return $rtn->insert_id;
    }

    
}


//END SCRIPT