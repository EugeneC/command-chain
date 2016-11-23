<?php
namespace Foo2Bundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class for demo command foo2:hello
 */
class HelloCommand extends Command
{
    /**
     * Configures the foo2:hello command
     * @throws InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('foo2:hello')
            ->setDescription('Say Hello from Foo2');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello from Foo2!');
    }
}
