<?php
/*
@auth:	dwizzel
@date:	17-01-2018
@info:	basic routing abstract object
*/

abstract class BaseRoutes{

    protected $reg;
    protected $arr;
    
    public function __construct(&$reg){
        $this->reg = $reg;
        $this->arr = array();
        $this->init();
    }

    abstract protected function init();
    abstract public function route();
    abstract protected function error404();

    public function getPath($name){
        return (isset($this->arr[$name]['path']))? $this->arr[$name]['path'] : false;
    }

    public function getPaths(){
        $paths = array();
        foreach($this->arr as $k=>$v){
            array_push($paths, $v['path']);
        }
        return $paths;
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

    protected function match($method, $str, $func){
        if(is_array($method)){
            if(!in_array($this->reg->get('req')->getMethod(), $method)) return $func(false);
        }else{
            if($method != $this->reg->get('req')->getMethod()) return $func(false);
        }
        $paths = explode('/', $this->reg->get('req')->get('path'));
        $regs = explode('/', $str);
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