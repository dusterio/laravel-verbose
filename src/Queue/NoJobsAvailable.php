<?php

namespace Dusterio\LaravelVerbose\Queue;

class NoJobsAvailable
{
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName
     * @return void
     */
    public function __construct($connectionName)
    {
        $this->connectionName = $connectionName;
    }
}
