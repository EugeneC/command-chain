<?php
namespace EugeneC\CommandChainBundle\Tests\Tester;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * CommandTester that dispatch command events
 */
class EventDispatchingCommandTester
{
    /**
     * @var Command
     */
    private $command;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param Command                  $command    A Command instance to test.
     * @param EventDispatcherInterface $dispatcher Dispatcher instance
     */
    public function __construct(Command $command, EventDispatcherInterface $dispatcher)
    {
        $this->command    = $command;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Executes the command and dispatch command events
     *
     * @param array $input   An array of command arguments and options
     * @param array $options An array of execution options
     * @return int The command exit code
     *
     * @throws \Exception
     * @see \Symfony\Component\Console\Tester\CommandTester::execute
     */
    public function execute(array $input, array $options = array())
    {
        $this->createIO($input, $options);

        $event = new ConsoleCommandEvent($this->command, $this->input, $this->output);
        $this->dispatcher->dispatch(ConsoleEvents::COMMAND, $event);

        if ($event->commandShouldRun()) {
            try {
                $exitCode = $this->command->run($this->input, $this->output);
            } catch (\Exception $e) {
                $event = new ConsoleTerminateEvent($this->command, $this->input, $this->output, $e->getCode());
                $this->dispatcher->dispatch(ConsoleEvents::TERMINATE, $event);

                $event = new ConsoleExceptionEvent($this->command, $this->input, $this->output, $e, $event->getExitCode());
                $this->dispatcher->dispatch(ConsoleEvents::EXCEPTION, $event);

                throw $event->getException();
            }
        } else {
            $exitCode = ConsoleCommandEvent::RETURN_CODE_DISABLED;
        }

        $event = new ConsoleTerminateEvent($this->command, $this->input, $this->output, $exitCode);
        $this->dispatcher->dispatch(ConsoleEvents::TERMINATE, $event);

        return $this->statusCode = $event->getExitCode();
    }

    /**
     * Gets the display returned by the last execution of the command.
     *
     * @param bool $normalize Whether to normalize end of lines to \n or not
     *
     * @return string The display
     */
    public function getDisplay($normalize = false)
    {
        rewind($this->output->getStream());

        $display = stream_get_contents($this->output->getStream());

        if ($normalize) {
            $display = str_replace(PHP_EOL, "\n", $display);
        }

        return $display;
    }

    /**
     * Gets the input instance used by the last execution of the command.
     *
     * @return InputInterface The current input instance
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Gets the output instance used by the last execution of the command.
     *
     * @return OutputInterface The current output instance
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Gets the status code returned by the last execution of the application.
     *
     * @return int The status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param array $input   An array of command arguments and options
     * @param array $options An array of execution options
     */
    private function createIO(array $input, array $options = array())
    {
        // Set the command name automatically if the application requires
        // This argument and no command name was passed
        if (!isset($input['command'])
            && (null !== $application = $this->command->getApplication())
            && $application->getDefinition()->hasArgument('command')
        ) {
            $input['command'] = $this->command->getName();
        }

        $this->input = new ArrayInput($input);

        if (isset($options['interactive'])) {
            $this->input->setInteractive($options['interactive']);
        }

        $this->output = new StreamOutput(fopen('php://memory', 'w', false));

        if (isset($options['decorated'])) {
            $this->output->setDecorated($options['decorated']);
        }

        if (isset($options['verbosity'])) {
            $this->output->setVerbosity($options['verbosity']);
        }
    }
}