<?php
namespace evo\shutdown\callback;

use evo\shutdown\traits\ExceptionModTrait;

/**
 * Abstract Base class for shutdown callbacks
 *
 * <i>(c) 2016 Hugh Durham III</i>
 *
 * For license information please view the <b>LICENSE</b> file included with this source code.
 *
 * @author Hugh E. Durham III {ArtisticPhoenix}
 * @package Evo
 * @subpackage Shutdown
 *
 */
abstract class AbstractShutdownCallback implements ShutdownCallbackInterface
{
    use ExceptionModTrait;
    
    /**
     * Development mode
     *
     * This is the least secure mode, but the one that
     * diplays the most information.
     *
     * @var string
     */
    const ENV_DEVELOPMENT = 'development';
    
    /**
     *
     * @var string
     */
    const ENV_TESTING = 'testing';
    
    /**
     *
     * @var string
     */
    const ENV_PRODUCTION = 'production';
  
    /**
     * Unique identigier  for each callback instance
     *
     * @var string
     */
    protected $id;
    
    /**
     * It is up to each Child Object to impliment how it handles each Environment.
     * 
     * Generally these rules should be followed
     * <ul>
     *  <li><b>self::ENV_DEVELOPMENT</b> (development) - least secure, show full stacktraces, full error messages</li>
     *  <li><b>self::ENV_TESTING</b>     (testing)     - moderately secure, show stacktraces without arguments, partial error messages</li>
     *  <li><b>self::ENV_PRODUCTION</b>  (production)  - fully secure, no stacktraces, show minimal error messages</li>
     * </ul> 
     * 
     * @var string
     */
    protected $environment;
    
    /**
     * Callbacks are sorted by their priority and called in sequence lowest to highest
     *
     * @var int
     */
    protected $priority;
    
    /**
     * Additional arguments that can be stored in a Sub Class
     * 
     * All callbacks should happen within the scope of the Callback class
     * Because of this these should always be accessable by using $this->args
     * 
     * @var array
     */
    protected $args;

    /**
     * 
     * @param string $id
     * @param number $priority
     */
    public function __construct($id, $environment = self::ENV_PRODUCTION, $priority=null, array $args = []){
        $this->id = $id;
        
        if(!$priority) $priority = 100;
        
        $this->setEnvironment($environment);
        $this->priority = $priority;
        $this->args = $args;
    }
 
    /**
     * 
     * {@inheritDoc}
     * @see ShutdownCallbackInterface::getId()
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * return the enviroment setting
     *
     * self::ENV_* constants
     *
     * @return string
     */
    public function getEnvironment(){
        return $this->environment;
    }
    
    /**
     * It is up to each Child Object to impliment how it handles each Environment.
     *
     * Generally these rules should be followed
     * <ul>
     *  <li><b>self::ENV_DEVELOPMENT</b> (development) - least secure, show full stacktraces, full error messages</li>
     *  <li><b>self::ENV_TESTING</b>     (testing)     - moderately secure, show stacktraces without arguments, partial error messages</li>
     *  <li><b>self::ENV_PRODUCTION</b>  (production)  - fully secure, no stacktraces, show minimal error messages</li>
     * </ul>
     *
     * @return string
     */
    public function setEnvironment( $environment ){
        $this->environment = $environment;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see ShutdownCallbackInterface::getPriority()
     */
    public function getPriority()
    {
        return $this->priority;
    }
        
   /**
    * 
    * {@inheritDoc}
    * @see ShutdownCallbackInterface::setArgs()
    */
    public function setArgs(array $args = [])
    {
        $this->args = $args;
    }
    
    /**
     * Return the freeform arguments
     *
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Set freeform arguments
     *
     * Freeform array of additional data that a callback can use
     * It is up to each Child Object to implemnt thes
     *
     * @param array $args
     */
    public function setArg($key, $value)
    {
        $this->args[$key] = $value;
    }
    
    /**
     * Return a single argument by key or all arguments when null
     *
     *
     * @param string $key
     * @param mixed $default when element is not set return this instead
     * @return array|false
     */
    public function getArg($key, $default=null)
    {
        if(!isset($this->args[$key])) return $default;
        
        return $this->args[$key];
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see ShutdownCallbackInterface::execute()
     */
    abstract public function execute($e);
    
    
    //==========================================
    // avalible only in decendants of this class   
    //==========================================
 
}
