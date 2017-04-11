<?php

namespace Dusterio\LaravelVerbose\Integrations;

use Illuminate\Support\ServiceProvider;
use Dusterio\LaravelVerbose\Queue\Worker;
use Dusterio\LaravelVerbose\Queue\WorkCommand;
use Dusterio\LaravelVerbose\Queue\Listener;
use Dusterio\LaravelVerbose\Queue\ListenCommand;
use Illuminate\Contracts\Debug\ExceptionHandler;

/**
 * Class CustomQueueServiceProvider
 * @package App\Providers
 */
class LaravelServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->registerWorker();
        $this->registerListener();
    }

    /**
     * Register the queue worker.
     *
     * @return void
     */
    protected function registerWorker()
    {
        $this->app->extend('queue.worker', function () {
            return new Worker(
                $this->app['queue'], $this->app['events'], $this->app[ExceptionHandler::class]
            );
        });

        $this->registerWorkCommand();
    }

    /**
     * Register the queue worker console command.
     *
     * @return void
     */
    protected function registerWorkCommand()
    {
        $this->app->extend('command.queue.work', function ($old, $app) {
            return new WorkCommand($app['queue.worker']);
        });
    }

    /**
     * Register the queue listener.
     *
     * @return void
     */
    protected function registerListener()
    {
        $this->app->extend('queue.listener', function () {
            return new Listener($this->app->basePath());
        });

        $this->registerListenCommand();
    }

    /**
     * Register the queue listener console command.
     *
     * @return void
     */
    protected function registerListenCommand()
    {
        $this->app->extend('command.queue.listen', function ($old, $app) {
            return new ListenCommand($app['queue.listener']);
        });
    }

    /**
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'queue.worker', 'queue.listener',
            'command.queue.work', 'command.queue.listen'
        ];
    }
}
