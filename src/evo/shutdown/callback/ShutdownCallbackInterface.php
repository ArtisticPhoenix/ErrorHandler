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
interface ShutdownCallbackInterface
{
    
    /**
     * Get the unique Identifier for this callback
     *
     * @return string
     */
    public function getId();
    
    /**
     *
     * @param string $id
     */
    public function setId($id);
    
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
     *
     * returns one of the ENV_* constants
     *
     * @return string
     */
    public function getEnvironment();
    
    /**
     * Set to one of the ENV_* constants
     *
     * @param string $environment
     */
    public function setEnvironment($environment);
  
    /**
     *
     * @param \Exception
     * @param mixed $arg1
     */
    public function execute($e, $arg1 = null);
}
