<?php
namespace evo\shutdown\callback;

/**
 * Interface for shutdown/error handlers.
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
interface ShutdownCallbackInterface
{
    /**
     * Get the unique Identifier for this callback
     * 
     * The unique Id allows unregestiring a callback.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the priority for this callback
     * 
     * Callbacks are sorted by their priority and called in sequence lowest to highest
     *
     * @return int
     */
    public function getPriority();      

    /**
     * This is the functional part of the callback
     * 
     * It's the Child Object's resposibillity on rather or not it should handle a given error.
     * return true from execute will halt execution of following callbacks (based on priority)
     *
     * @param \Exception
     * @return mixed
     */
    public function execute($e);
}
