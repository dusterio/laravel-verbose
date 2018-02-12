<?php

namespace Dusterio\LaravelVerbose\Integrations;

use Illuminate\Support\ServiceProvider;
use Dusterio\LaravelVerbose\Queue\Worker53;
use Dusterio\LaravelVerbose\Queue\Worker54;
use Dusterio\LaravelVerbose\Queue\WorkCommand;
use Dusterio\LaravelVerbose\Queue\Listener53;
use Dusterio\LaravelVerbose\Queue\Listener54;
use Dusterio\LaravelVerbose\Queue\ListenCommand;
use Illuminate\Contracts\Debug\ExceptionHandler;

/**
 * Class CustomQueueServiceProvider
 * @package App\Providers
 */
class LaravelServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $implementations = [
        '5\.3\.\d+' => [
            'worker' => Worker53::class,
            'listener' => Listener53::class
        ],
        '5\.4\.\d+|5\.5\.\d+' => [
            'worker' => Worker54::class,
            'listener' => Listener54::class
        ]
    ];

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
        $class = $this->findWorkerClass($this->app->version());

        $this->app->extend('queue.worker', function () use ($class) {
            return new $class(
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
        $class = $this->findListenerClass($this->app->version());

        $this->app->extend('queue.listener', function () use ($class) {
            return new $class($this->app->basePath());
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

    /**
     * @param $version
     * @return mixed
     */
    protected function findWorkerClass($version)
    {
        foreach ($this->implementations as $regexp => $config) {
            if (preg_match('/' . $regexp . '/', $version)) return $config['worker'];
        }
    }

    /**
     * @param $version
     * @return mixed
     */
    protected function findListenerClass($version)
    {
        foreach ($this->implementations as $regexp => $config) {
            if (preg_match('/' . $regexp . '/', $version)) return $config['listener'];
        }
    }
}
