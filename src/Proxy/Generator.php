<?php

namespace Kamilov\PDB\Proxy;

/**
 * Class Generator
 * @package Kamilov\PDB\Proxy
 */
class Generator
{
    /** @var object */
    private $object;
    /** @var string */
    private $method;

    /**
     * Generator constructor.
     * @param object $object
     * @param string $method
     */
    public function __construct(object $object, string $method)
    {
        $this->object = $object;
        $this->method = $method;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return call_user_func([$this->object, $this->method], $name);
    }
}
