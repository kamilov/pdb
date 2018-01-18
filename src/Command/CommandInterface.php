<?php

namespace Kamilov\PDB\Command;

/**
 * Interface CommandInterface
 * @package Kamilov\PDB\Command
 */
interface CommandInterface
{
    /**
     * @param array ...$arguments
     * @return mixed
     */
    public function __invoke(...$arguments);
}
