<?php

namespace Kamilov\PDB\Command;

/**
 * Class CollectionCommand
 * @package Kamilov\PDB\Command
 */
class CollectionCommand extends AbstractCommand
{
    /**
     * @param string $collectionType
     * @param string $collectionName
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    protected function execute(string $collectionType, string $collectionName, string $method, array $arguments)
    {
        $collection = $this->collectionPool->{$collectionType}($collectionName);
        return call_user_func([$collection, $method], ...$arguments);
    }
}
