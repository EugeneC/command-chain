<?php
namespace EugeneC\CommandChainBundle\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class AbstractChainEvent provide constructor over symfony event to inject symfony command
 */
abstract class AbstractChainEvent extends Event implements ChainEventInterface
{
    /**
     * @var Command Symfony command
     */
    protected $command;

    /**
     * AbstractChainEvent constructor.
     * @param Command $command Symfony command
     */
    public function __construct(Command $command)
    {
        $this->command = $command;
    }
}