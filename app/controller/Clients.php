<?php
/*
@auth: dwizzel
@date: 17-01-2018
@info: clients controller
*/

class Clients {

    private $reg;
    private $filename = 'clients';
    private $shema = array(
        'firstname' => array(
            'value' => '',
            'empty' => false,
            'error' => 'firstname must be filled'
        ),
        'lastname' => array(
            'value' => '',
            'empty' => false,
            'error' => 'lastname must be filled'
        ),
        'appointmentDate' => array(
            'value' => '',
            'empty' => true
        )
    );
	           
	public function __construct(&$reg){
        $this->reg = $reg;
        }
        
    public function process(){
        switch($this->reg->get('req')->getMethod()){
            case 'POST':
                break;
            default:
                $this->viewAll();
                break;    
        }        
    }

    public function processApi($args){
        switch($this->reg->get('req')->getMethod()){
            case 'POST':
                $this->setOne();
                break;
            case 'GET':
                if(isset($args['clientId'])){
                    $this->getOne(intVal($args['clientId']));
                }else{
                    $this->getAll();    
                }
                break;
            case 'PUT':
                $this->updateOne(intVal($args['clientId']));
                break;    
            case 'DELETE':
                $this->deleteOne(intVal($args['clientId']));
                break;
            default:
                break;    
        }        
    }

    private function setOne(){
        Cache::remove($this->filename);
        $err = false;
        $data = array();
        foreach($this->shema as $k=>$v){
            $data[$k] = $this->reg->get('req')->get($k);
            if(!$v['empty'] && $data[$k] == ''){
                $this->reg->get('resp')->addError($v['error'], '100');            
                $err = true;
            }
        }
        if($err){
            exit($this->reg->get('resp')->output());    
        }
        require_once(MODEL_PATH.'ClientsModel.php');
        $oClients = new ClientsModel($this->reg);
        $clientId = $oClients->setOne($data);
        if($clientId === false){
            $this->reg->get('resp')->addError('client not inserted', '100');    
        }else{
            $this->reg->get('resp')->put('clientId', $clientId);
        }
        exit($this->reg->get('resp')->output());
    }

    private function updateOne($clientId){
        Cache::remove($this->filename);
        $err = false;
        $data = array();
        foreach($this->shema as $k=>$v){
            $data[$k] = $this->reg->get('req')->get($k);
            if(!$v['empty'] && $data[$k] == ''){
                $this->reg->get('resp')->addError($v['error'], '100');            
                $err = true;
            }
        }
        if($err){
            exit($this->reg->get('resp')->output());    
        }
        require_once(MODEL_PATH.'ClientsModel.php');
        $oClients = new ClientsModel($this->reg);
        $this->reg->get('resp')->put('clientId', $oClients->updateOne($clientId, $data));
        exit($this->reg->get('resp')->output());
    }

    private function deleteOne($clientId){
        Cache::remove($this->filename);
        require_once(MODEL_PATH.'ClientsModel.php');
        $oClients = new ClientsModel($this->reg);
        $this->reg->get('resp')->put('clientId', $oClients->deleteOne($clientId));
        exit($this->reg->get('resp')->output());
    }

    private function getAll(){
        $data = Cache::read($this->filename);
        if($data === false){
            require_once(MODEL_PATH.'ClientsModel.php');
            $oClients = new ClientsModel($this->reg);
            $data = $oClients->getAll();
            $this->reg->get('resp')->puts($data);
            exit($this->reg->get('resp')->output($this->filename));
        }else{
            $this->reg->get('resp')->sendHeader();
            exit($data);
        }
    }

    private function getOne($clientId){
        require_once(MODEL_PATH.'ClientsModel.php');
        $oClients = new ClientsModel($this->reg);
        require_once(MODEL_PATH.'ClientsModel.php');
        $oClients = new ClientsModel($this->reg);
        $arr = $oClients->getOne($clientId);
        if($arr === false || !count($arr)){
            $this->reg->get('resp')->addError('client not found', '100');    
        }else{
            $this->reg->get('resp')->puts($arr);
        }
        exit($this->reg->get('resp')->output());
    }

    private function viewAll(){
        $data = array(
            'title' => 'Clients',
            'lang' => $this->reg->get('req')->getLang(),
            'content' => ''
        );
        require_once(MODEL_PATH.'MenuModel.php');
        $oMenu = new MenuModel($this->reg); 
        $data['top-menu'] = $oMenu->getTopMenu(); 
        require_once(VIEW_PATH.'Clients'.'.php');
    }

    
}


//END SCRIPT