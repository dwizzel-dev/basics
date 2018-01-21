<?php
/*
@auth: dwizzel
@date: 17-01-2018
@info: home controller
*/

class Home {

	private $reg;
	           
	public function __construct(&$reg){
        $this->reg = $reg;
		}

    public function process(){
        $data = array(
            'title' => 'Homepage',
            'lang' => $this->reg->get('req')->getLang(),
			'content' => 'blablablabla...',
        );
        require_once(MODEL_PATH.'MenuModel.php');
        $oMenu = new MenuModel($this->reg); 
        $data['top-menu'] = $oMenu->getTopMenu(); 
        require_once(VIEW_PATH.'Home.php');
    }

    
}


//END SCRIPT