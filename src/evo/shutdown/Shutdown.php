<?php
namespace evo\shutdown;

use evo\pattern\singleton\SingletonInterface;
use evo\pattern\singleton\SingletonTrait;
use evo\shutdown\exception as E;
use evo\shutdown\callback\ShutdownCallbackInterface;
use evo\shutdown\exception\ShutdownRuntimeError;

/**
 * 
 * (c) 2018 Hugh Durham III
 *
 * For license information please view the LICENSE file included with this source code.
 *
 * [Singleton]Throwing errors from within the error handler is generally a bad idea!
 *
 * Error / Exception / Shudown handler
 * 
 * <dl>
 *   <dt>For the puposes of this document, and because of the ambiguity of Error classes in PHP7</dt>
 *   <dd>Errors(uppercased) will refer to the PHP7 Error class.</dd>
 *   <dd>errors(lowercased) will refer to the older procedual errors.</dd>
 * </dl>
 * 
 * 
 * This class does nothing by itself, it relies on one or more callbacks.
 * All callbacks must impliment <em>eShutdownCallbackInterface</em> 
 * It is up to these child classes to handle the displaying of these messages.
 * 
 * However, this class generally respects the setting for <em>error_reporting</em>
 * <ul>
 *  <li>
 *      <b>Shutdown</b>
 *      Shutdown errors are converted to Exceptions<em>(ShutdownError)</em> only if the lasterror's <em>type</em> is compatable
 *      with the current<em>error_reporting</em> level when shutdown occurs.  Thrown <em>ShutdownErrors</em> are passed to the 
 *      main Exception handler and handled as if they were normal exceptions (see below).
 *  </li>
 *  <li>
 *      <b>Exceptions/Errors(Throwable)</b>
 *      Exceptions and Errors are always passed to the callback handler, exceptions are always fatal when not handled.
 *      They are distributed to the callbacks based on their position in the priority list. It is the individual
 *      callback's resposibillity to impliment its on error handling and to respect the <b>display_errors</b> setting.
 *      A good example of why this is the case is the <em>ShutdownErrorLogCallback</em>. It's pefectly acceptable
 *      for this callback to log errors to a file even when <em>display_errors = 0</em>
 *  </li>
 *  <li>
 *      <b>errors</b>
 *      error are converted to Exceptions<em>(ShutdownRuntimeError)</em> only if the errors <em>severity</em> is compatable
 *      with the current <em>error_reporting</em> level when the error is triggered. Thrown <em>ShutdownRuntimeError</em> are
 *      passed to the main Exception handler and handled as if they were normal exceptions (see above).
 *  </li>
 * </ul>
 *
 * @author HughDurham {ArtisticPhoenix}
 * @package Evo
 * @subpackage Shutdown
 *
 */
final class Shutdown implements SingletonInterface
{
    use SingletonTrait;
  
    /**
     * Name of the shutdown handler [Internal Use]
     *
     * @var string
     */
    const HANDLE_SHUTDOWN = 'handleShutdown';
    
    /**
     * Name of the exception handler [Internal Use]
     *
     * @var string
     */
    const HANDLE_EXCEPTION = 'handleException';
    
    /**
     * Name of the error handler [Internal Use]
     *
     * @var string
     */
    const HANDLE_ERROR = 'handleError';
    
    /**
     * Storage for callbacks
     *
     * @var array
     */
    protected $callbacks = [];
    
    /**
     * Storage for handlers stateso we can unregester them
     *
     * @var array
     */
    protected $handlers = [];
    
    /**
     * triggered on construct
     */
    protected function init()
    {
        register_shutdown_function([$this, self::HANDLE_SHUTDOWN]);
        $this->regesterShutdownHandler();
        
        set_exception_handler([$this, self::HANDLE_EXCEPTION]);
        $this->regesterExceptionHandler();
        
        set_error_handler([$this, self::HANDLE_ERROR]);
        $this->regesterErrorHandler();
    }
    
    public function regesterShutdownHandler()
    {      
       $this->handlers[self::HANDLE_SHUTDOWN] = true;
    }
    
    public function unRegesterShutdownHandler()
    {
        $this->handlers[self::HANDLE_SHUTDOWN] = false;
    }
    
    /**
     * handle the shutdown
     */
    public function handleShutdown()
    {
        
        if(!$this->handlers[self::HANDLE_SHUTDOWN]) return false;      
        
        $lasterror = error_get_last();
        
        print_r("hello world");
        
        if ( is_null( $lasterror )){
            //normal shutdown / no error
            return false;
        }
        
        if (error_reporting() == -1 || error_reporting() & $lasterror['type']) {
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
                $this->handleAny($e);
            }
            return true;
        }
        return false;
    }
    
    
    
    public function regesterExceptionHandler()
    {
        $this->handlers[self::HANDLE_EXCEPTION] = true;
    }
 
    public function unRegesterExceptionHandler()
    {
        $this->handlers[self::HANDLE_EXCEPTION] = false;
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
        if(is_a($e, ShutdownRuntimeError::class)){
            if($this->handlers[self::HANDLE_ERROR]){
                return false;
            }  
        }else if($this->handlers[self::HANDLE_EXCEPTION]){
            return false;
        }  
        return $this->handleAny($e);
    }
 
    
    public function regesterErrorHandler()
    {
        $this->handlers[self::HANDLE_ERROR] = true;
    }
    
    public function unRegesterErrorHandler()
    {
        $this->handlers[self::HANDLE_ERROR] = false;
    }
    
    /**
     * throw exceptions for error regestered in error_reporting
     *
     * catching these errors depend on error_reporting settings (use at your own risk)
     *
     * @param int $severity
     * @param string $message
     * @param string $file
     * @param string $line
     *
     * @see http://php.net/manual/en/function.set-error-handler.php
     */
    public function handleError($severity, $message, $file = 'unknown', $line = 'unknown')
    {
        
        if(!$this->handlers[self::HANDLE_ERROR]) return false;
        
        if (error_reporting() == -1 || error_reporting() & $severity) {
            //allow normal errors to bubble up through exception handling
            //these will be caught by the exception callback
            throw new E\ShutdownRuntimeError(
                $message,
                E\ShutdownRuntimeError::ERROR_CODE,
                $severity,
                $file,
                $line
            );
        }
        return false;
    }
    
    /**
     * 
     * @param unknown $e
     * @throws E\ShutdownRuntimeError
     * @return void|boolean
     */
    private function handleAny($e){
        if (!is_a($e, \Exception::class) && !is_a($e, '\\Error', false)) {
            $type = interface_exists('\\Throwable') ? '\\Throwable' : '\\Exception';
            //PHP7 compatibility
            throw new E\ShutdownRuntimeError('Argument 1 passed to '.__METHOD__.' must be an instance of '.$type);
            return false;
        }
        
        /*@var $callback ShutdownCallbackInterface */
        foreach ($this->callbacks as $callback) {
            if ($callback->execute($e)) {
                return;
            }
        }
        
        return false;
    }
    
    
    //--------------------------------------  
    //            HELPERS
    //--------------------------------------
    

    /**
     *
     * @param ShutdownCallbackInterface $Callback
     * @return self
     */
    public function regesterCallback(ShutdownCallbackInterface $Callback)
    {
        $id = $Callback->getId();
        
        $priority = $Callback->getPriority();
        
        $this->callbacks[$id] = $Callback;
        
        uasort($this->callbacks, function ($a, $b) {
            return $a->getPriority() - $b->getPriority();
        });
        return $this;
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
        if (!$this->hasCallback[$id]) return false;

        unset($this->callbacks[$id]);
        return true;
    }
    
    /**
     * remove all callbacks
     */
    public function clearCallbacks(){
        $this->callbacks = [];
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
        return !$id ? $this->callbacks : isset($this->callbacks[$id]) ? $this->callbacks[$id] : false;
    }

}
