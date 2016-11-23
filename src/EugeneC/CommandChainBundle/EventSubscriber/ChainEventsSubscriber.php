<?php
namespace EugeneC\CommandChainBundle\EventSubscriber;

use EugeneC\CommandChainBundle\Event\ChainEventInterface;
use EugeneC\CommandChainBundle\Event\ChainEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscriber for chain events
 */
class ChainEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface Logger
     */
    protected $logger;

    /**
     * ChainEventsSubscriber constructor.
     * @param LoggerInterface $logger Logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get array of subscribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ChainEvents::MASTER_DETECTED => ['logEvent', 10],
            ChainEvents::CHILD_DETECTED => ['logEvent', 10],
            ChainEvents::START_MASTER_EXECUTING => ['logEvent', 10],
            ChainEvents::START_CHILDREN_EXECUTING => ['logEvent', 10],
            ChainEvents::FINISH_COMMAND_EXECUTING => ['logEvent', 10],
            ChainEvents::FINISH_CHAIN_EXECUTING => ['logEvent', 10]
        ];
    }

    /**
     * Log event
     *
     * @param ChainEventInterface $event
     */
    public function logEvent(ChainEventInterface $event)
    {
        $this->logger->info($event->getMessage());
    }
}
