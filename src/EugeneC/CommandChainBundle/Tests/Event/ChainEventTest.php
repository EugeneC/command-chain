<?php
namespace EugeneC\CommandChainBundle\Tests\Event;

use EugeneC\CommandChainBundle\Event\ChainChildDetectedEvent;
use EugeneC\CommandChainBundle\Event\ChainCommandExecutingFinishedEvent;
use EugeneC\CommandChainBundle\Event\ChainEventInterface;
use EugeneC\CommandChainBundle\Event\ChainExecutingFinishedEvent;
use EugeneC\CommandChainBundle\Event\ChainMasterDetectedEvent;
use EugeneC\CommandChainBundle\Event\ChainStartChildrenExecutingEvent;
use EugeneC\CommandChainBundle\Event\ChainStartMasterExecutingEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ChainEventTest
 */
class ChainEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Command
     */
    private $command;

    /**
     * Mock up foo:hello command
     */
    public function setUp()
    {
        $this->command = $this->createMock(Command::class);
        $this->command->method('getName')->willReturn('foo:hello');
    }
    /**
     * @test
     * @expectedException \TypeError
     */
    public function createChainMasterDetectedEventWithoutCommand()
    {
        $event = new ChainMasterDetectedEvent();
    }

    /**
     * @test
     * @expectedException \TypeError
     */
    public function createChainMasterDetectedEventWithNotCommand()
    {
        $event = new ChainMasterDetectedEvent(new \stdClass());
    }

    /**
     * @test
     */
    public function createChainMasterDetectedEventWithCommand()
    {
        $event = new ChainMasterDetectedEvent($this->command);
        static::assertInstanceOf(Event::class, $event);
        static::assertInstanceOf(ChainEventInterface::class, $event);
    }

    /**
     * @test
     */
    public function createMasterDetectedEvent()
    {
        $event = new ChainMasterDetectedEvent($this->command);
        static::assertEquals(
            sprintf(
                '%s is a master command of a command chain that has registered member commands.',
                $this->command->getName()
            ),
            $event->getMessage()
        );
    }

    /**
     * @test
     */
    public function createChildDetectedEvent()
    {
        $event = new ChainChildDetectedEvent($this->command, 'bar:hi');
        static::assertEquals(
            sprintf(
                '%s registered as a member of %s command chain.',
                'bar:hi',
                $this->command->getName()
            ),
            $event->getMessage()
        );
    }

    /**
     * @test
     */
    public function createStartMasterExecutingEvent()
    {
        $event = new ChainStartMasterExecutingEvent($this->command);
        static::assertEquals(
            sprintf(
                'Executing %s command itself first:',
                $this->command->getName()
            ),
            $event->getMessage()
        );
    }

    /**
     * @test
     */
    public function createStartChildrenExecutingEvent()
    {
        $event = new ChainStartChildrenExecutingEvent($this->command);
        static::assertEquals(
            sprintf(
                'Executing %s chain members:',
                $this->command->getName()
            ),
            $event->getMessage()
        );
    }

    /**
     * @test
     */
    public function createFinishCommandExecutingEvent()
    {
        $output = 'Hello from Foo!';
        $event = new ChainCommandExecutingFinishedEvent($output);
        static::assertEquals($output, $event->getMessage());
    }

    /**
     * @test
     */
    public function createFinishChainExecutingEvent()
    {
        $event = new ChainExecutingFinishedEvent($this->command);
        static::assertEquals(
            sprintf(
                'Execution of %s chain completed.',
                $this->command->getName()
            ),
            $event->getMessage()
        );
    }
}
