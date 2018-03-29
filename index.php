<?php
use evo\errorhandler\ErrorHandler;
//error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(-1);
ini_set('display_errors', 1);

if(!defined('EVO_AUTOLOAD')){
    define('EVO_AUTOLOAD', __DIR__.'/vendor/autoload.php');
}

require EVO_AUTOLOAD;

//http://localhost/ErrorHandler/index.php?config=C%3A%5CUniServerZ%5Cwww%5CErrorHandler%5Csrc%5Cevo%5Cerrorhandler%5CeJinnConf.php
//require __DIR__.'/vendor/evo/ejinn/index.php'; exit();

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
