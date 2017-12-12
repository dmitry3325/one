<?php

namespace App\Providers;

use App\Services\Shop\GoodsStorage;
use Illuminate\Support\ServiceProvider;

/**
 * Class StorageServiceProvider
 * @package App\Providers
 */
class StorageServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GoodsStorage::class, function ($app) {
            return new GoodsStorage();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [GoodsStorage::class];
    }

}