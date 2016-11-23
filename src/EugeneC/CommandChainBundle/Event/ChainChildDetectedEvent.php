<?php
namespace EugeneC\CommandChainBundle\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ChainChildDetectedEvent describe event that fires when child command was detected
 */
class ChainChildDetectedEvent extends Event implements ChainEventInterface
{
    /**
     * @var Command Symfony command
     */
    private $command;

    /**
     * @var string Child command name
     */
    private $childName;

    /**
     * ChainChildDetectedEvent constructor.
     * @param Command $command   Symfony command
     * @param string  $childName Child command name
     */
    public function __construct(Command $command, $childName)
    {
        $this->command   = $command;
        $this->childName = $childName;
    }

    /**
     * Get message that describe current event
     * @return string
     */
    public function getMessage()
    {
        return sprintf(
            '%s registered as a member of %s command chain.',
            $this->childName,
            $this->command->getName()
        );
    }
}