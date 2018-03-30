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
     * H Readable severity names
     *
     * @var array
     */
    protected $serverityNames = array(
        -1                      => 'FATAL_ERROR',
        E_ERROR                 => 'FATAL_ERROR',
        E_RECOVERABLE_ERROR     => 'RECOVERABLE_ERROR',
        E_WARNING               => 'WARNING',
        E_PARSE                 => 'PARSE',
        E_NOTICE                => 'NOTICE',
        E_STRICT                => 'STRICT',
        E_DEPRECATED            => 'DEPRECATED',
        E_CORE_ERROR            => 'CORE_ERROR',
        E_CORE_WARNING          => 'CORE_WARNING',
        E_COMPILE_ERROR         => 'COMPILE_ERROR',
        E_COMPILE_WARNING       => 'COMPILE_WARNING',
        E_USER_ERROR            => 'EVO_ERROR',
        E_USER_WARNING          => 'EVO_WARNING',
        E_USER_NOTICE           => 'EVO_NOTICE',
        E_USER_DEPRECATED       => 'EVO_DEPRECATED',
        E_ALL                   => 'FATAL_ERROR'
    );
    
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
    abstract public function run($message, $code, $severity, $filename, $lineno, $e, $arg1 = null);
    
    /**
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
     *
     * @param int $severity
     * @return string
     */
    protected function getSeverityName($severity)
    {
        if (!isset($this->serverityNames[$severity])) {
            $severity = 1;
        }
        return $this->serverityNames[$severity];
    }
}
