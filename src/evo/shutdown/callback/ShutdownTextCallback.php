<?php
namespace evo\shutdown\callback;

/**
 * Basic text error screen printer, callback
 *
 * <i>(c) 2016 Hugh Durham III</i>
 *
 * For license information please view the <b>LICENSE</b> file included with this source code.
 *
 * @author Hugh E. Durham III {ArtisticPhoenix}
 * @package Evo
 * @subpackage Shutdown        
 */
class ShutdownTextCallback extends AbstractShutdownCallback
{

    /**
     * 
     * (non-PHPdoc)
     * @see AbstractShutdownCallback::execute()
     */
    public function execute($e)
    {
        
        print_r($this->args);
       /*
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
        */ 
        
    }
}

