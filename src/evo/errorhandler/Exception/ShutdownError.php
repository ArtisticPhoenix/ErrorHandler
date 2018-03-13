<?php
namespace evo\errorhandler\Exception;

/**
 * (eJinn Generated File, do not edit directly)
 * eJinn The Exception Genie
 *
 * @author ErrorHandler
 * @package Evo
 * @subpackage Exception
 * @link https://github.com/ArtisticPhoenix/ErrorHandler/issues
 * @varsion 1.0.0
 * @eJinn:buildVersion 1.0.0
 * @eJinn:buildTime 1520920953.7529
 */
class ShutdownError extends \Exception implements \evo\errorhandler\Exception\ErrorHandlerExceptionInterface
{

	/**
	 * @var int
	 */
	const ERROR_CODE = 2000;

    /**
     *
     * {@inheritDoc}
     * @see \Exception::__construct()
     */
    public function __construct($message = "", $code = 2000, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
