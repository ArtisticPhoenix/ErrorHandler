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

        /*@var $callback CallbackInterface */
        foreach ($this->callbacks as $callback) {
            $args = [$e, $callback->getArgs()];
            
            if ($callback->run(...$args)) {
                return;
            }
        }
        
        return false;
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
        if(error_reporting() == -1 || error_reporting() & $severity){
            throw new E\RuntimeError(
                $message,
                E\RuntimeError::ERROR_CODE,
                $severity,
                $file,
                $line
            ); 
        }
        return false;
    }

    /**
     * handle the shutdown
     */
    public function handleShutdown()
    {
        $lasterror = error_get_last();
        
        if(error_reporting() == -1 || (isset($lasterror['type']) && error_reporting() & $lasterror['type'])){
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
            return true;
        }
        return false;
    }
    

}
