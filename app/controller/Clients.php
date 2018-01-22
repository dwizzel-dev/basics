<?php
/*
@auth: dwizzel
@date: 17-01-2018
@info: clients controller
*/

class Clients {

	private $reg;
	           
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
            case 'PUT':
                $this->updateOne(intVal($args['clientId']));
                break;    
            case 'DELETE':
                $this->deleteOne(intVal($args['clientId']));
                break;
            default:
                $this->getAll();
                break;    
        }        
    }

    private function setOne(){
        $data = array(
            'firstname' => $this->reg->get('req')->get('firstname'),
            'lastname' => $this->reg->get('req')->get('lastname'),
            'appointmentDate' => $this->reg->get('req')->get('appointmentDate')
        );
        require_once(MODEL_PATH.'ClientsModel.php');
        $oClients = new ClientsModel($this->reg);
        $this->reg->get('resp')->put('clientId', $oClients->setOne($data));
        exit($this->reg->get('resp')->output());
    }

    private function updateOne($clientId){
        $data = array(
            'firstname' => $this->reg->get('req')->get('firstname'),
            'lastname' => $this->reg->get('req')->get('lastname'),
            'appointmentDate' => $this->reg->get('req')->get('appointmentDate')
        );
        require_once(MODEL_PATH.'ClientsModel.php');
        $oClients = new ClientsModel($this->reg);
        $this->reg->get('resp')->put('clientId', $oClients->updateOne($clientId, $data));
        exit($this->reg->get('resp')->output());
    }

    private function deleteOne($clientId){
        require_once(MODEL_PATH.'ClientsModel.php');
        $oClients = new ClientsModel($this->reg);
        $this->reg->get('resp')->put('clientId', $oClients->deleteOne($clientId));
        exit($this->reg->get('resp')->output());
    }

    private function getAll(){
        require_once(MODEL_PATH.'ClientsModel.php');
        $oClients = new ClientsModel($this->reg);
        $this->reg->get('resp')->put('clients', $oClients->getAll());
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
        require_once(MODEL_PATH.'ClientsModel.php');
        $oClients = new ClientsModel($this->reg);
        $data['clients'] = $oClients->getAll();
        require_once(VIEW_PATH.'Clients'.'.php');
    }

    
}


//END SCRIPT