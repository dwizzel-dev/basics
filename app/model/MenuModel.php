<?php
/*
@auth: dwizzel
@date: 17-01-2018
@info: menu model
*/

class MenuModel {

    public function __construct(&$reg){
        $this->reg = $reg;
        }

    public function getTopMenu(){
        return array(
            'home' => array(
                'text' => 'Home',
                'path' => $this->reg->get('routes')->get('home')
            ),
            'clients' => array(
                'text' => 'Clients',
                'path' => $this->reg->get('routes')->get('clients')
            )
            
        );
    }

    

}


//END SCRIPT