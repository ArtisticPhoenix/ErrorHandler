<?php
namespace evo\shutdown\callback;

use evo\shutdown\exception as E;

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
abstract class AbstractCallback implements CallbackInterface
{
    
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
        if (!$this->id) {
            throw new E\InvalidCallback("No callback id defined");
        }
        
        return $this->id;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::getPriority()
     */
    public function getPriority()
    {
        return $this->priority;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::setPriority()
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::getArgs()
     */
    public function getArgs()
    {
        return $this->args;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::setArgs()
     */
    public function setArgs(array $args)
    {
        $this->args = $args;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::getErrorReporting()
     */
    public function getErrorReporting()
    {
        return $this->errorReporting;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::setErrorReporting()
     */
    public function setErrorReporting($level)
    {
        $this->error_reporting = $level;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\CallbackInterface::run()
     */
    abstract public function run($e, $arg1 = null);
    
    //========================== HELPERS =========================
    
    /**
     * Based on the current error_reporting level and the Errors serverity can we handle this error
     *
     * @param int $severity
     * @return boolean
     */
    protected function canHandle($severity)
    {
        $fatal = E_ERROR | E_PARSE | E_COMPILE_ERROR | E_USER_ERROR;
        return (error_reporting() == -1 || $fatal & $severity || error_reporting() & $severity) ? true : false;
    }

    /**
     * Get the severity of this error
     *
     * Unfortiantly PHP has several exeptions/error classes that dont provide a severity level
     * We'll assum these are E_ERROR
     *
     * @param \ErrorException $e
     * @return int
     */
    protected function getSeverity($e)
    {
        if(!is_a($e, \ErrorException::class)){
            //if ErrorException is not an ancestor
            $severity = E_ERROR;
        }else{
            $severity = $e->getSeverity();
        }
        return $severity;
    }

    /**
     * Get a human readable name for a severity
     *
     * @param \Exception $e
     * @return string
     */
    protected function getSeverityName($e)
    {
        if (!isset($this->serverityNames[$severity])) {
            $severity = 1;
        }
        return $this->serverityNames[$severity];
    }
}
