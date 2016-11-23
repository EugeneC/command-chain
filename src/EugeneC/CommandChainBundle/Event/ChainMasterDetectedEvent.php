<?php
namespace EugeneC\CommandChainBundle\Event;

/**
 * Class ChainMasterDetectedEvent describe event that fires when master command was detected
 */
class ChainMasterDetectedEvent extends AbstractChainEvent
{
    /**
     * Get message that describe current event
     * @return string
     */
    public function getMessage()
    {
        return sprintf(
            '%s is a master command of a command chain that has registered member commands.',
            $this->command->getName()
        );
    }
}