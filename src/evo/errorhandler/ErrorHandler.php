<?php
namespace evo\errorhandler;


use evo\errorhandler\Exception\RuntimeError;
use evo\errorhandler\Exception\ShutdownError;

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
final class ErrorHandler{
    
    /**
     * 
     * @var string
     */
    const ENV_PRODUCTION = 'production';
    
    /**
     *
     * @var string
     */
    const ENV_TESTING = 'testing';
    
    /**
     *
     * @var string
     */
    const ENV_DEVELOPMENT = 'development';
    
    /**
     * @var self
     */
    protected static $INSTANCE;
    
    protected $serverityNames = array(
        -1                      => 'FATAL ERROR',
        E_ERROR                 => 'FATAL ERROR',
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
        E_ALL                   => 'FATAL ERROR'
    );
    
    /**
     *
     * @var string
     */
    protected $enviroment;
    
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
        $this->setEnvironment();
        
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
    * @param string $environment - one of the ENV_* constants
    */
    public function setEnvironment($environment = self::ENV_PRODUCTION){
        switch ($environment){
            case self::ENV_DEVELOPMENT:
            case self::ENV_TESTING:
                $this->enviroment = $environment;
            break;
            default:
                $this->enviroment = self::ENV_PRODUCTION;
            break;     
        }    
    }
    
    /**
     * 
     * @return string
     */
    public function getEnvironment(){
        return $this->enviroment;
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
     * regester a callback that fires on handling an error
     * 
     * Callbacks should at least one argument which impliments the throwable interface
     * function(\Throwable $e){ }
     * Returning True from the callback indicates that the Exception was handled and 
     * skips executing any other callbacks in the stack.
     * 
     * @param callable $callback - any callable (callable typehint avalible PHP 5.4+)
     * @param string $id - a unique identifier to insure only one instnace is regestered
     * @param int $severity - level to handle (simular to error_reporting())
     * @param int $priority - sort order ASC, lower numbers execute first
     * @param array $args - additional arguments to pass to the error handler
     * 
     * @return bool - regestered or not.
     */
    public function regesterCallback(callable $callback, $id = null, $severity = -1, $priority = 50, array $args = array()){
        if(!$id){
            $id = uniqid(null, true);
        }
        
        if(isset($this->callbacks[$id])) return false;
        
        $this->callbacks[$id] = [
            'callback'  => $callback,
            'severity'  => $severity,
            'priority'  => $priority,
            'args'      => $args
        ];
        
        usort($this->callbacks, function($a, $b){
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });
        
        return true;
    }
    
    /**
     * un-regester a callback by it's id
     * 
     * @param string $id
     */
    public function unRegesterCallback($id){
        
    }
    
    /**
     * get a callback
     * 
     * if $id is null, get all callbacks.
     * 
     * @param mixed $id
     */
    public function getCallback($id=null){
        
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
            throw new RuntimeError('Argument 1 passed to '.__METHOD__.' must be an instance of \Throwable');
            return false;
        }
 
        $severity = E_ERROR; 
        if(is_a($e, \ErrorException::class)){
            $severity = $e->getSeverity();
        }
        $severityName = $this->getSeverityName($severity);
        
        
        foreach ($this->callbacks as $callback){
            
            
          //  if(call_user_func_array())
            
            
        }

        echo $severityName . "\n";
    }

    /**
     * 
     * @param int $severity
     * @param string $message
     * @param string $file
     * @param string $line
     */
    public function handleError($severity, $message, $file = 'unknown', $line = 'unknown')
    {
        //print_r(__METHOD__."\n");
        if(!$this->canHandle($severity)){
            return;
        }
        
        //throw 
        throw new RuntimeError(
            $message,
            RuntimeError::ERROR_CODE,
            $severity,
            $file,
            $line
        );
    }

    /**
     * 
     */
    public function handleShutdown()
    {
        $lasterror = error_get_last();

        if (is_null($lasterror) || empty($lasterror['type']) || !$this->canHandle($lasterror['type'])) {
            // This error code is not included in error_reporting
            return;
        }
        
        //convert to exception
        try{
            throw new ShutdownError(
                $lasterror['message'],
                ShutdownError::ERROR_CODE,
                $lasterror['type'],
                $lasterror['file'],
                $lasterror['line']
            );
        }catch(ShutdownError $e){
            $this->handleException($e);
        }
    }
    
    /**
     * 
     * @param int $severity
     * @return boolean
     */
    public function canHandle($severity)
    {
        $fatal = E_ERROR | E_PARSE | E_COMPILE_ERROR | E_USER_ERROR;
        
        if($fatal & $severity || error_reporting() & $severity) return true;
        
        return false;
    }
    
    /*
     * 

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
      SodiumExceptio
    
     */
    
}