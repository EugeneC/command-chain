<?php
namespace FooBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class for demo command foo:hello with argument and option
 */
class HelloCommand extends Command
{
    /**
     * Configures the foo:hello command
     * @throws InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('foo:hello')
            ->setDescription('Say Hello from Foo')
            ->addArgument('firstName', InputArgument::REQUIRED)
            ->addOption('secondName', null, InputOption::VALUE_REQUIRED, '', '');
    }

    /**
     * {@inheritDoc}
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            sprintf(
                'Hello from Foo! First name: %s; Second name: %s.',
                $input->getArgument('firstName'),
                $input->getOption('secondName')
            )
        );
    }
}
