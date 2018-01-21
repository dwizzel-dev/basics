<?php
/*
@auth:	dwizzel
@date:	17-01-2018
@info:	basic routing abstract object
*/

abstract class BaseRoutes{

    protected $reg;
    
    public function __construct(&$reg){
        $this->reg = $reg;
        $this->init();
    }

    abstract protected function init();
    abstract public function route();

    public function get($name){
        return (isset($this->arr[$name]['path']))? $this->arr[$name]['path'] : false;
    }

    protected function getController($name){
        return (isset($this->arr[$name]['controller']))? $this->arr[$name]['controller'].'.php' : false;
    }

    protected function set($name, $path, $controller){
        $this->arr[$name] = array(
            'path' => $path,
            'controller' => $controller
        );
    }

    protected function match($str, $func){
        $paths = explode('/', $this->reg->get('req')->get('path'));
        $regs = explode('/', $str);
        //print_r($paths);
        //print_r($regs);
        //echo '------------'.PHP_EOL;
        if(count($paths) != count($regs)){
            return $func(false);
        }
        $data = array();
        foreach($paths as $k=>$v){
            if(!isset($regs[$k])){
                return $func(false);
            }   
            if(preg_match('/^.*\{([a-zA-Z]+)\}$/', $regs[$k], $match)){
                $data[$match[1]] = $v;
            }else if($regs[$k] != $v){
                return $func(false);
            }
        }
        return $func($data);
    }

}

//END SCRIPT