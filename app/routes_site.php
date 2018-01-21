<?php
/*
@auth:	dwizzel
@date:	17-01-2018
@info:	basic routing for the site
*/

class RoutesSite extends BaseRoutes{

    public function __construct(&$reg){
        parent::__construct($reg);
    }

    protected function init(){
        $this->set('home', '/', CONTROLLER_PATH.'Home');
        $this->set('error404', '/404/', CONTROLLER_PATH.'Error404');
        $this->set('clients', '/clients/', CONTROLLER_PATH.'Clients');
        $this->set('google', 'https://www.google.ca', '');
    }

    public function route(){
        $paths = explode('/', $this->reg->get('req')->get('path'));
        if(count($paths) >= 1){
            switch('/'.$paths[0]){
                case '/':
                    require_once($this->getController('home'));
                    $oCtrl = new Home($this->reg);
                    $oCtrl->process();
                    break;
                case '/clients':
                    require_once($this->getController('clients'));
                    $oCtrl = new Clients($this->reg);
                    $oCtrl->process();
                    break;    
                default:
                    require_once($this->getController('error404'));
                    $oCtrl = new Error404($this->reg);
                    $oCtrl->process();
                    break;
            }
        }        
    }
}

//END SCRIPT