<?php
use evo\errorhandler\ErrorHandler;
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', 1);

if(!defined('EVO_AUTOLOAD')){
    define('EVO_AUTOLOAD', __DIR__.'/vendor/autoload.php');
}

require EVO_AUTOLOAD;

//http://localhost/ErrorHandler/index.php?config=C%3A%5CUniServerZ%5Cwww%5CErrorHandler%5Csrc%5Cevo%5Cerrorhandler%5CeJinnConf.php
require __DIR__.'/vendor/evo/ejinn/index.php';

/*echo "<pre>";

$ErrorHandler = ErrorHandler::getInstance();

echo "Loaded\n";

trigger_error("Test", E_USER_DEPRECATED);

throw new Exception("Test");

echo "Complete";

///
