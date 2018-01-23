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
    }

    protected function error404(){
        $this->reg->get('resp')->addHeader('HTTP/1.0 404 Not Found');
        exit($this->reg->get('resp')->output());
    }

    public function route(){
        if($this->match(['GET','POST'], 'clients/', function($args){
            return $this->useClients($args);
            })){
        }else if($this->match(['PUT','GET','DELETE'], 'clients/{clientId}/', function($args){
            return $this->useClients($args);    
            })){
        }else{
            $this->error404();
        }
    }

    private function useClients(&$args){
        if($args === false){
            return false;
        }
        require_once(CONTROLLER_PATH.'Clients.php');
        $oCtrl = new Clients($this->reg);
        $oCtrl->processApi($args);
        return true;
    }    

    

}

//END SCRIPT