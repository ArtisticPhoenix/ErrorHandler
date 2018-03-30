<?php
use evo\errorhandler\ErrorHandler;
//error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(-1);
ini_set('display_errors', 1);

echo "<pre>";

if(!defined('EVO_AUTOLOAD')){
    define('EVO_AUTOLOAD', __DIR__.'/vendor/autoload.php');
}

require EVO_AUTOLOAD;

if(isset($_GET['rebuild_eJinn'])){
    define('EJINN_CONF_PATH', __DIR__.'/src/evo/shutdown/eJinnConf.php');  
    require __DIR__.'/vendor/evo/ejinn/src/evo/ejinn/run.php';
    exit();
}

echo "<pre>";

$ErrorHandler = ErrorHandler::getInstance();


echo "Loaded\n";

/*set_time_limit(1);
sleep(2);*/


class test{
    function testCallback(){
        echo __FUNCTION__;
        
    }
}

$T = new test();

$ErrorHandler->regesterCallback([$T,'testCallback'], null, -1, 100);
$ErrorHandler->regesterCallback([$T,'testCallback']);

$ErrorHandler->handleException($T);

trigger_error("Test", E_USER_DEPRECATED);


echo "Complete";

///
