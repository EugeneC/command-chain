<?php
namespace EugeneC\CommandChainBundle\Event;

/**
 * Class ChainStartMasterExecutingEvent describe event that fires when master command executing was started
 */
class ChainStartMasterExecutingEvent extends AbstractChainEvent
{
    /**
     * Get message that describe current event
     * @return string
     */
    public function getMessage()
    {
        return sprintf(
            'Executing %s command itself first:',
            $this->command->getName()
        );
    }
}