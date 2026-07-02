<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Schema::defaultStringLength(191);
    }

    public function boot(): void
    {
        // Register Spatie Permission Blade directives manually
        Blade::directive('role', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$expression})): ?>";
        });
        
        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });
        
        Blade::directive('hasanyrole', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasAnyRole({$expression})): ?>";
        });
        
        Blade::directive('endhasanyrole', function () {
            return "<?php endif; ?>";
        });
        
        Blade::directive('hasrole', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$expression})): ?>";
        });
        
        Blade::directive('endhasrole', function () {
            return "<?php endif; ?>";
        });
    }
}