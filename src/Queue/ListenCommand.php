<?php

namespace Dusterio\LaravelVerbose\Queue;

use Illuminate\Queue\Listener;
use Illuminate\Console\Command;
use Illuminate\Queue\ListenerOptions;

class ListenCommand extends \Illuminate\Queue\Console\ListenCommand
{
    /**
     * Get the listener options for the command.
     *
     * @return \Illuminate\Queue\ListenerOptions
     */
    protected function gatherOptions()
    {
        return new ListenerOptions(
            $this->option('env'), $this->option('delay'),
            $this->option('memory'), $this->option('timeout'),
            $this->option('sleep'), $this->option('tries'),
            $this->option('force'), $this->resolveVerbosityParameter()
        );
    }

    /**
     * Resolve a Symfony verbosity level back to its CLI parameter.
     *
     * @return string|null
     */
    private function resolveVerbosityParameter()
    {
        $currentVerbosity = $this->output->getVerbosity();
        $parameter = array_search($currentVerbosity, $this->verbosityMap);

        return $parameter ?: null;
    }
}
