<?php
namespace evo\shutdown;

/**
 *
 * (c) 2016 Hugh Durham III
 *
 * For license information please view the LICENSE file included with this source code.
 *
 * @author HughDurham {ArtisticPhoenix}
 * @package Evo
 * @subpackage ErrorHandler
 *
 */
interface CallbackInterface{
    
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
     * 
     * @example <pre>
     * -1
     * E_ALL
     * E_ALL ^ E_DEPRECATED
     * E_ERROR | E_WARNING 
     */
    public function getErrorReporting();
    
    public function setErrorReporting();
    
}