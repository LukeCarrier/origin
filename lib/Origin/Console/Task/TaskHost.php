<?php

/**
 * Task host library.
 *
 * Origin.
 *
 * @author    Luke Carrier <luke@carrier.im>
 * @copyright 2014 Luke Carrier
 * @license   Proprietary; all rights reserved
 */

namespace Origin\Console\Task;
use Origin\Console\Arguments\Parser as ArgumentParser,
    Origin\Util\CallableUtil;

/**
 * Task host for CLI applications.
 *
 * The task host library provides an environment for your command line
 * applications. Using the {@link \Origin\Console\Arguments\Parser} to interpret
 * command line arguments, the library provides a similar dispatching framework
 * for actions as the {@link \Origin\Routing\Router} does for web applications.
 */
class TaskHost {
    /**
     * The {@link \Origin\Console\Arguments\Parser} instance.
     *
     * The command line argument parser.
     *
     * @var \Origin\Console\Arguments\Parser
     */
    protected $argument_parser;

    /**
     * The argument vector.
     *
     * This array should contain all of the command line arguments passed to the
     * application on invocation.
     *
     * @var array<integer, string>
     */
    protected $argument_vector;

    /**
     * All registered actions.
     *
     * @var array<integer, ITask>
     */
    protected $tasks;

    /**
     * Initialiser.
     *
     * @param array $argument_vector The argument vector.
     */
    public function __construct($argument_vector) {
        $desc = 'Allows developers and operations teams to automate common tasks';
        $this->argument_parser = new ArgumentParser(null, 'Origin task host',
                                                    $desc);
        $this->argument_vector = $argument_vector;
    }

    /**
     * Add an argument that affects all tasks.
     *
     * @param array $argument The argument to add. This should be an array
     *        containing fields named after the parameters of
     *        {@link ArgumentParser->addArgument()}.
     *
     * @return void
     */
    public function addGlobalArgument($argument) {
        $argument = static::normaliseArgument($argument);
        $this->argument_parser->addArgument($argument['long'],
                                            $argument['short'],
                                            $argument['destination'],
                                            $argument['num_args'],
                                            $argument['help'],
                                            $argument['required']);
    }

    /**
     * Register a task.
     *
     * @param array $task An array of indformation about the task, where the
     *                    keys are named after parameters to the parser's
     *                    addArgument message.
     *
     * @return void
     */
    public function addTask($task) {
        $defaults = array(
            'short' => null,
            'destination' => null,
            'num_args' => 0,
            'help' => null,
            'required' => null,
        );

        $subparser = $this->argument_parser->addSubparser($task['name']);
        foreach ($task['arguments'] as $name => $value) {
            $value = array_merge($defaults, $value);
            $subparser->addArgument($name, $value['short'],
                                    $value['destination'], $value['num_args'],
                                    $value['help'], $value['required']);
        }
        $this->tasks[$task['name']] = [$task['handler'], 'run'];
    }

    /**
     * Execute a task.
     *
     * @todo Subclass Exception!
     *
     * @return mixed The result of the task.
     */
    public function runTask() {
        $arguments = $this->argument_parser->parseArguments($this->argument_vector);

        if (!array_key_exists('action', $arguments)) {
            throw new \Exception('no action specified');
        }

        if (!array_key_exists('action', $arguments)) {
            throw new \Exception('no such action');
        }

        $handler = $this->tasks[$arguments['action']];
        
        return CallableUtil::callWithNamedParameters($handler,
                                                     $arguments['subparser']);
    }

    /**
     * Normalise an argument's parameters.
     *
     * @param array $argument An array containing information about an argument,
     *                        where the keys match those to the parser's
     *                        addArgument method.
     *
     * @return array The normalised argument.
     */
    public static function normaliseArgument($argument) {
        $argument['short']       = (array_key_exists('short',       $argument))
                                    ? $argument['short']       : null;
        $argument['destination'] = (array_key_exists('destination', $argument))
                                    ? $argument['destination'] : $argument['long'];
        $argument['num_args']    = (array_key_exists('num_args',    $argument))
                                    ? $argument['num_args']    : 0;
        $argument['help']        = (array_key_exists('help',        $argument))
                                    ? $argument['help']        : null;
        $argument['required']    = (array_key_exists('required',    $argument))
                                    ? $argument['required']    : false;

        return $argument;
    }
}
