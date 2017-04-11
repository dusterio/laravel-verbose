<?php

namespace Dusterio\LaravelVerbose\Integrations;

use Illuminate\Support\ServiceProvider;
use Dusterio\LaravelVerbose\Queue\Worker;
use Dusterio\LaravelVerbose\Queue\WorkCommand;
use Dusterio\LaravelVerbose\Queue\Listener;
use Dusterio\LaravelVerbose\Queue\ListenCommand;

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
        $this->registerWorkCommand();

        $this->app->singleton('queue.worker', function ($app) {
            return new Worker(
                $app['queue'], $app['events'],
                $app['Illuminate\Contracts\Debug\ExceptionHandler']
            );
        });
    }

    /**
     * Register the queue worker console command.
     *
     * @return void
     */
    protected function registerWorkCommand()
    {
        $this->app->singleton('command.queue.work', function ($app) {
            return new WorkCommand($app['queue.worker']);
        });

        $this->commands('command.queue.work');
    }

    /**
     * Register the queue listener.
     *
     * @return void
     */
    protected function registerListener()
    {
        $this->registerListenCommand();

        $this->app->singleton('queue.listener', function ($app) {
            return new Listener($app->basePath());
        });
    }

    /**
     * Register the queue listener console command.
     *
     * @return void
     */
    protected function registerListenCommand()
    {
        $this->app->singleton('command.queue.listen', function ($app) {
            return new ListenCommand($app['queue.listener']);
        });

        $this->commands('command.queue.listen');
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
