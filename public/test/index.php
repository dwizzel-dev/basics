<?php

//xdebug : vi "/etc/php/7.1/mods-available/xdebug.ini"
/*
zend_extension=/usr/lib/php/20160303/xdebug.so
xdebug.force_error_reporting=1
xdebug.force_display_errors=1
xdebug.remote_enable=1
xdebug.autostart=1
xdebug.remote_autostart=1
xdebug.remote_connect_back=1
xdebug.remote_port=9999
xdebug.max_nesting_level=512
xdebug.remote_host=192.168.0.157
xdebug.remote_mode=req
xdebug.remote_log=/var/log/xdebug/connection.log
;xdebug.scream=1
xdebug.auto_trace=1
xdebug.idekey=vagrant
*/

//phpinfo();



Class Request{

    private $_data = array();
        
    public function __construct(){
        
    }

    public function __set($key, $value){
        $this->{$key} = $value;
    }

    public function __get($key){
        if(isset($this->{$key})){
            return $this->{$key};
        }
        return false;
    }

    public function set($key){
        $this->data[$key] = $value;
    }

    public function get($key){
        if(isset($this->$_datadata[$key])){
            return $this->$_data[$key];
        }
        return false;
    }

    public function data($method = 'get'){
        foreach($_GET as $k=>$v){
            $this->_data[$k] = $v;
        }
        switch($method){
            case 'put':
                parse_str(file_get_contents('php://input'), $_PUT);
                foreach($_PUT as $k=>$v){
                    $this->_data[$k] = $v;
                }       
                break;    
            case 'post':
                foreach($_POST as $k=>$v){
                    $this->_data[$k] = $v;
                }
                break; 
            default:
                break; 
        }
    }

}

Class Router{

    public function __construct(){

    }

    private function match($url, $path){
        $paths = explode('/', $path);
        $regs = explode('/', $url);
        if(count($paths) != count($regs)){
            return false;
        }
        $request = new Request();
        foreach($paths as $k=>$v){
            if(!isset($regs[$k])){
                return false;
            }   
            if(preg_match('/^.*\{([a-zA-Z]+)\}$/', $regs[$k], $match)){
                print_r($match);
                $request->{$match[1]} = $v;
            }else if($regs[$k] != $v){
                return false;
            }
        }
        return $request;
    }

    private function callClassMethod($args, Request &$req){

        list($class, $method) = explode('@', $args);
        if(method_exists($class, $method)){
            //$class::$method($req); //static
            (new $class)->{$method}($req); //dynamic
        }

    }

    public static function __callStatic($method, $args){
        if(strtoupper($method) != $_SERVER['REQUEST_METHOD']){
            return false;
        }
        $match = $args[0];
        $func = $args[1];
        $url = parse_url($_SERVER['REQUEST_URI']);
        $path = $url['path'];
        $host = $url['host'];
        
        $req = (new Router)->match($match, $path);
        if($req){
            $req->data($method);
            return (is_string($func))? (new Router)->callClassMethod($func, $req) : $func($req);
        }
        return false;
        
    }

    public function __call($method, $args){
        echo 'dynamic method "'.$method.'" dont exist'.PHP_EOL;
    }

}


class Controller{

    public function __construct(){

    }

    public function index(Request &$request){
        echo 'index';
        var_dump($request);
    }


}



(new Router)->test();

Router::get('/clients/', 'Controller@index');

Router::get('/clients/{id}/', function(Request $request){
    var_dump($request);
});
Router::get('/clients/{id}/accounts/{accountId}/', function(Request $request){
    var_dump($request);
});
Router::get('/clients/{id}/acc-{accountId}/', function(Request $request){
    var_dump($request);
});
Router::put('/clients/{id}', function(Request $request){
    var_dump($request);
});
Router::delete('/clients/{id}', function(Request $request){
    var_dump($request);
});
Router::post('/clients/', function(Request $request){
    var_dump($request);
});






