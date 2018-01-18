<?php

namespace Kamilov\PDB\Console\Command;

use Kamilov\PDB\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ServerCommand
 * @package Kamilov\PDB\Console\Command
 */
class ServerCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('server')
            ->setDescription('Run PDB server')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Listening address', '127.0.0.1')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Port number', 22389)
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $host = $input->getOption('host');
        $port = $input->getOption('port');

        if ($io->isVerbose() || $io->isVeryVerbose()) {
            $io->title(sprintf('Serve PDB on %s:%d', $host, $port));
        }

        try {
            $server = new Server(
                $host,
                $port,
                new ConsoleLogger($output)
            );

            $server->run();
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
            return $exception->getCode();
        }

        return 0;
    }
}
