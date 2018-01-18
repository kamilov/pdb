<?php

namespace Kamilov\PDB\Proxy;

/**
 * Class Proxy
 * @package Kamilov\PDB\Proxy
 */
class Proxy
{
    /** @var callable */
    private $callback;

    /**
     * Proxy constructor.
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return call_user_func($this->callback, $method, $arguments);
    }
}
