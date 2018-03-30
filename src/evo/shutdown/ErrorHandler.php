<?php
namespace evo\shutdown;

use evo\shutdown\Exception as E;
use evo\shutdown\callback\CallbackInterface;

/**
 *
 * (c) 2016 Hugh Durham III
 *
 * For license information please view the LICENSE file included with this source code.
 * 
 * Throwing errors from within the error handler is generally a bad idea.
 *
 * @author HughDurham {ArtisticPhoenix}
 * @package Evo
 * @subpackage Shutdown
 *
 */
final class ErrorHandler{
    /**
     * @var self
     */
    protected static $INSTANCE;
    
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
     * @var array
     */
    protected $callbacks = [];
    
    /**
     * no public access
     */
    private function __clone()
    {
    }
    
    /**
     * no public access
     */
    private function __construct()
    {
        //regester out custom handlers.
        register_shutdown_function([$this,"handleShutdown"]);
        set_error_handler([$this,"handleError"]);
        set_exception_handler([$this, "handleException"]);
    }
    
    /**
     * 
     * Singleton constructor
     * @return self
     */
    public static function getInstance(){
        if(!self::$INSTANCE){
            self::$INSTANCE = new self;
        }
        return self::$INSTANCE;
    }
    
    /**
     * 
     * @param int $severity
     */
    public function getSeverityName($severity){
        if(!isset($this->serverityNames[$severity])) $severity = 1;      
        return $this->serverityNames[$severity];
    }
    
    /**
     * 
     * @param CallbackInterface $Callback
     * @return boolean - can regester callback
     */
    public function regesterCallback(CallbackInterface $Callback){
        
    }
    
    /**
     * Is the call back regestered
     * 
     * @param int $id
     * @return bool
     */
    public function hasCallback($id){
        return isset($this->callbacks[$id]);
    }
    
    /**
     * un-regester a callback by it's id
     * 
     * @param string $id
     * @return bool - false if the callback was not exists
     */
    public function unRegesterCallback($id){
        if(!$this->hasCallback[$id]) return false;
        
        unset($this->callbacks[$id]);
        return true;
    }
    
    /**
     * get a callback
     * 
     * if $id is null, get all callbacks.
     * 
     * @param mixed $id
     */
    public function getCallback($id=null){
        if(!$id) return $this->callbacks;
        
        
    }
    
    /*
     *
     * Regester a callback funtion to exectue when an error is not caught
     * -note- because these callbacks are used to create errors, its a bad idea to throw errors here.
     * we could set these up as events in our commandor but again for sake of robustness we'll handle it independantly
     *
     * @param callable $callback - function to run, when condtions are met
     * @param int $severity - severity condition
     * @param string $priority - priority the callback runs at
     * @param array $args - additional arguments for the callback
     *
    public static function registerCallback($callback, $id, $type = self::TYPE_ALL, $severity = -1, $priority = self::DEFAULT_PRIORITY, array $args = array())
    {
        if (isset(self::$_CALLBACKS[ $id ])) {
            die("could not register ErrorHandler Callback $id duplicate id, unregesterCallback first");
        } //developer error should never happen
        
        $regester = false;
        if (is_callable($callback)) {
            $regester = true;
        } elseif (is_array($callback) && count($callback) == 2 && method_exists($callback[0], $callback[1])) {
            $regester = true;
        } elseif (is_string($callback) && function_exists($callback)) {
            $regester = true;
        }
        
        if (!$regester) {
            die("could not register ErrorHandler Callback $id is not callable");
        } //developer error should never happen
        
        self::$_CALLBACKS[ $id ] = array(
            'callable'    => $callback,
            'type'        => $type,
            'severity'    => (int)$severity,
            'priority'    => (int)$priority,
            'args'        => $args
        );
        
        //		evo_dump( self::$_CALLBACKS );
        
        uasort(self::$_CALLBACKS, function ($a, $b) {
            return ($a['priority'] > $b['priority']) ? true : false;
        });
    }*/
    
   /**
    * Main exception handling function
    * 
    * All errors wind up here
    * @todo Throwable typehint avalible PHP 7+, \Error avalible PHP7+
    * 
    * @param \Throwable $e 
    */
    public function handleException($e)
    { 
        if(!is_a($e, \Exception::class) && !is_a($e, '\\Error', false)){
            //php 5.6 fallback.
            throw new E\RuntimeError('Argument 1 passed to '.__METHOD__.' must be an instance of \Throwable');
            return false;
        }
 
        $severity = E_ERROR; 
        if(is_a($e, \ErrorException::class)){
            $severity = $e->getSeverity();
        }
        $severityName = $this->getSeverityName($severity);
 
        foreach ($this->callbacks as $callback){
            $args = array_merge([$e], $callback['args']);
           
            if(call_user_func_array($callback['callback'], $args)){
                return true;
            } 
        }

        echo $severityName . "\n";
    }

    /**
     * handle all error & throw exceptions for them
     * 
     * @param int $severity
     * @param string $message
     * @param string $file
     * @param string $line
     */
    public function handleError($severity, $message, $file = 'unknown', $line = 'unknown')
    {
        //throw all uncought errors as a RuntimeError > child of ErrorException
        throw new E\RuntimeError(
            $message,
            E\RuntimeError::ERROR_CODE,
            $severity,
            $file,
            $line
        );
    }

    /**
     * handle the shutdown
     */
    public function handleShutdown()
    {
        $lasterror = error_get_last();

        if (is_null($lasterror) || empty($lasterror['type'])) {
            // This is not an error, but a normal shutdown
            return;
        }
        
        //convert to exception
        try{
            throw new E\ShutdownError(
                $lasterror['message'],
                E\ShutdownError::ERROR_CODE,
                $lasterror['type'],
                $lasterror['file'],
                $lasterror['line']
            );
        }catch(E\ShutdownError $e){
            //we have to catch it to put it in handle as this is 
            //the shutdown.  But this normalizes the errors.
            $this->handleException($e);
        }
    }
    
    /**
     * 
     * @param int $severity
     * @return boolean
     *
    public function canHandle($severity)
    {
        $fatal = E_ERROR | E_PARSE | E_COMPILE_ERROR | E_USER_ERROR;
        
        if($fatal & $severity || error_reporting() & $severity) return true;
        
        return false;
    }
    

   /**
     * helper for exception handling - normalze errors
     * @param Throwable  $e
     * @param string $reportingLevel
     * @return array
     *
    public static function getSecuredException(\Exception  $e, $reportingLevel = EVO_ENV_PRODUCTION)
    {
        
        //$reportingLevel = EVO_ENV_TESTING;
        
        switch ($reportingLevel) {
            case EVO_ENV_DEBUG:
                $message = $e->getMessage();
                $line = $e->getLine();
                $file = $e->getFile();
                $stackTrace = $e->getTraceAsString();
            break;
            case EVO_ENV_TESTING:
                $message = self::getSecureMessage($e->getMessage());
                $line = $e->getLine();
                $file = self::getSecureFile($e->getFile());
                $stackTrace = self::getSecureStrTrace($e->getTrace());
            break;
            case EVO_ENV_PRODUCTION:
            default:
                $message = self::getSecureMessage($e->getMessage());
                $line = '';
                $file = '';
                $stackTrace = '';
            break;
        }
        
        $severity = E_USER_ERROR;
        if (method_exists($e, 'getSeverity')) {
            $severity = $e->getSeverity();
        }
        
        $severityName = self::getSeverityName($severity);
        
        $errorType = self::getErrorTypeName($e);
        
        return array(
                'submitted'        => new \DateTime(),
                'severity'            => $severity,
                'errorType'            => $errorType,
                'severityName'        => $severityName,
                'errorClass'        => '\\'.get_class($e),
                'message'            => $message,
                'errorCode'            => $e->getCode(),
                'file'                => $file,
                'line'                => $line,
                'stackTrace'        => $stackTrace
        );
    }
    
    /**
     *
     * @param mixed $var
     * @return string
     *
    private static function _parseArg($var)
    {
        switch (gettype($arg)) {
            case 'boolean':
                return $bool = ($arg ? 'true' : 'false');
            case 'integer':
                return intval($arg);
            case 'double':
                return floatval($arg);
            case 'string':
                return "'".$arg."'";
            case 'resource':
                return 'Resource id #'.(int)$arg;
            case 'NULL':
                return 'NULL';
            case 'unknown type':
                return 'UNKNOWN TYPE';;
            case 'array':
                return "Array";
            case 'object':
                return  'Object('.get_class($arg).')';
        }
    }
/*
    PHP7 exception tree
Throwable
    Error
        ArithmeticError
            DivisionByZeroError
        AssertionError
        ParseError
        TypeError
            ArgumentCountError
    Exception
        ClosedGeneratorException
        DOMException
        ErrorException
        IntlException
        LogicException
            BadFunctionCallException
                BadMethodCallException
            DomainException
            InvalidArgumentException
            LengthException
            OutOfRangeException
        PharException
        ReflectionException
        RuntimeException
            OutOfBoundsException
            OverflowException
            PDOException
            RangeException
            UnderflowException
            UnexpectedValueException
        SodiumException
     */
    
}