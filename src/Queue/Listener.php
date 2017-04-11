<?php

namespace Dusterio\LaravelVerbose\Queue;

use Illuminate\Queue\ListenerOptions;
use Symfony\Component\Process\Process;

class Listener extends \Illuminate\Queue\Listener
{
    /**
     * Create a new Symfony process for the worker.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  ListenerOptions  $options
     * @return \Symfony\Component\Process\Process
     */
    public function makeProcess($connection, $queue, ListenerOptions $options)
    {
        $command = $this->workerCommand;

        // If the environment is set, we will append it to the command string so the
        // workers will run under the specified environment. Otherwise, they will
        // just run under the production environment which is not always right.
        if (isset($options->environment)) {
            $command = $this->addEnvironment($command, $options);
        }

        if (isset($options->verbosity)) {
            $command = $this->addVerbosity($command, $options);
        }

        // Next, we will just format out the worker commands with all of the various
        // options available for the command. This will produce the final command
        // line that we will pass into a Symfony process object for processing.
        $command = $this->formatCommand(
            $command, $connection, $queue, $options
        );

        return new Process(
            $command, $this->commandPath, null, null, $options->timeout
        );
    }

    /**
     * Resolve a Symfony verbosity level back to its CLI parameter.
     *
     * @param  string  $command
     * @param  ListenerOptions  $options
     * @return string
     */
    protected function addVerbosity($command, ListenerOptions $options)
    {
        return $command.' -'.$options->verbosity;
    }
}
