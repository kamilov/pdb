<?php

namespace Kamilov\PDB;

use function Amp\asyncCall;
use Amp\Loop;
use function Amp\Socket\listen;
use Amp\Socket\ServerSocket;
use Kamilov\PDB\Collection\CollectionPool;
use Kamilov\PDB\Command\CommandInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Server
 * @package Kamilov\PDB
 */
class Server
{
    /** @var string */
    private $uri;
    /** @var LoggerInterface */
    private $logger;
    /** @var array */
    private $commands = [];
    /** @var CollectionPool */
    private $collectionPool;

    /**
     * Server constructor.
     * @param string $host
     * @param int $port
     */
    public function __construct(string $host, int $port, LoggerInterface $logger)
    {
        $this->uri = sprintf('tcp://%s:%d', $host, $port);
        $this->logger = $logger;
        $this->collectionPool = new CollectionPool();
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger() : LoggerInterface
    {
        return $this->logger;
    }

    /**
     *
     */
    public function run() : void
    {
        Loop::run(function () {
            $server = listen($this->uri);

            $this->getLogger()->info(sprintf('Listening for new connections on %s', $this->uri));

            while ($socket = yield $server->accept()) {
                asyncCall(function (ServerSocket $socket) {
                    $message = yield $socket->read();
                    $this->handleMessage($message, $socket);
                }, $socket);
            }
        });
    }

    /**
     * @param ServerSocket $socket
     */
    protected function handleMessage(string $message, ServerSocket $socket) : void
    {
        $this->getLogger()->info(sprintf(
            'Accepted connection from %s',
            $socket->getRemoteAddress()
        ));

        try {
            /**
             * @var string $command
             * @var array $arguments
             */
            extract(json_decode($message, true));

            $this->getLogger()->info(sprintf(
                'Command %s(%s)',
                $command,
                implode(', ', array_map('json_encode', $arguments))
            ));

            try {
                $result = $this->runCommand($command, $arguments);

                $socket->write(json_encode($result));
            } catch (\Exception $exception) {
                $socket->write(json_encode([
                    'error' => $exception->getMessage(),
                ]));
            }
        } catch (\Exception $exception) {
            $this->getLogger()->error($exception->getMessage(), compact('exception'));
        }
    }

    /**
     * @param string $command
     * @param array $arguments
     * @return mixed
     */
    protected function runCommand(string $command, array $arguments)
    {
        if (!isset($this->commands[$command])) {
            $this->commands[$command] = new $command($this->collectionPool);

            if (!$this->commands[$command] instanceof CommandInterface) {
                throw new \RuntimeException(sprintf(
                    'Command class %s not implemented %s',
                    $command,
                    CommandInterface::class
                ));
            }
        }

        return $this->commands[$command](...$arguments);
    }
}
