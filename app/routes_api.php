<?php
/*
@auth:	dwizzel
@date:	17-01-2018
@info:	basic routing for the api
*/

class RoutesApi extends BaseRoutes{

    public function __construct(&$reg){
        parent::__construct($reg);
    }

    protected function init(){
        $this->set('clients', '/clients/', CONTROLLER_PATH.'Clients');
    }

    public function route(){
        if($this->match('clients/', function($args){
            return $this->useClients($args);
            })){
        }else if($this->match('clients/{clientId}/', function($args){
            return $this->useClients($args);    
            })){
        }else{
            //
        }
    }

    private function useClients(&$args){
        if($args === false){
            return false;
        }
        require_once($this->getController('clients'));
        $oCtrl = new Clients($this->reg);
        $oCtrl->processApi($args);
        return true;
    }    

    

}

//END SCRIPT