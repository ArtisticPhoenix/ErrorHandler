<?php
namespace evo\shutdown\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Shutdow/Error/Exception handler
 *
 * @author ArtisticPhoenix
 * @package Evo
 * @subpackage shutdown
 * @link https://github.com/ArtisticPhoenix/Shutdown/issues
 * @version 1.0.0
 * @eJinn:buildVersion 1.0.0
 * @eJinn:buildTime 1522903380.5456
 */
class EvoShutdownInvalidCallback extends \ErrorException implements \evo\shutdown\exception\EvoShutdownExceptionInterface, \evo\exception\EvoExceptionInterface
{

    /**
     * @var int
     */
    const ERROR_CODE = 999;

    /**
     *
     * {@inheritDoc}
     * @see \ErrorException::__construct()
     */
    public function __construct($message = "", $code = 999, $severity = 1, $filename = null, $lineno = null, \Exception $previous = null)
    {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
    }
}
