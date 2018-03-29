<?php
namespace evo\errorhandler;


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
        'EVERYTHING'            => -1,
        'ERROR'                 => E_ERROR,
        'RECOVERABLE_ERROR'     => E_RECOVERABLE_ERROR,
        'WARNING'               => E_WARNING,
        'PARSE'                 => E_PARSE,
        'NOTICE'                => E_NOTICE,
        'STRICT'                => E_STRICT,
        'DEPRECATED'            => E_DEPRECATED,
        'CORE_ERROR'            => E_CORE_ERROR,
        'CORE_WARNING'          => E_CORE_WARNING,
        'COMPILE_ERROR'         => E_COMPILE_ERROR,
        'COMPILE_WARNING'       => E_COMPILE_WARNING,
        'EVO_ERROR'             => E_USER_ERROR,
        'EVO_WARNING'           => E_USER_WARNING,
        'EVO_NOTICE'            => E_USER_NOTICE,
        'EVO_DEPRECATED'        => E_USER_DEPRECATED,
        'ALL'                   => E_ALL,
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
     * @param callable $callback
     */
    public function regesterCallback($callback){
        
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
    
    
    public function handle($message = "", $type="", $code = 2000, $severity = E_ERROR, $filename = 'unknown', $lineno = 'unknown'){
        
    }

    /**
     * we cant type hint $e because of the \Error|\Exception class
     *
     * @param \Exception $e
     */
    public function handleException($e)
    {
        $severity = E_ERROR;
        
        if(is_a($e, "\\ErrorException")){
            $e->getSeverity();
        }
        
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
        throw new \ErrorException(
            $message,
            1,
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
       // print_r(__METHOD__."\n");
        $lasterror = error_get_last();
        
        print_r($lasterror);

        if (is_null($lasterror) || empty($lasterror['type']) || !$this->canHandle($lasterror['type'])) {
            // This error code is not included in error_reporting
            return;
        }
        
        //convert to exception
        /*try{
            throw new ShutdownError(
                $lasterror['message'],
                ShutdownError::ERROR_CODE,
                $lasterror['type'],
                $lasterror['file'],
                $lasterror['line']
            );
        }catch(ShutdownError $e){
            $this->handleException($e);
        }*/
    }
    
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