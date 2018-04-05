<?php
use evo\shutdown\ErrorHandler;
use evo\shutdown\callback\DynamicCallback;
use evo\debug\Debug;

error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(-1);
ini_set('display_errors', 1);

//echo (error_reporting() & E_USER_DEPRECATED) ? 'true' : 'false';

echo "<pre>";

if(!defined('EVO_AUTOLOAD')){
    define('EVO_AUTOLOAD', __DIR__.'/vendor/autoload.php');
}

require EVO_AUTOLOAD;

Debug::regesterFunctions();


if(isset($_GET['rebuild_eJinn'])){
    define('EJINN_CONF_PATH', __DIR__.'/eJinnConf.php'); 
    $eJinnHome =  __DIR__.'/../ejinn/';
    require $eJinnHome.'vendor/autoload.php';
    require $eJinnHome.'src/evo/ejinn/run.php';
    exit();
}

echo "<pre>";



$ErrorHandler = ErrorHandler::getInstance();


//print_r(get_defined_constants(true));


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


evo_debug_kill(0);

///
