<?php
namespace evo\shutdown\callback;

use evo\shutdown\exception as E;
use evo\shutdown\ErrorHandler;

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
abstract class AbstractShutdownCallback implements ShutdownCallbackInterface
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
     *
     * @var string
     */
    protected $environment = 'production';
    
    /**
     * @var array
     */
    protected static $errorTypes = [
        E_ERROR                 => 'FATAL_ERROR',
        E_RECOVERABLE_ERROR     => 'RECOVERABLE_ERROR',
        E_WARNING               => 'WARNING',
        E_PARSE                 => 'PARSE_ERROR',
        E_NOTICE                => 'NOTICE',
        E_DEPRECATED            => 'DEPRECATED',
        E_CORE_ERROR            => 'FATAL_ERROR',
        E_CORE_WARNING          => 'WARNING',
        E_COMPILE_ERROR         => 'COMPILE_ERROR',
        E_COMPILE_WARNING       => 'COMPILE_WARNING',
        E_USER_ERROR            => 'EVO_ERROR',
        E_USER_WARNING          => 'EVO_WARNING',
        E_USER_NOTICE           => 'EVO_NOTICE',
        E_USER_DEPRECATED       => 'EVO_DEPRECATED',
        //E_ALL                   => 'FATAL_ERROR',
        //-1                      => 'FATAL_ERROR',
        //E_STRICT                => 'FATAL_ERROR',
    ];
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\ShutdownCallbackInterface::getId()
     */
    public function getId()
    {
        if (!$this->id) {
            throw new E\EvoShutdownInvalidCallback("No callback id defined");
        }
        
        return $this->id;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\ShutdownCallbackInterface::setId()
     */
    public function setId($id)
    {
        $this->id = $id;
    }
      
    /**
     *
     * @param string $prefix
     * @return string
     */
    protected function generateId($prefix=false)
    {
        if (!$prefix) {
            $prefix = get_called_class().'-';
        }
        return uniqid($prefix, true);
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\ShutdownCallbackInterface::getPriority()
     */
    public function getPriority()
    {
        return $this->priority;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\ShutdownCallbackInterface::setPriority()
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\ShutdownCallbackInterface::getArgs()
     */
    public function getArgs()
    {
        return $this->args;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\ShutdownCallbackInterface::setArgs()
     */
    public function setArgs(array $args)
    {
        $this->args = $args;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\ShutdownCallbackInterface::getEnvironment()
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
   
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\ShutdownCallbackInterface::setEnvironment()
     */
    public function setEnvironment($environment)
    {
        switch ($environment) {
            case ErrorHandler::ENV_DEVELOPMENT:
            case ErrorHandler::ENV_TESTING:
                $this->environment = $environment;
                break;
            case ErrorHandler::ENV_PRODUCTION:
            default:
                $this->environment = ErrorHandler::ENV_PRODUCTION;
                break;
        }
    }
    

    //======================== Exception Wrapper Methods ===========================//
    /**
     * get the class, on production enviroment this removes the namespace
     *
     * @param \Exception $e
     * @return string
     */
    protected function getClass($e)
    {
        $class = get_class($e);
        
        if (ErrorHandler::ENV_PRODUCTION == $this->environment) {
            return trim(strrchr('\\'.$class, '\\'), '\\');
        }
        
        return trim($class, '\\');
    }
    
    /**
      * Can be used to remove stuff in a error message you'd rather not show,
      * only removes content when Environment is ErrorHandler::ENV_PRODUCTION.
      * This will replace any matched pair of # word #
      *
      * @example <pre>
      * "File not found # home/yoursite/public_html/somefile.txt #"
      * "File not found # home/yoursite/public_html # somefile.txt"
      * "File not found # home/yoursite/public_html # somefile.txt "
      * returns
      * "File not found"
      * "File not found somefile.txt"
      *
      * @param string $string
      */
    protected function getMessage($e)
    {
        //convert " to '
        $message = str_replace('"', "'", $e->getMessage());
        
        if (ErrorHandler::ENV_PRODUCTION == $this->environment) {
            return trim(preg_replace('/\s*#(?:[^#]*+|(?0))*#\s*/', ' ', $message));
        }
            
        return $message;
    }

    /**
     * Accepts any Throwable PHP7+
     * @param \ErrorException $e
     * @return string|mixed
     */
    protected function getSeverity($e)
    {
        if (!method_exists($e, 'getSeverity')) {
            return E_ERROR;
        }

        return $e->getSeverity();
    }
    
    /**
     * Accepts any Throwable PHP7+
     * @param \ErrorException $e
     * @return string
     */
    protected function getSeverityName($e)
    {
        $severity = $this->getSeverity($e);
        
        if (!isset(self::$errorTypes[$severity])) {
            return 'UNKNOWN_ERROR';
        }
        
        return self::$errorTypes[$severity];
    }
    
    /**
     * Accepts any Throwable PHP7+
     * @param \ErrorException $e
     * @return string
     */
    protected function getLine($e)
    {
        return $e->getLine();
    }
    
    /**
     * Accepts any Throwable PHP7+
     * @param \ErrorException $e
     * @return string
     */
    protected function getFile($e)
    {
        return $e->getFile();
    }
    
    /**
     * Removes arguments from the stacktrace
     *    if the environment is not ErrorHandler::ENV_DEVELOPMENT
     *
     * Accepts any Throwable PHP7+
     * @param \ErrorException $e
     * @return string
     */
    protected function getTraceAsString($e)
    {
        $trace = $e->getTraceAsString();
        if (ErrorHandler::ENV_DEVELOPMENT != $this->environment) {
            return preg_replace('/\(.+\)$/m', '(...)', $trace);
        }

        return $trace;
    }
 
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\ShutdownCallbackInterface::execute()
     */
    abstract public function execute($e, $arg1 = null);
}
