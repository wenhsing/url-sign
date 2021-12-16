<?php

namespace Wenhsing\UrlSign\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use Wenhsing\UrlSign\UrlSignManager;

class UrlSignProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'url-sign');

        $this->app->singleton(UrlSignManager::class, function ($app) {
            return new UrlSignManager($this->getConfig());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([$this->getConfigPath() => config_path('url-sign.php')], 'url-sign');
    }

    /**
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__.'/../../../config/url-sign.php';
    }

    protected function getConfig()
    {
        return $this->app['config']->get('url-sign');
    }
}
