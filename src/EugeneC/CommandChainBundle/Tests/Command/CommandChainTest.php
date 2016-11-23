<?php
namespace EugeneC\CommandChainBundle\Tests\Command;

use BarBundle\Command\HiCommand;
use EugeneC\CommandChainBundle\Tests\Tester\EventDispatchingCommandTester;
use Foo2Bundle\Command\HelloCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests for command chain
 */
class CommandChainTest extends KernelTestCase
{
    /**
     * Boot kernel
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    /**
     * Try to execute child command
     * @test
     */
    public function executeChildCommand()
    {
        $application = new Application(static::$kernel);
        $application->add(new HiCommand());
        $command = $application->find('bar:hi');
        $commandTester = new EventDispatchingCommandTester($command, static::$kernel->getContainer()->get('event_dispatcher'));
        $commandTester->execute(['command' => $command->getName()]);

        static::assertContains(
            'Error: bar:hi command is a member of foo2:hello command(s) chain and cannot be executed on its own.',
            $commandTester->getDisplay()
        );
    }

    /**
     * Try to execute master command
     * @test
     */
    public function successfullyExecuteChain()
    {
        $application = new Application(static::$kernel);
        $application->add(new HelloCommand());
        $command = $application->find('foo2:hello');
        $commandTester = new EventDispatchingCommandTester($command, static::$kernel->getContainer()->get('event_dispatcher'));
        $commandTester->execute(['command' => $command->getName()]);

        static::assertContains('Hello from Foo2!'.PHP_EOL.
            'Hi from Bar!'.PHP_EOL.
            'Hello from Foo! First name: Foo; Second name: foo Command from chain 1.'.PHP_EOL.
            'Hello from Foo! First name: Foo; Second name: foo Command from chain 3.',
            $commandTester->getDisplay()
        );
    }
}
