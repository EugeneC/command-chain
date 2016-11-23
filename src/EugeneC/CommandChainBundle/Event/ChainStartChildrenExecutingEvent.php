<?php
namespace EugeneC\CommandChainBundle\Event;

/**
 * Class ChainStartChildrenExecutingEvent describe event that fires when children commands executing was started
 */
class ChainStartChildrenExecutingEvent extends AbstractChainEvent
{
    /**
     * Get message that describe current event
     * @return string
     */
    public function getMessage()
    {
        return sprintf(
            'Executing %s chain members:',
            $this->command->getName()
        );
    }
}