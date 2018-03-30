<?php
namespace evo\shutdown\exception;

/**
 * (eJinn Generated File, do not edit directly)
 * Shutdow/Error/Exception handler
 *
 * @author ArtisticPhoenix
 * @package Evo
 * @subpackage exception
 * @link https://github.com/ArtisticPhoenix/Shutdown/issues
 * @varsion 1.0.0
 * @eJinn:buildVersion 1.0.0
 * @eJinn:buildTime 1522391233.2386
 */
class ShutdownError extends \ErrorException implements \evo\shutdown\exception\ShutdownExceptionInterface
{

    /**
     * @var int
     */
    const ERROR_CODE = 900;

    /**
     *
     * {@inheritDoc}
     * @see \ErrorException::__construct()
     */
    public function __construct($message = "", $code = 900, $severity = 1, $filename = null, $lineno = null, \Exception $previous = null)
    {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
    }
}
