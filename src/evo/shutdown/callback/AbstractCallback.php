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
abstract class AbstractCallback implements CallbackInterface{
    
    /**
     * 
     * @var string
     */
    protected $id;
    
    /**
     * 
     * @var int
     */
    protected $priority = 100;
    
    /**
     * @var array
     */
    protected $args = [];
    
    /**
     * bitwise
     * 
     * @var int
     */
    protected $errorReporting = -1;
    
    /**
     * 
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::getId()
     */
    public function getId()
    {
        if(!$this->id) 
        
        return $this->id;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::getPriority()
     */
    public function getPriority()
    {
        return $this->args;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::setPriority()
     */
    public function setPriority($priority){
        $this->priority = $priority;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::getArgs()
     */
    public function getArgs(){
        return $this->args;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::setArgs()
     */
    public function setArgs(array $args){
        $this->args = $args;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::getErrorReporting()
     */
    public function getErrorReporting(){
        return $this->errorReporting;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::setErrorReporting()
     */
    public function setErrorReporting($level){
        $this->error_reporting = $level;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::run()
     */
    abstract public function run($message, $code, $filename, $lineno, $severity, $e);
    
    
    protected function canHandle($e){
        
    }
}