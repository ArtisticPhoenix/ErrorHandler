<?php
namespace evo\shutdown;

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
     * @var self
     */
    protected static $INSTANCE;
    
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
        register_shutdown_function(array($this,"handleShutdown"));
        set_error_handler(array($this,"handleError"));
        set_exception_handler(array( $this, "handleException"));
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
     * we cant type hint $e because of the \Error|\Exception class
     *
     * @param \Exception $e
     */
    public function handleException($e)
    {
        
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
        
    }

    /**
     * 
     */
    public function handleShutdown()
    {
        $lasterror = error_get_last();
        if (is_null($lasterror) || empty($lasterror['type']) || !(error_reporting() & $lasterror['type'])) {
            // This error code is not included in error_reporting
            return;
        }
        
        /*try {
            throw new \Exception(
                $lasterror['message'],
                ErrorException::SHUTDOWN_ERROR,
                $lasterror['type'],
                $lasterror['file'],
                $lasterror['line']
           );
        } catch (\Evo\Core\Error\ErrorException $e) {
            self::_handle($e);
        }*/
    }
    
    public function canHandle($type)
    {
        
    }
    
}