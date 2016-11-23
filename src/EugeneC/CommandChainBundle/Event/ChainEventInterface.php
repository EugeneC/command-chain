<?php
namespace EugeneC\CommandChainBundle\Event;

/**
 * Class ChainEventInterface.
 * Chain events must be logged with message string.
 */
interface ChainEventInterface
{
    /**
     * @return string Message that describe current event
     */
    public function getMessage();
}