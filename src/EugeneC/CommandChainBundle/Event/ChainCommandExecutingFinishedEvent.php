<?php
namespace EugeneC\CommandChainBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ChainCommandExecutingFinishedEvent describe event that fires when command executing was finished
 */
class ChainCommandExecutingFinishedEvent extends Event implements ChainEventInterface
{
    /**
     * @var string
     */
    private $results;

    /**
     * ChainCommandExecutingFinishedEvent constructor.
     * @param string $results
     */
    public function __construct($results)
    {
        $this->results = $results;
    }

    /**
     * Get message that describe current event
     * @return string
     */
    public function getMessage()
    {
        return $this->results;
    }
}