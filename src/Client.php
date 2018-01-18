<?php

namespace Kamilov\PDB;

use Amp\Loop;
use Amp\Socket\ClientSocket;
use function Amp\Socket\connect;
use Kamilov\PDB\Collection\CollectionInterface;
use Kamilov\PDB\Collection\MapCollection;
use Kamilov\PDB\Collection\PriorityCollection;
use Kamilov\PDB\Collection\QueueCollection;
use Kamilov\PDB\Collection\StackCollection;
use Kamilov\PDB\Command\CollectionCommand;
use Kamilov\PDB\Proxy\Generator;
use Kamilov\PDB\Proxy\Proxy;

/**
 * Class Client
 * @package Kamilov\PDB
 * @property object $map
 * @property object $priority
 * @property object $queue
 * @property object $stack
 */
class Client
{
    /** @var string */
    private $uri;

    /**
     * Client constructor.
     * @param string $host
     * @param int $port
     */
    public function __construct(string $host, int $port)
    {
        $this->uri = sprintf('tcp://%s:%d', $host, $port);
    }

    /**
     * @param string $method
     * @return object
     */
    public function __get(string $method)
    {
        $reflection = new \ReflectionClass(static::class);

        if ($method === '__get' || !($reflection->hasMethod($method) && $reflection->getMethod($method)->isPublic())) {
            throw new \InvalidArgumentException(sprintf('There are no property "%s" in client class.', $method));
        }

        return new Generator($this, $method);
    }

    /**
     * @param string $collection
     * @return CollectionInterface|Proxy
     */
    public function map(string $collection) : Proxy
    {
        return new Proxy(function (string $method, array $arguments) use ($collection) {
            $this->checkCollectionMethod(MapCollection::class, $method);
            return $this->command(
                CollectionCommand::class,
                'map',
                $collection,
                $method,
                $arguments
            );
        });
    }

    /**
     * @param string $collection
     * @return PriorityCollection|Proxy
     */
    public function priority(string $collection) : Proxy
    {
        return new Proxy(function (string $method, array $arguments) use ($collection) {
            $this->checkCollectionMethod(PriorityCollection::class, $method);
            return $this->command(
                CollectionCommand::class,
                'priority',
                $collection,
                $method,
                $arguments
            );
        });
    }

    /**
     * @param string $collection
     * @return Proxy
     */
    public function queue(string $collection) : Proxy
    {
        return new Proxy(function (string $method, array $arguments) use ($collection) {
            $this->checkCollectionMethod(QueueCollection::class, $method);
            return $this->command(
                CollectionCommand::class,
                'queue',
                $collection,
                $method,
                $arguments
            );
        });
    }

    /**
     * @param string $collection
     * @return Proxy
     */
    public function stack(string $collection) : Proxy
    {
        return new Proxy(function (string $method, array $arguments) use ($collection) {
            $this->checkCollectionMethod(StackCollection::class, $method);
            return $this->command(
                CollectionCommand::class,
                'queue',
                $collection,
                $method,
                $arguments
            );
        });
    }

    /**
     * @param string $command
     * @param array ...$arguments
     * @return mixed
     */
    private function command(string $command, ...$arguments)
    {
        $message = json_encode(compact('command', 'arguments'));
        $result = null;

        Loop::run(function () use ($message, &$result) {
            /** @var ClientSocket $socket */
            $socket = yield connect($this->uri);

            yield $socket->write($message);

            $response = yield $socket->read();

            $result = json_decode($response, true);
        });

        return $result;
    }

    /**
     * @param string $className
     * @param string $methodName
     */
    private function checkCollectionMethod(string $className, string $methodName)
    {
        $reflection = new \ReflectionClass($className);

        if (!$reflection->hasMethod($methodName) || !$reflection->getMethod($methodName)->isPublic()) {
            throw new \RuntimeException(sprintf(
                'Collection "%s" does not have method "%s".',
                $className,
                $methodName
            ));
        }
    }
}
