<?php
namespace evo\shutdown;

use evo\pattern\singleton\SingletonInterface;
use evo\pattern\singleton\SingletonTrait;
use evo\exception as E;
use evo\shutdown\callback\ShutdownCallbackInterface;
use evo\shutdown\traits\ExceptionModTrait;

/*
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
 *      with the current <b>error_reporting</b> level when shutdown occurs.  Thrown <em>ShutdownErrors</em> always respect
 *      the settings of the shutdown handler, regarless if they were Execptions, Error (PHP7 throwable), or errors (PHP errors)
 *  </li>
 *  <li>
 *      <b>Exceptions/Errors(Throwable)</b>
 *      Exceptions and Errors are passed to the callback handler, except in the event of shutdown.
 *      The execption handler will respect the settings for the error handler, regardless of its settings
 *      Anything making it to shutdown, will respect the settings of the shutdown handler.     
 *      Exceptions are distributed to the callbacks based on their position in the priority list. It is the individual
 *      callback's resposibillity to impliment its on error handling and to respect the <b>display_errors</b> setting.
 *      A good example of why this is the case is the <em>ShutdownErrorLogCallback</em>. It's pefectly acceptable
 *      for this callback to log errors to a file even when <em>display_errors = 0</em>
 *  </li>
 *  <li>
 *      <b>errors</b>
 *      error are converted to Exceptions<em>(ShutdownRuntimeError)</em> only if the errors <em>severity</em> is compatable
 *      with the current <b>error_reporting</b> level when the error is triggered. Or if alwaysConvertErrors is true.
 *      Thrown <em>ShutdownRuntimeError</em> are passed to the main Exception handler and handled by a try catch block in
 *      application code or caught by the exception handler(see above).  The exception handler will respect the settings
 *      for the error handler beign registered when dealing with errors.
 *  </li>
 * </ul>
 */
 
/**
 * (c) 2018 Hugh Durham III
 *
 * For license information please view the LICENSE file included with this source code.
 *
 * [Singleton]
 *
 * Error / Exception / Shudown handler
 * @author HughDurham {ArtisticPhoenix}
 * @package Evo
 * @subpackage Shutdown
 *
 */
final class Shutdown implements SingletonInterface
{
    use SingletonTrait;
    use ExceptionModTrait;
  
    /**
     * Name of the shutdown handler
     *
     * @var string
     */
    const HANDLE_SHUTDOWN = 'SHUTDOWN';
    
    /**
     * Name of the error handler
     * 
     * @var string
     */
    const HANDLE_ERROR = 'ERROR';
       
    /**
     * Name of the exception handler
     *
     * @var string
     */
    const HANDLE_EXCEPTION = 'EXCEPTION';
    
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
     * 
     * @var string
     */
    protected $alwaysConvertErrors=true;
    
    /**
     * triggered on construct
     */
    protected function init()
    {
        register_shutdown_function([$this, 'handleShutdown']);
        $this->registerHandler(self::HANDLE_SHUTDOWN);
        
        set_error_handler([$this, 'handleError']);
        $this->registerHandler(self::HANDLE_ERROR);
        
        set_exception_handler([$this, 'handleException']);
        $this->registerHandler(self::HANDLE_EXCEPTION);
    }
    
    /*
     * handlers are acutally registered on init()
     * but php has no way to deregister a specific
     * handler, so we can simply track a bool value
     * to do this
    */
    
    /**
     * Register a handler
     *
     * @param string $which
     */
    public function registerHandler($which){
        $this->handlers[$which] = true;
    }
    
    /**
     * UN-Register a handler
     *
     * handlers are acutally registered on init()
     * but php has no way to deregister a specific
     * handler, so we can simply track a bool value
     * to do this
     *
     * @param string $which
     */
    public function unRegisterHandler($which){
        $this->handlers[$which] = false;
    }
    
    /*
     * 
     * if handling is on, and this is on (both are on)
     *  - all errors will be converted and are handled (regardless of the exception handler being off or on)
     *
     * if handling is on, and this is off
     *  - errors will be converted only when their severity is covered by the <b>error repoting</b> setting.
     * 
     * if handling is off, and this is on
     *  - errors will be thown, but not handled by the exception handler even if exception hadling is on
     *  - this will happen regardless of the <b>error reporting</b> settings
     *  
     * if handling is off, and this is off (both are off)
     *  - errors will never be converted. Fatal errors that trigger a shutdown are still handled by the
     *  - shutdown handler regardless of the error hanlder setting.
     */
     
     /**
     * always conver errors to ShutdownRuntimeExceptions
     * 
     * @param bool $bool
     */
    public function alwaysConvertErrors($bool){
        $this->alwaysConvertErrors = (bool)$bool;
    }
    
    /**
     * anything that makes it this far will be handled if the error is a level that is
     * compatable with the current <b>error_reporint</b> setting
     * 
     * handle the shutdown
     */
    public function handleShutdown()
    {
        if(!$this->handlers[self::HANDLE_SHUTDOWN]) return false;      
        
        $lasterror = error_get_last();
        
        if ( is_null( $lasterror )){
            //normal shutdown / no error
            return false;
        }
        
        //test against error reporting
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
                //we have to manually catch it to put it in handleAny as this is the shutdown. 
                // This normalizes all errors for us..
                //additionally by bypassing the handleException method we can respect the 
                //register shutdown setting even when handle error and handle exeption is off
                $this->handleAny($e);
            }
            return true;
        }
        return false;
    }

    /**
     * Main exception handling function
     *
     * All errors wind up here, compatable with PHP7
     *
     * @param \Exception $e - @todo \Throwable after PHP7
     */
    public function handleException($e)
    {     
        if(is_a($e, E\ShutdownError::class)){
            //if this is a runtime error (from error handler)
            //check the hanle error setting instead
            if(!$this->handlers[self::HANDLE_ERROR]){
                return false;
            }  
        }else if(!$this->handlers[self::HANDLE_EXCEPTION]){
            //otherwise check the exception hanlder
            return false;
        }  
        
        
        return $this->handleAny($e);
    }
    
    /**
     * 
     * @param unknown $which
     */
    protected function checkHandler($which){
        if(!defined(__CLASS__.'::HANDLE_'.$which)) throw new E\ShutdownHandlerName();
    }
    
    /**
     * throw exceptions for error regestered in error_reporting
     *
     * catching these errors depend on error_reporting settings, and rather or not
     * the error handler is on
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
        //allow though if error handler is on
        if(!$this->handlers[self::HANDLE_ERROR]) return false;
       
        if ($this->alwaysConvertErrors || error_reporting() == -1 || (error_reporting() & $severity)) {
           
            //allow normal errors to bubble up through exception handling
            //these will be caught by the exception callbak if not in a application try/catch block
            //if error handler is off, the exception handler will respct this and no handle the error
            //if the error makes it to the shutdown hanler it will respect the settings of the shutdown hanlder
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
     * @param \Exception $e - @todo \Throwable after PHP7
     * @throws E\ShutdownRuntimeError
     * @return void|boolean
     */
    private function handleAny($e){
       if(!$this->isTrowable($e)) return false;
        
        /*@var $callback ShutdownCallbackInterface */
        foreach ($this->callbacks as $callback) {
            if ($callback->execute($e)) {
                return false;
            }
        }
        
        return false;
    }
    
    
    //--------------------------------------  
    //            HELPERS
    //--------------------------------------

    /**
     * Register a callback to handle exceptions  
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
     * get a callback or all callbacks
     *
     * @param string|null $id get a single callback by id or get all callbacks
     */
    public function getCallback($id=null)
    {    
        return !$id ? $this->callbacks : isset($this->callbacks[$id]) ? $this->callbacks[$id] : false;
    }
    
}
