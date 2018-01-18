<?php

namespace Kamilov\PDB\Console\Command;

use Amp\Loop;
use function Amp\Socket\connect;
use Kamilov\PDB\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class ClientCommand
 * @package Kamilov\PDB\Console\Command
 */
class ClientCommand extends Command
{
    private const EXIT_COMMAND = 'exit';
    private const DEFAULT_AUTOCOMPLETER_VALUES = [
        'pdb',
        'pdb.map',
        self::EXIT_COMMAND,
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('client')
            ->setDescription('PDB client shell.')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'PDB server host', '127.0.0.1')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'PDB server port', 22389)
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

        try {
            $io->title('Welcome to PDB client');

            $client = new Client($host, $port);
            $language = new ExpressionLanguage();
            $helper = $this->getHelper('question');
            $question = new Question('> ');
            $autocompleterValues = self::DEFAULT_AUTOCOMPLETER_VALUES;

            do {
                $question->setAutocompleterValues(
                    $autocompleterValues
                );

                $autocompleterValues[] = $command = $helper->ask($input, $output, $question);

                if ($command === self::EXIT_COMMAND) {
                    break;
                }

                try {
                    $result = $language->evaluate($command, [
                        'pdb' => $client
                    ]);

                    $io->writeln(print_r($result, true));
                } catch (\Exception $exception) {
                    $io->warning($exception->getMessage());
                }
            } while (true);
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
            return $exception->getCode();
        }

        return 0;
    }

    /**
     * @return array
     */
    private function getAutocompleterValues() : array
    {
        return [
            'pdb',
            'pdb.map'
        ];
    }
}
