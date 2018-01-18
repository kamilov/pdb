<?php

namespace Kamilov\PDB\Collection;

/**
 * Trait CollectionTrait
 * @package Kamilov\PDB\Collection
 */
trait CollectionTrait
{
    /**
     * @return array
     */
    public function all() : array
    {
        return iterator_to_array($this);
    }

    /**
     * Clear data
     */
    public function clear() : void
    {
        foreach ($this as $key => $value) {
            unset($this[$key]);
        }
    }

    /**
     * @param callable $callback
     * @param int|null $limit
     * @return array|null
     */
    public function filter(callable $callback, int $limit = null) : ?array
    {
        $result = [];

        foreach ($this as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                $result[$key] = $value;
            }

            if ($limit !== null && --$limit === 0) {
                break;
            }
        }

        return empty($result) ? null : $result;
    }
}
