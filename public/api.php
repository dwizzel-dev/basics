<?php
/*
@auth: dwizzel
@date: 17-01-2018
@info: main entry file for public api access
*/

//basic defined
require_once('define.php');

//dev specific
sleep(SLEEP);

//basic file required
require_once(CORE_PATH.'registry.php');
require_once(CORE_PATH.'request.php');
require_once(CORE_PATH.'response.php');
require_once(CORE_PATH.'db.php');
require_once(CORE_PATH.'json.php');
require_once(CORE_PATH.'globals.php');
require_once(CORE_PATH.'routes.php');

//routing
require_once(APP_PATH.'routes_api.php');

//instanciate basic registry for passing one global args by ref
$oReg = new Registry();
$oReg->set('db', new DB(
    DB_HOSTNAME, 
    DB_USERNAME, 
    DB_PASSWORD, 
    DB_DATABASE, 
    $oReg));

//minor check on db conn
if(!$oReg->get('db')->getStatus()){
    exit($oReg->get('db')->getErrorMsg());
    }

//instanciate the rest
$oReg->set('glob', new Globals());
$oReg->set('req', new Request());
$oReg->set('resp', new Response(new Json()));
$oReg->set('routes', new RoutesApi($oReg));
    
//were done 
$oReg->get('routes')->route();

//END SCRIPT