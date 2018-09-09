<?php
namespace evo\shutdown\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Exception handler
 *
 * @author ArtisticPhoenix
 * @package Evo
 * @subpackage Shutdown
 * @link https://github.com/ArtisticPhoenix/Shutdown/issues
 * @version 1.0.0
 * @eJinn:buildVersion 1.0.0
 * @eJinn:buildTime 1536450082.3375
 */
class ShutdownHandlerName extends \ErrorException implements \evo\shutdown\exception\ShutdownExceptionInterface, \evo\exception\EvoExceptionInterface
{

    /**
     * @var int
     */
    const ERROR_CODE = 1100;

    /**
     *
     * {@inheritDoc}
     * @see \ErrorException::__construct()
     */
    public function __construct($message = "Please use on of the Shutdown::HANDLER_* constants", $code = 1100, $severity = 1, $filename = null, $lineno = null, \Exception $previous = null)
    {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
    }
}