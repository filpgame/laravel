<?php

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SlackHandler;
use Way\Generators\GeneratorsServiceProvider;
use Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider;
use Barryvdh\Debugbar\ServiceProvider as DebugbarServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment('local')) {
            if (env('APP_DEBUG', false)) {
                $this->app->register(IdeHelperServiceProvider::class);
                $this->app->register(GeneratorsServiceProvider::class);
                $this->app->register(MigrationsGeneratorServiceProvider::class);
                $this->app->register(DebugbarServiceProvider::class);
                \Cache::flush();
            }
        }
        /**
         * Define o Handler para registrar log do sistema para o Slack.
         */
        if (env('SLACK_LOG', false)) {
            $monolog = \Log::getMonolog();
            $slack = new SlackHandler($this->app['config']['services.slack.channel'], true, null, $this->app['config']['services.slack.level'], true, false, true);
            $slack->setFormatter(new LineFormatter());
            $monolog->pushHandler($slack);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
