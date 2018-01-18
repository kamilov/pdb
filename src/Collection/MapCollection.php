<?php

namespace Kamilov\PDB\Collection;

/**
 * Class MapCollection
 * @package Kamilov\PDB\Collection
 */
class MapCollection extends \ArrayIterator implements CollectionInterface
{
    use CollectionTrait;

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this[$key] = $value;
    }

    /**
     * @param mixed $key
     * @return mixed|null
     */
    public function get($key)
    {
        return isset($this[$key]) ? $this[$key] : null;
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this[$key]);
    }

    /**
     * @param mixed $key
     */
    public function remove($key)
    {
        unset($this[$key]);
    }
}
