<?php
namespace evo\shutdown;

use evo\shutdown\Exception as E;
use evo\shutdown\callback\CallbackInterface;
use evo\shutdown\exception\InvalidCallback;
use evo\pattern\singleton\SingletonInterface;
use evo\pattern\singleton\SingletonTrait;

/**
 *
 * (c) 2016 Hugh Durham III
 *
 * For license information please view the LICENSE file included with this source code.
 *
 * [Singleton]Throwing errors from within the error handler is generally a bad idea.
 *
 * @author HughDurham {ArtisticPhoenix}
 * @package Evo
 * @subpackage Shutdown
 *
 */
final class ErrorHandler implements SingletonInterface
{
    use SingletonTrait;
    /**
     *
     * @var array
     */
    protected $callbacks = [];
    
    protected function init()
    {
        //regester out custom handlers.
        register_shutdown_function([$this,"handleShutdown"]);
        set_error_handler([$this,"handleError"]);
        set_exception_handler([$this, "handleException"]);
    }
    
    /**
     *
     * @param CallbackInterface $Callback
     * @return boolean - can regester callback
     */
    public function regesterCallback(CallbackInterface $Callback)
    {
        $id = $Callback->getId();
        
        if (isset($this->callbacks[$id])) {
            throw new InvalidCallback("Callback ID#{$id}# already regestered");
        }
        
        $priority = $Callback->getPriority();
        
        $this->callbacks[$id] = $Callback;
        
        uasort($this->callbacks, function ($a, $b) {
            if ($a->getPriority() == $b->getPriority()) {
                return 0;
            }
            
            return $a->getPriority() > $b->getPriority() ? 1 : -1;
        });
    }
    
    /**
     * Is the call back regestered
     *
     * @param int $id
     * @return bool
     */
    public function hasCallback($id)
    {
        return isset($this->callbacks[$id]);
    }
    
    /**
     * un-regester a callback by it's id
     *
     * @param string $id
     * @return bool - false if the callback was not exists
     */
    public function unRegesterCallback($id)
    {
        if (!$this->hasCallback[$id]) {
            return false;
        }
        
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
    public function getCallback($id=null)
    {
        if (!$id) {
            return $this->callbacks;
        }
    }
    
    /**
     * Main exception handling function
     *
     * All errors wind up here
     *
     * @param \Exception $e - @todo \Throwable after PHP7
     */
    public function handleException($e)
    {
        if (!is_a($e, \Exception::class) && !is_a($e, '\\Error', false)) {
            //php 5.6 fallback.
            throw new E\RuntimeError('Argument 1 passed to '.__METHOD__.' must be an instance of \Throwable');
            return false;
        }

 
        foreach ($this->callbacks as $callback) {
            $args = [$e, $callback->getArgs()];
            
            if (call_user_func_array([$callback, 'run'], $args)) {
                return;
            }
        }
        
        echo "cought";
        
        return false;
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
        try {
            throw new E\ShutdownError(
                $lasterror['message'],
                E\ShutdownError::ERROR_CODE,
                $lasterror['type'],
                $lasterror['file'],
                $lasterror['line']
            );
        } catch (E\ShutdownError $e) {
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
 *
 *


1       E_ERROR (integer)               Fatal run-time errors. These indicate errors that can not be recovered from, such as a memory allocation problem. Execution of the script is halted.
2       E_WARNING (integer)             Run-time warnings (non-fatal errors). Execution of the script is not halted.
4       E_PARSE (integer)               Compile-time parse errors. Parse errors should only be generated by the parser.
8       E_NOTICE (integer)              Run-time notices. Indicate that the script encountered something that could indicate an error, but could also happen in the normal course of running a script.
16      E_CORE_ERROR (integer)          Fatal errors that occur during PHP's initial startup. This is like an E_ERROR, except it is generated by the core of PHP.
32      E_CORE_WARNING (integer)     	Warnings (non-fatal errors) that occur during PHP's initial startup. This is like an E_WARNING, except it is generated by the core of PHP.
64      E_COMPILE_ERROR (integer)    	Fatal compile-time errors. This is like an E_ERROR, except it is generated by the Zend Scripting Engine.
128 	E_COMPILE_WARNING (integer)     Compile-time warnings (non-fatal errors). This is like an E_WARNING, except it is generated by the Zend Scripting Engine.
256 	E_USER_ERROR (integer)          User-generated error message. This is like an E_ERROR, except it is generated in PHP code by using the PHP function trigger_error().
512 	E_USER_WARNING (integer)    	User-generated warning message. This is like an E_WARNING, except it is generated in PHP code by using the PHP function trigger_error().
1024 	E_USER_NOTICE (integer)    	    User-generated notice message. This is like an E_NOTICE, except it is generated in PHP code by using the PHP function trigger_error().
2048 	E_STRICT (integer)              Enable to have PHP suggest changes to your code which will ensure the best interoperability and forward compatibility of your code. 	Since PHP 5 but not included in E_ALL until PHP 5.4.0
4096 	E_RECOVERABLE_ERROR (integer) 	Catchable fatal error. It indicates that a probably dangerous error occurred, but did not leave the Engine in an unstable state. If the error is not caught by a user defined handle (see also set_error_handler()), the application aborts as it was an E_ERROR. 	Since PHP 5.2.0
8192 	E_DEPRECATED (integer)          Run-time notices. Enable this to receive warnings about code that will not work in future versions. 	Since PHP 5.3.0
16384 	E_USER_DEPRECATED (integer) 	User-generated warning message. This is like an E_DEPRECATED, except it is generated in PHP code by using the PHP function trigger_error(). 	Since PHP 5.3.0
32767 	E_ALL (integer) 	            All errors and warnings, as supported, except of level E_STRICT prior to PHP 5.4.0. 	32767 in PHP 5.4.x, 30719 in PHP 5.3.x, 6143 in PHP 5.2.x, 2047 previously

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

     switch ($errno) {
        case E_NOTICE:
        case E_USER_NOTICE:
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
        case E_STRICT:
            echo("STRICT error $errstr at $errfile:$errline n");
            break;

        case E_WARNING:
        case E_USER_WARNING:
            echo("WARNING error $errstr at $errfile:$errline n");
            break;

        case E_ERROR:
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
            exit("FATAL error $errstr at $errfile:$errline n");

        default:
            exit("Unknown error at $errfile:$errline n");
    }


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
