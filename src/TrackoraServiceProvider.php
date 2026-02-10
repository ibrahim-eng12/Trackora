<?php

namespace IbrahimEng12\Trackora;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use IbrahimEng12\Trackora\Http\Middleware\AuthorizeTrackoraDashboard;
use IbrahimEng12\Trackora\Http\Middleware\TrackVisitor;

class TrackoraServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/trackora.php',
            'trackora'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerMiddleware();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerPublishing();
    }

    /**
     * Register middleware aliases.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('trackora.track', TrackVisitor::class);
        $router->aliasMiddleware('trackora.authorize', AuthorizeTrackoraDashboard::class);
    }

    /**
     * Register routes.
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    /**
     * Register views.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'trackora');
    }

    /**
     * Register publishing.
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__ . '/../config/trackora.php' => config_path('trackora.php'),
            ], 'trackora-config');

            // Publish views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/trackora'),
            ], 'trackora-views');

            // Publish migrations
            $this->publishes([
                __DIR__ . '/../database/migrations/create_trackora_visits_table.php.stub' => database_path('migrations/' . date('Y_m_d_His') . '_create_trackora_visits_table.php'),
            ], 'trackora-migrations');

            // Publish assets (logo, etc.)
            $this->publishes([
                __DIR__ . '/../public' => public_path('vendor/trackora'),
            ], 'trackora-assets');
        }
    }
}
