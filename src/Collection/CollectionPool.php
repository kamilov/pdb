<?php

namespace Kamilov\PDB\Collection;

/**
 * Class CollectionPool
 * @package Kamilov\PDB\Collection
 */
class CollectionPool
{
    /**
     * @var array
     */
    private $collections = [];

    /**
     * @param string $className
     * @param string $name
     * @return CollectionInterface
     */
    private function getCollectionOrCreate(string $className, string $name) : CollectionInterface
    {
        if (!isset($this->collections[$className])) {
            $this->collections[$className] = [];
        }
        if (!isset($this->collections[$className][$name])) {
            $this->collections[$className][$name] = new $className;
        }

        return $this->collections[$className][$name];
    }

    /**
     * @param string $collection
     * @return CollectionInterface
     */
    public function map(string $collection) : CollectionInterface
    {
        return $this->getCollectionOrCreate(MapCollection::class, $collection);
    }

    /**
     * @param string $collection
     * @return CollectionInterface
     */
    public function priority(string $collection) : CollectionInterface
    {
        return $this->getCollectionOrCreate(PriorityCollection::class, $collection);
    }

    /**
     * @param string $collection
     * @return CollectionInterface
     */
    public function queue(string $collection) : CollectionInterface
    {
        return $this->getCollectionOrCreate(QueueCollection::class, $collection);
    }

    /**
     * @param string $collection
     * @return CollectionInterface
     */
    public function stack(string $collection) : CollectionInterface
    {
        return $this->getCollectionOrCreate(StackCollection::class, $collection);
    }
}
