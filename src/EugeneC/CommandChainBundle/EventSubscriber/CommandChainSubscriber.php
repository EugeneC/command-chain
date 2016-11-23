<?php
namespace EugeneC\CommandChainBundle\EventSubscriber;

use EugeneC\CommandChainBundle\Composite\CommandChainListInterface;
use EugeneC\CommandChainBundle\Event\ChainChildDetectedEvent;
use EugeneC\CommandChainBundle\Event\ChainCommandExecutingFinishedEvent;
use EugeneC\CommandChainBundle\Event\ChainEvents;
use EugeneC\CommandChainBundle\Event\ChainExecutingFinishedEvent;
use EugeneC\CommandChainBundle\Event\ChainMasterDetectedEvent;
use EugeneC\CommandChainBundle\Event\ChainStartChildrenExecutingEvent;
use EugeneC\CommandChainBundle\Event\ChainStartMasterExecutingEvent;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscriber for symfony command events
 */
class CommandChainSubscriber implements EventSubscriberInterface
{
    /**
     * @var CommandChainListInterface Chains configuration
     */
    protected $chainList;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var BufferedOutput
     */
    protected $bufferedOutput;

    /**
     * @var Application
     */
    protected $application;

    /**
     * CommandChainSubscriber constructor.
     * @param CommandChainListInterface $chainList       Chains configuration
     * @param EventDispatcherInterface  $eventDispatcher Event dispatcher
     */
    public function __construct(CommandChainListInterface $chainList, EventDispatcherInterface $eventDispatcher)
    {
        $this->chainList       = $chainList;
        $this->eventDispatcher = $eventDispatcher;
        $this->bufferedOutput  = new BufferedOutput();
    }

    /**
     * Get array of subscribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND   => ['beforeCommand', 10],
            ConsoleEvents::TERMINATE => ['afterCommand', 10]
        ];
    }

    /**
     * Execute method before command
     *
     * @param ConsoleCommandEvent $event
     * @throws \Exception
     */
    public function beforeCommand(ConsoleCommandEvent $event)
    {
        $output = $event->getOutput();
        $currentCommand = $event->getCommand();
        if ($this->chainList->isChild($currentCommand->getName())) {
            $output->writeln(sprintf('<error>Error</error>: <info>%s</info> command is a member of <info>%s</info> command(s) chain and cannot be executed on its own.', $currentCommand->getName(), implode($this->chainList->getParents($currentCommand->getName()), ', ')));
            $event->disableCommand();

            return;
        }
        if ($this->chainList->isMaster($currentCommand->getName())) {
            $this->eventDispatcher->dispatch(ChainEvents::MASTER_DETECTED, new ChainMasterDetectedEvent($currentCommand));
            $children = $this->chainList->getChildren($currentCommand->getName());
            foreach ($children as $child) {
                $this->eventDispatcher->dispatch(ChainEvents::CHILD_DETECTED, new ChainChildDetectedEvent($currentCommand, $child['command']));
            }
            $this->eventDispatcher->dispatch(ChainEvents::START_MASTER_EXECUTING, new ChainStartMasterExecutingEvent($currentCommand));
            $event->disableCommand();
            $currentCommand->run($event->getInput(), $this->bufferedOutput);
            $bufferedResults = $this->bufferedOutput->fetch();
            $output->write($bufferedResults);
            $this->eventDispatcher->dispatch(ChainEvents::FINISH_COMMAND_EXECUTING, new ChainCommandExecutingFinishedEvent($bufferedResults));
            $this->application = $currentCommand->getApplication();
        }
    }

    /**
     * Execute method after command
     *
     * @param ConsoleTerminateEvent $event
     * @throws CommandNotFoundException If command not found
     * @throws \Exception
     */
    public function afterCommand(ConsoleTerminateEvent $event)
    {
        $currentCommand = $event->getCommand();
        if ($this->chainList->isMaster($currentCommand->getName()) && !$this->chainList->isChild($currentCommand->getName())) {
            $this->eventDispatcher->dispatch(ChainEvents::START_CHILDREN_EXECUTING, new ChainStartChildrenExecutingEvent($currentCommand));
            $this->childrenCommandsRun($currentCommand, $event);
            $this->eventDispatcher->dispatch(ChainEvents::FINISH_CHAIN_EXECUTING, new ChainExecutingFinishedEvent($currentCommand));
        }
    }

    /**
     * Recursion run of the child commands
     *
     * @param Command               $command
     * @param ConsoleTerminateEvent $event
     * @throws CommandNotFoundException If command not found
     * @throws \Exception
     */
    private function childrenCommandsRun(Command $command, ConsoleTerminateEvent $event)
    {
        $children = $this->chainList->getChildren($command->getName());
        foreach ($children as $child) {
            $childCommand = $this->application->get($child['command']);
            $childCommand->run(new ArrayInput($child), $this->bufferedOutput);
            $bufferedResults = $this->bufferedOutput->fetch();
            $event->getOutput()->write($bufferedResults);
            $this->eventDispatcher->dispatch(ChainEvents::FINISH_COMMAND_EXECUTING, new ChainCommandExecutingFinishedEvent($bufferedResults));
            $this->childrenCommandsRun($childCommand, $event);
        }
    }
}
