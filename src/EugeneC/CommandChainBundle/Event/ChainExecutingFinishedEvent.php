<?php
namespace EugeneC\CommandChainBundle\Event;

/**
 * Class ChainExecutingFinishedEvent describe event that fires when chain executing was finished
 */
class ChainExecutingFinishedEvent extends AbstractChainEvent
{
    /**
     * Get message that describe current event
     * @return string
     */
    public function getMessage()
    {
        return sprintf(
            'Execution of %s chain completed.',
            $this->command->getName()
        );
    }
}