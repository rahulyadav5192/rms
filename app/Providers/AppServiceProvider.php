<?php

namespace App\Providers;

use App\Models\Company;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Modules\Subdomain\Entities\SubdomainSetting;
use function config;
use Illuminate\Pagination\Paginator;
 
 
class AppServiceProvider extends ServiceProvider

{

    /**
     * Register any application services.
     *
     * @return void
     */

    // public function boot()
    // {
        
    // }
    
    
    public function register()
    {
        Cashier::ignoreMigrations();

        if (config('app.redirect_https')) {
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    public function boot()
    {
        
        Paginator::useBootstrap();

        Cashier::useCustomerModel(Company::class);


        if (config('app.redirect_https')) {
            \URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);

        Model::preventLazyLoading(app()->environment('development') && !isRunningInConsoleOrSeeding());

        if (app()->environment('development')) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        CarbonInterval::macro('formatHuman', function ($totalMinutes): string {
            return static::minutes($totalMinutes)->cascade()->forHumans(['short' => true, 'options' => 0]);
        });

    }

}
