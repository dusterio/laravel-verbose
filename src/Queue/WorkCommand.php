<?php

namespace Dusterio\LaravelVerbose\Queue;

use Carbon\Carbon;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Symfony\Component\Console\Output\OutputInterface;

class WorkCommand extends \Illuminate\Queue\Console\WorkCommand
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if ($this->downForMaintenance() && $this->option('once')) {
            return $this->worker->sleep($this->option('sleep'));
        }

        // We'll listen to the processed and failed events so we can write information
        // to the console as jobs are processed, which will let the developer watch
        // which jobs are coming through a queue and be informed on its progress.
        $this->listenForEvents();

        $connection = $this->argument('connection')
            ?: $this->laravel['config']['queue.default'];

        // We need to get the right queue for the connection which is set in the queue
        // configuration file for the application. We will pull it based on the set
        // connection being run for the queue operation currently being executed.
        $queue = $this->getQueue($connection);

        $this->output->writeln("Using connection: {$connection}", OutputInterface::VERBOSITY_VERBOSE);
        $this->output->writeln("Using queue: {$queue}", OutputInterface::VERBOSITY_VERBOSE);

        $this->runWorker(
            $connection, $queue
        );
    }

    /**
     * Listen for the queue events in order to update the console output.
     *
     * @return void
     */
    protected function listenForEvents()
    {
        $this->laravel['events']->listen(NoJobsAvailable::class, function () {
            $this->output->writeln('The queue seems to be empty.', OutputInterface::VERBOSITY_VERBOSE);
        });

        $this->laravel['events']->listen(JobProcessing::class, function ($event) {
            $this->output->writeln('Popped a job from the queue: ' . $event->job->resolveName(), OutputInterface::VERBOSITY_VERY_VERBOSE);
        });

        $this->laravel['events']->listen(JobProcessed::class, function ($event) {
            $this->output->writeln('<info>[' . Carbon::now()->format('Y-m-d H:i:s') . '] Processed:</info> ' . $event->job->resolveName());
        });

        $this->laravel['events']->listen(JobFailed::class, function ($event) {
            $this->output->writeln('<error>[' . Carbon::now()->format('Y-m-d H:i:s') . '] Failed:</error> ' . $event->job->resolveName());

            $this->logFailedJob($event);
        });

        $this->laravel['events']->listen(Sleeping::class, function ($event) {
            $this->output->writeln("Sleeping for {$event->seconds} seconds.", OutputInterface::VERBOSITY_VERY_VERBOSE);
        });

        $this->laravel['events']->listen(QueueExceptionOccurred::class, function ($event) {
            $this->output->writeln($event->getMessage(), OutputInterface::VERBOSITY_VERY_VERBOSE);
            $this->output->writeln("Couldn't fetch a job from the queue. See the log file for more information.", OutputInterface::VERBOSITY_VERBOSE);
        });
    }
}

