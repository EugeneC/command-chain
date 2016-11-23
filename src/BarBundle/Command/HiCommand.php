<?php
namespace BarBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class for demo command bar:hi
 */
class HiCommand extends Command
{
    /**
     * Configures the bar:hi command
     * @throws InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('bar:hi')
            ->setDescription('Say Hi from Bar');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hi from Bar!');
    }
}
