<?php
namespace EugeneC\CommandChainBundle\Tests\EventSubscriber;

use EugeneC\CommandChainBundle\Composite\CommandChainList;
use EugeneC\CommandChainBundle\EventSubscriber\CommandChainSubscriber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;

/**
 * Tests for CommandChainSubscriber
 */
class CommandChainSubscriberTest extends KernelTestCase
{
    /**
     * @var CommandChainSubscriber
     */
    private $commandChainSubscriber;

    /**
     * @var Command Mock for symfony command foo2:hello
     */
    private $commandFoo2;

    /**
     * @var Command Mock for symfony command foo:hello
     */
    private $commandFoo;

    /**
     * @var BufferedOutput
     */
    private $bufferedOutput;

    /**
     * Mock commands, event dispatcher and application
     */
    public function setUp()
    {
        $chains = [
            'chain_1' => [
                'main' => 'foo2:hello',
                'child' => 'foo:hello',
                'parameters' => [
                    'arg1' => [
                        'name' => 'firstName',
                        'value' => 'Foo command'
                    ]
                ]
            ],
            'chain_2' => [
                'main' => 'foo2:hello',
                'child' => 'bar:hi'
            ],
            'chain_3' => [
                'main' => 'foo:hello',
                'child' => 'bar:hi'
            ]
        ];
        $this->commandChainSubscriber = new CommandChainSubscriber(
            new CommandChainList($chains),
            $this->createMock(TraceableEventDispatcher::class)
        );
        /** Mock commands */
        $this->commandFoo2 = $this->createMock(Command::class);
        $this->commandFoo2->method('getName')->willReturn('foo2:hello');
        $this->commandFoo = $this->createMock(Command::class);
        $this->commandFoo->method('getName')->willReturn('foo:hello');
        $commandBar = $this->createMock(Command::class);
        $commandBar->method('getName')->willReturn('bar:hi');
        /** Mock commands output */
        $output = new BufferedOutput();
        $this->commandFoo2->method('run')->will($this->returnCallback(
            function () use (&$output) {
                return $output->writeln('Hello from Foo2!');
            }
        ));
        $this->commandFoo->method('run')->will($this->returnCallback(
            function () use (&$output) {
                return $output->writeln('Hello from Foo!');
            }
        ));
        $commandBar->method('run')->will($this->returnCallback(
            function () use (&$output) {
                return $output->writeln('Hi from Bar!');
            }
        ));
        $this->bufferedOutput = $output;
        /** Mock application */
        $application = $this->createMock(Application::class);
        $application->expects($this->any())
            ->method('get')
            ->will(
                $this->returnValueMap(
                    [
                        ['foo2:hello', $this->commandFoo2],
                        ['foo:hello', $this->commandFoo],
                        ['bar:hi', $commandBar]
                    ]
                )
            );
        $this->commandFoo2->method('getApplication')->willReturn($application);
    }

    /**
     * @test
     */
    public function childExecuting()
    {
        $event = $this->createMock(ConsoleCommandEvent::class);
        $event->method('getCommand')->willReturn($this->commandFoo);
        $event->method('getOutput')->willReturn($this->bufferedOutput);
        $this->commandChainSubscriber->beforeCommand($event);
        static::assertEquals(
            "Error: foo:hello command is a member of foo2:hello command(s) chain and cannot be executed on its own.\n",
            $this->bufferedOutput->fetch()
        );
    }

    /**
     * @test
     */
    public function masterExecuting()
    {
        $this->commandChainSubscriber->beforeCommand($this->mockMasterCommandEvent());
        $this->commandChainSubscriber->afterCommand($this->mockMasterTerminateEvent());
        static::assertEquals("Hello from Foo2!\nHello from Foo!\nHi from Bar!\nHi from Bar!\n", $this->bufferedOutput->fetch());
    }

    /**
     * @return ConsoleCommandEvent
     */
    private function mockMasterCommandEvent()
    {
        $commandEvent = $this->createMock(ConsoleCommandEvent::class);
        $commandEvent->method('getCommand')->willReturn($this->commandFoo2);
        $commandEvent->method('getOutput')->willReturn($this->bufferedOutput);
        $commandEvent->method('getInput')->willReturn(new ArrayInput(['command' => 'foo2:hello']));
        static::assertInstanceOf(ConsoleCommandEvent::class, $commandEvent);

        return $commandEvent;
    }

    /**
     * @test
     */
    public function checkSubscribedEvents()
    {
        static::assertEquals(
            [
                ConsoleEvents::COMMAND => ['beforeCommand', 10],
                ConsoleEvents::TERMINATE => ['afterCommand', 10]
            ],
            $this->commandChainSubscriber::getSubscribedEvents()
        );
    }

    /**
     * @return ConsoleTerminateEvent
     */
    private function mockMasterTerminateEvent()
    {
        $terminateEvent = $this->createMock(ConsoleTerminateEvent::class);
        $terminateEvent->method('getCommand')->willReturn($this->commandFoo2);
        $terminateEvent->method('getInput')->willReturn(new ArrayInput([]));
        $terminateEvent->method('getOutput')->willReturn($this->bufferedOutput);
        static::assertInstanceOf(ConsoleTerminateEvent::class, $terminateEvent);

        return $terminateEvent;
    }
}
