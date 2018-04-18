<?php
namespace evo\shutdown\callback;

use evo\shutdown\ErrorHandler;

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
class TextShutdownCallback extends AbstractShutdownCallback
{
    
    /**
     *
     * @var integer
     */
    protected $messageWidth;
    
    /**
     *
     * @param string $id
     * @param string $environment    One of the ENV_* constants
     * @param int $priority   Run order ASE, lowest first
     * @param int $messageWidth   width of messages in char
     */
    public function __construct($id = null, $environment = null, $priority=100, $messageWidth = 120, array $args = [])
    {
        if (!$id) {
            //auto genterate an id if non is sent.
            $id = $this->generateId();
        }

        $this->setId($id);
        
        if (!$environment && defined('EVO_ENVIROMENT')) {
            //if enviroment is not in constructor but the constant is set then use that.
            $environment = EVO_ENVIROMENT;
        }

        $this->setEnvironment($environment);
  
        $this->setPriority($priority);

        $this->setMessageWidth($messageWidth);
    }
   
    /**
     * Return the max with for messages in chars
     * @return string
     */
    public function getMessageWidth()
    {
        return $this->messageWidth;
    }
    
    /**
     * only applies to the message wrapper
     *
     * @param int $width
     */
    public function setMessageWidth($width)
    {
        $this->messageWidth = $width;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\AbstractShutdownCallback::execute()
     */
    public function execute($e, $arg1 = null)
    {
        $output = "";
        $output .= str_pad("= Exception Handler({$this->getSeverityName($e)}) =", $this->messageWidth, "=", STR_PAD_BOTH)."\n";
        $output .= "[".date('M d Y H:i:s')."]{$this->getClass($e)}({$e->getCode()}) \"{$this->getMessage($e)}\"\n";
          
        if (ErrorHandler::ENV_PRODUCTION == $this->environment) {
            $output .= "Called in {$this->getFile($e)} on {$this->getLine($e)}\n";
            $output .= str_pad("", $this->messageWidth, "-", STR_PAD_BOTH)."\n";
            $output .= $this->getTraceAsString($e)."\n";
        }

        $output .= str_pad("", $this->messageWidth, "=", STR_PAD_BOTH)."\n\n";

        echo $output;
    }
}
