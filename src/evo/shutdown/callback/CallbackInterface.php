<?php
namespace evo\shutdown\callback;

/**
 *
 * (c) 2016 Hugh Durham III
 *
 * For license information please view the LICENSE file included with this source code.
 *
 * @author HughDurham {ArtisticPhoenix}
 * @package Evo
 * @subpackage Shutdown
 *
 */
interface CallbackInterface
{
    
    /**
     * Get the unique Identifier for this callback
     *
     * @return string
     */
    public function getId();
    
    /**
     * get the priority for this callback
     *
     * @return int
     */
    public function getPriority();
    
    /**
     * set the priorty for this callback
     *
     * Sort order ASC, lower numbers execute first
     *
     * @param int $priority
     */
    public function setPriority($priority);
    
    /**
     * return additional arguments for the callback
     *
     * @return array
     */
    public function getArgs();
    
    /**
     * set additional args to be passed to the callback
     *
     * @param array $args
     */
    public function setArgs(array $args);
    
    /**
     * bitwise reporting level, simular to PHP's error_reporting()
     *
     * @return int
     */
    public function getErrorReporting();
    
    /**
     * bitwise reporting level, simular to PHP's error_reporting()
     *
     * @param int $level
     *
     * @example <pre>
     * -1
     * E_ALL
     * E_ALL ^ E_DEPRECATED
     * E_ERROR | E_WARNING
     */
    public function setErrorReporting($level);
    

    /**
     *
     * @param string $message
     * @param int $code
     * @param int $severity - error level (error_reporting bitwise)
     * @param string $filename
     * @param int $lineno
     * @param \Exception $e - @todo \Throwable after PHP7
     * @param mixed $arg1 - additional args can follow
     *
     * @return bool - return true to singnal that the error was handled and prevent further callbacks for executing
     *
     *
     */
    public function run($message, $code, $severity, $filename, $lineno, $e, $arg1 = null);
}
