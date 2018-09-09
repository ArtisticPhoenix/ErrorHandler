<?php
use evo\debug\Debug;
use evo\shutdown\Shutdown;
use evo\shutdown\callback\ShutdownTextCallback;
use evo\shutdown\callback\ShutdownDynamicCallback;

//error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(-1);
//error_reporting(E_ALL ^ E_USER_DEPRECATED);
ini_set('display_errors', 1);

//echo (error_reporting() & E_USER_DEPRECATED) ? 'true' : 'false';

echo "<pre>";

if(!defined('EVO_AUTOLOAD')){
    define('EVO_AUTOLOAD', __DIR__.'/vendor/autoload.php');
}

require EVO_AUTOLOAD;

Debug::regesterFunctions();

$Shutdown = Shutdown::getInstance();
//$Shutdown->regesterCallback(new ShutdownTextCallback('default', 100, ['enviroment' => &$enviroment]));


$Shutdown->regesterCallback(new ShutdownDynamicCallback('dynamic', function($e){
    print_r($this->getTraceAsString($e));
},ShutdownDynamicCallback::ENV_DEVELOPMENT, 100));

$Shutdown->unRegisterHandler(Shutdown::HANDLE_ERROR);


trigger_error("Test Deprecated", E_USER_ERROR);


//$ErrorHandler->regesterCallback(new TextShutdownCallback());


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
//trigger_error("Test Deprecated", E_USER_ERROR);
//throw new Exception('This is a test error', 3000);


evo_debug_kill("Finished");

///
