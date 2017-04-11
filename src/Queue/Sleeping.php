<?php

namespace Dusterio\LaravelVerbose\Queue;

class Sleeping
{
    /**
     * @var  int
     */
    public $seconds;

    /**
     * Create a new event instance.
     *
     * @param  int  $seconds
     * @return void
     */
    public function __construct($seconds)
    {
        $this->seconds = $seconds;
    }
}
