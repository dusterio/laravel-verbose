<?php

namespace Dusterio\LaravelVerbose\Queue;

use Exception;
use Throwable;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class Worker53 extends \Illuminate\Queue\Worker
{
    /**
     * Get the next job from the queue connection.
     *
     * @param  \Illuminate\Contracts\Queue\Queue  $connection
     * @param  string  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    protected function getNextJob($connection, $queue)
    {
        try {
            foreach (explode(',', $queue) as $queue) {
                if (! is_null($job = $connection->pop($queue))) {
                    return $job;
                }
            }

            $this->raiseEmptyQueueEvent($connection);
        } catch (Exception $e) {
            $this->raiseQueueExceptionOccurredEvent($connection, $e);
            $this->exceptions->report($e);
        } catch (Throwable $e) {
            $this->exceptions->report(new FatalThrowableError($e));
        }
    }

    /**
     * Raise the before queue job event.
     *
     * @param  string  $connectionName
     * @return void
     */
    protected function raiseEmptyQueueEvent($connectionName)
    {
        $this->events->fire(new NoJobsAvailable(
            $connectionName
        ));
    }

    /**
     * Raise the before queue job event.
     *
     * @param  string  $connectionName
     * @param  \Exception  $e
     * @return void
     */
    protected function raiseQueueExceptionOccurredEvent($connectionName, $e)
    {
        $this->events->fire(new QueueExceptionOccurred(
            $connectionName, $e
        ));
    }

    /**
     * Raise the before queue job event.
     *
     * @param  int  $seconds
     * @return void
     */
    protected function raiseSleepingEvent($seconds)
    {
        $this->events->fire(new Sleeping(
            $seconds
        ));
    }

    /**
     * Sleep the script for a given number of seconds.
     *
     * @param  int   $seconds
     * @return void
     */
    public function sleep($seconds)
    {
        $this->raiseSleepingEvent($seconds);
        sleep($seconds);
    }
}
