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
        return $this->setActive(array(
            'home' => array(
                'text' => 'Home',
                'path' => $this->reg->get('routes')->getPath('home'),
                'active' => false,
                'submenu' => false
            ),
            'clients' => array(
                'text' => 'Clients',
                'path' => $this->reg->get('routes')->getPath('clients'),
                'active' => false,
                'submenu' => false
            )
        ));
    }

    private function setActive($arr, $level = 0){
        $paths = explode('/', $this->reg->get('req')->get('path'));
        if(isset($paths[$level])){
            foreach($arr as $k=>$v){
                if(preg_match('/^\/?'.$paths[$level].'\/?$/', $v['path'])){
                    $arr[$k]['active'] = true;
                    if(is_array($v['submenu'])){
                        $this->setActive($arr[$k], $level + 1);
                    }
                    break;
                }
            }
        }
        return $arr;
    }

    public function active($path){
        foreach($this->arr as $k=>$v){
            if(preg_match('/^'.$path.'$/', $v['path'])){
                return true;
            }
        }
        return false;
    }


    

}


//END SCRIPT