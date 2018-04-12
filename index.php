<?php
use evo\shutdown\ErrorHandler;
use evo\debug\Debug;
use evo\shutdown\callback\TextShutdownCallback;

//error_reporting(E_ALL ^ E_DEPRECATED);
//error_reporting(-1);
error_reporting(E_ALL ^ E_USER_DEPRECATED);
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

/*
array(6){
	["file"] => string(36) "C:\\UniServerZ\\www\\Shutdown\\index.php",
	["line"] => int(37),
	["function"] => string(13) "getTraceFirst",
	["class"] => string(15) "evo\\debug\\Debug",
	["type"] => string(2) "->",
	["args"] => array(0){},
}
 */
//evo_debug_dump(evo_debug_trace());


//evo_debug_dump(evo_debug_backtrace());


//evo_debug_dump(Debug::getInstance('functions')->getTraceFirst());

//string(70) "Output from FILE[ C:\\UniServerZ\\www\\Shutdown\\index.php ] on LINE[ 38 ]"
//evo_debug_dump(Debug::getInstance('functions')->getTraceFirstAsString());

$ErrorHandler = ErrorHandler::getInstance();
$ErrorHandler->alwaysConvertErrors(true);

$ErrorHandler->regesterCallback(new TextShutdownCallback());

define('EVO_ENVIROMENT', ErrorHandler::ENV_DEVELOPMENT);


//print_r(get_defined_constants(true));

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

//trigger_error("Test Deprecated", E_USER_NOTICE);
//trigger_error("Test Deprecated", E_USER_DEPRECATED);
//trigger_error("Test Deprecated", E_USER_WARNING);
trigger_error("Test Deprecated", E_USER_ERROR);
throw new Exception('This is a test error', 3000);


evo_debug_kill(0);

///
