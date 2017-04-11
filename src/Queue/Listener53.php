<?php

namespace Dusterio\LaravelVerbose\Queue;

use Illuminate\Queue\ListenerOptions;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

class Listener53 extends \Illuminate\Queue\Listener
{
    /**
     * Create a new Symfony process for the worker.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  int     $delay
     * @param  int     $memory
     * @param  int     $timeout
     * @return \Symfony\Component\Process\Process
     */
    public function makeProcess($connection, $queue, $delay, $memory, $timeout)
    {
        $string = $this->workerCommand;

        // If the environment is set, we will append it to the command string so the
        // workers will run under the specified environment. Otherwise, they will
        // just run under the production environment which is not always right.
        if (isset($this->environment)) {
            $string .= ' --env='.ProcessUtils::escapeArgument($this->environment);
        }

        // Next, we will just format out the worker commands with all of the various
        // options available for the command. This will produce the final command
        // line that we will pass into a Symfony process object for processing.
        $command = sprintf(
            $string,
            ProcessUtils::escapeArgument($connection),
            ProcessUtils::escapeArgument($queue),
            $delay,
            $memory,
            $this->sleep,
            $this->maxTries
        );

        return new Process($command, $this->commandPath, null, null, $timeout);
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
