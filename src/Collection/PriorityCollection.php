<?php

namespace Kamilov\PDB\Collection;

/**
 * Class PriorityQueue
 * @package Kamilov\PDB\Collection
 */
class PriorityCollection extends \SplPriorityQueue implements CollectionInterface
{
    use CollectionTrait;
}
