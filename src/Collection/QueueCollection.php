<?php

namespace Kamilov\PDB\Collection;

/**
 * Class QueueCollection
 * @package Kamilov\PDB\Collection
 */
class QueueCollection extends \SplQueue implements CollectionInterface
{
    use CollectionTrait;

    /**
     * @return mixed
     */
    public function pop()
    {
        return $this->dequeue();
    }

    /**
     * @param mixed $value
     */
    public function push($value)
    {
        $this->enqueue($value);
    }
}
