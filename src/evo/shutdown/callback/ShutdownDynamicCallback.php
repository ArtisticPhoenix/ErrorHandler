<?php
namespace evo\shutdown\callback;

use evo\shutdown\Shutdown;

/**
 * Shutdown Callback wrappper for runtime closures
 * 
 * Dynamic Callbacks must be a closure, this closer is copied by BindTo
 * To resolve the scope of this class, this allows easy access to 
 * all the propeties of this class.
 * 
 * Responsibillity for handliing any errors/exceptions is passed of to the closure
 * return true from the closure will halt execution of following callbacks(based on priority)
 *
 * <i>(c) 2016 Hugh Durham III</i>
 *
 * For license information please view the <b>LICENSE</b> file included with this source code.
 *
 * @author Hugh E. Durham III {ArtisticPhoenix}
 * @package Evo
 * @subpackage Shutdown
 */
class ShutdownDynamicCallback extends AbstractShutdownCallback
{
    
    /**
     * 
     * @var callable
     */
    protected $dynamicCallback;  
    
    /**
     * 
     * @param string $id
     * @param \Closure $dynamicCallback
     * @param string $environment
     * @param number $priority
     * @param array $args
     */
    public function __construct($id, \Closure $dynamicCallback, $environment=self::ENV_PRODUCTION, $priority=100, array $args=[]){
        //resolve the scope to this class, so $args are accessable
        $this->dynamicCallback = $dynamicCallback->bindTo($this, __CLASS__);
        
        parent::__construct($id, $environment, $priority, $args);
    }

    /**
     * (non-PHPdoc)
     *
     * @see AbstractShutdownCallback::execute()
     *
     */
    public function execute($e)
    {
        return $this->dynamicCallback->__invoke($e);
    }
}

