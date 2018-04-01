<?php
use evo\shutdown\ErrorHandler;
use evo\shutdown\callback\DynamicCallback;

error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(-1);
ini_set('display_errors', 1);

//echo (error_reporting() & E_USER_DEPRECATED) ? 'true' : 'false';

echo "<pre>";

if(!defined('EVO_AUTOLOAD')){
    define('EVO_AUTOLOAD', __DIR__.'/vendor/autoload.php');
}

require EVO_AUTOLOAD;

/*

if(isset($_GET['rebuild_eJinn'])){
    define('EJINN_CONF_PATH', __DIR__.'/src/evo/shutdown/eJinnConf.php');  
    require __DIR__.'/vendor/evo/ejinn/src/evo/ejinn/run.php';
    exit();
}

echo "<pre>";*/



$ErrorHandler = ErrorHandler::getInstance();





echo "Loaded\n";

/*set_time_limit(1);
sleep(2);*/

/*$A = new DynamicCallback(function(){
    echo "A\n";
}, 'A');

$A->setPriority(1);

$B = new DynamicCallback(function(){
    echo "B\n";
}, 'B');

$ErrorHandler->regesterCallback($B);
$ErrorHandler->regesterCallback($A);*/

trigger_error("Test", E_USER_DEPRECATED);


echo "Complete";

///
