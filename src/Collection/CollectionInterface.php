<?php

namespace Kamilov\PDB\Collection;

/**
 * Interface CollectionInterface
 * @package Kamilov\PDB\Collection
 */
interface CollectionInterface extends \Iterator, \Countable
{
    /**
     * Return all collection elements as array
     * @return array
     */
    public function all() : array;

    /**
     * Clear data
     */
    public function clear() : void;

    /**
     * @param callable $callback
     * @param int|null $limit
     * @return array|null
     */
    public function filter(callable $callback, int $limit = null) : ?array;
}
