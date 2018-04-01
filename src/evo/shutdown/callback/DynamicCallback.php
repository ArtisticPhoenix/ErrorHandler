<?php
namespace evo\shutdown\callback;

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
class DynamicCallback extends AbstractCallback
{
    /**
     *
     * @var callable
     */
    protected $callback;
    
    /**
     *
     * @param callable $callback
     * @param unknown $id
     */
    public function __construct(callable $callback, $id = null)
    {
        $this->callback = $callback;
        $this->id = is_null($id) ? $this->generateID() : $id;
    }

    /**
     *
     * @return string
     */
    protected function generateID()
    {
        return uniqid('', true);
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \evo\shutdown\callback\AbstractCallback::run()
     */
    public function run($e, $arg1 = null)
    {
        return call_user_func_array($this->callback, func_get_args());
    }
}
