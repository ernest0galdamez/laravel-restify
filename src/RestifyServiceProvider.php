<?php

namespace Binaryk\LaravelRestify;

use Binaryk\LaravelRestify\Http\Controllers\RepositoryIndexController;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * This provider is injected in console context by the main provider or by the RestifyInjector
 * if a restify request.
 */
class RestifyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerExceptionHandler();
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        $config = [
            'namespace' => 'Binaryk\LaravelRestify\Http\Controllers',
            'as' => 'restify.api.',
            'prefix' => Restify::path(),
            'middleware' => config('restify.middleware', []),
        ];

        $this->defaultRoutes($config)
            ->registerPrefixed($config)
            ->registerIndexPrefixed($config);
    }

    /**
     * @param $config
     * @return RestifyServiceProvider
     */
    public function defaultRoutes($config)
    {
        Route::group($config, function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });

        return $this;
    }

    /**
     * @param $config
     * @return $this
     */
    public function registerPrefixed($config)
    {
        collect(Restify::$repositories)
            ->filter(fn ($repository) => $repository::prefix())
            ->each(function ($repository) use ($config) {
                $config['prefix'] = $repository::prefix();
                Route::group($config, function () {
                    $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
                });
            });

        return $this;
    }

    public function registerIndexPrefixed($config)
    {
        collect(Restify::$repositories)
            ->filter(fn ($repository) => $repository::indexPrefix())
            ->each(function ($repository) use ($config) {
                $config['prefix'] = $repository::indexPrefix();
                Route::group($config, function () {
                    Route::get('/{repository}', '\\'.RepositoryIndexController::class);
                });
            });

        return $this;
    }

    /**
     * Register Restify's custom exception handler.
     *
     * @return void
     */
    protected function registerExceptionHandler()
    {
        if (config('restify.exception_handler') && class_exists(value(config('restify.exception_handler')))) {
            $this->app->bind(ExceptionHandler::class, value(config('restify.exception_handler')));
        }
    }
}
