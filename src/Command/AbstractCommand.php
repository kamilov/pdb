<?php

namespace Kamilov\PDB\Command;
use Kamilov\PDB\Collection\CollectionPool;

/**
 * Class AbstractCommand
 * @package Kamilov\PDB\Command
 */
abstract class AbstractCommand implements CommandInterface
{
    /** @var CollectionPool */
    protected $collectionPool;

    /**
     * AbstractCommand constructor.
     * @param CollectionPool $collectionPool
     */
    public function __construct(CollectionPool $collectionPool)
    {
        $this->collectionPool = $collectionPool;
    }

    /**
     * @param array ...$arguments
     * @return mixed
     */
    public function __invoke(...$arguments)
    {
        return $this->execute(...$arguments);
    }
}
