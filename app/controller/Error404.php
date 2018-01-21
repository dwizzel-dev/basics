<?php
/*
@auth: dwizzel
@date: 17-01-2018
@info: 404 controller
*/

class Error404 {

	private $reg;
	        
	public function __construct(&$reg){
        $this->reg = $reg;
		}

    public function process(){
        $data = array(
            'title' => 'Error 404',
            'lang' => $this->reg->get('req')->getLang(),
			'content' => 'page not found',
        );
        require_once(MODEL_PATH.'MenuModel.php');
        $oMenu = new MenuModel($this->reg);
        header('HTTP/1.0 404 Not Found', true);
		$data['top-menu'] = $oMenu->getTopMenu(); 
        require_once(VIEW_PATH.'Error404.php');
    }

    
}


//END SCRIPT