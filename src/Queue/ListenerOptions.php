<?php

namespace Dusterio\LaravelVerbose\Queue;

class ListenerOptions extends \Illuminate\Queue\ListenerOptions
{
    /**
     * Verbosity level (v/vv/vvv).
     *
     * @var string
     */
    public $verbosity;

    /**
     * Create a new listener options instance.
     *
     * @param  string  $environment
     * @param  int  $delay
     * @param  int  $memory
     * @param  int  $timeout
     * @param  int  $sleep
     * @param  int  $maxTries
     * @param  bool  $force
     * @param  string  $verbosity
     */
    public function __construct($environment = null, $delay = 0, $memory = 128, $timeout = 60, $sleep = 3, $maxTries = 0, $force = false, $verbosity = null)
    {
        $this->verbosity = $verbosity;

        parent::__construct($environment, $delay, $memory, $timeout, $sleep, $maxTries, $force);
    }
}
