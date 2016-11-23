<?php
namespace EugeneC\CommandChainBundle\Tests\EventSubscriber;

use EugeneC\CommandChainBundle\Event\ChainChildDetectedEvent;
use EugeneC\CommandChainBundle\Event\ChainCommandExecutingFinishedEvent;
use EugeneC\CommandChainBundle\Event\ChainEvents;
use EugeneC\CommandChainBundle\Event\ChainExecutingFinishedEvent;
use EugeneC\CommandChainBundle\Event\ChainMasterDetectedEvent;
use EugeneC\CommandChainBundle\Event\ChainStartChildrenExecutingEvent;
use EugeneC\CommandChainBundle\Event\ChainStartMasterExecutingEvent;
use EugeneC\CommandChainBundle\EventSubscriber\ChainEventsSubscriber;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;

/**
 * Tests for ChainEventsSubscriber
 */
class ChainEventsSubscriberTest extends KernelTestCase
{
    /**
     * @var Command Symfony command
     */
    private $command;

    /**
     * @var ChainEventsSubscriber
     */
    private $subscriber;

    /**
     * @var string Message that must be logged
     */
    private $collectedMessage = '';

    /**
     * Mock command foo:hello and ChainEventsSubscriber
     */
    public function setUp()
    {
        $this->command = $this->createMock(Command::class);
        $this->command->method('getName')->willReturn('foo:hello');
        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger
            ->expects($this->any())
            ->method('info')
            ->will(
                $this->returnCallback(
                    function ($message) {
                        $this->collectedMessage = $message;
                    }
                )
            );
        $this->subscriber = new ChainEventsSubscriber($logger);
    }

    /**
     * @test
     */
    public function dispatchMasterDetectedEvent()
    {
        $this->subscriber->logEvent(new ChainMasterDetectedEvent($this->command));
        static::assertEquals(
            sprintf(
                '%s is a master command of a command chain that has registered member commands.',
                $this->command->getName()
            ),
            $this->collectedMessage
        );
    }

    /**
     * @test
     */
    public function dispatchChildDetectedEvent()
    {
        $this->subscriber->logEvent(new ChainChildDetectedEvent($this->command, 'bar:hi'));
        static::assertEquals(
            sprintf(
                '%s registered as a member of %s command chain.',
                'bar:hi',
                $this->command->getName()
            ),
            $this->collectedMessage
        );
    }

    /**
     * @test
     */
    public function dispatchStartMasterExecutingEvent()
    {
        $this->subscriber->logEvent(new ChainStartMasterExecutingEvent($this->command));
        static::assertEquals(
            sprintf(
                'Executing %s command itself first:',
                $this->command->getName()
            ),
            $this->collectedMessage
        );
    }

    /**
     * @test
     */
    public function dispatchStartChildrenExecutingEvent()
    {
        $this->subscriber->logEvent(new ChainStartChildrenExecutingEvent($this->command));
        static::assertEquals(
            sprintf(
                'Executing %s chain members:',
                $this->command->getName()
            ),
            $this->collectedMessage
        );
    }

    /**
     * @test
     */
    public function dispatchFinishCommandExecutingEvent()
    {
        $this->subscriber->logEvent(new ChainCommandExecutingFinishedEvent('Hello from Foo!'));
        static::assertEquals('Hello from Foo!', $this->collectedMessage);
    }

    /**
     * @test
     */
    public function dispatchFinishChainExecutingEvent()
    {
        $this->subscriber->logEvent(new ChainExecutingFinishedEvent($this->command));
        static::assertEquals(
            sprintf(
                'Execution of %s chain completed.',
                $this->command->getName()
            ),
            $this->collectedMessage
        );
    }

    /**
     * @test
     */
    public function checkSubscribedEvents()
    {
        static::assertEquals(
            [
                ChainEvents::MASTER_DETECTED => ['logEvent', 10],
                ChainEvents::CHILD_DETECTED => ['logEvent', 10],
                ChainEvents::START_MASTER_EXECUTING => ['logEvent', 10],
                ChainEvents::START_CHILDREN_EXECUTING => ['logEvent', 10],
                ChainEvents::FINISH_COMMAND_EXECUTING => ['logEvent', 10],
                ChainEvents::FINISH_CHAIN_EXECUTING => ['logEvent', 10]
            ],
            $this->subscriber::getSubscribedEvents()
        );
    }
}
