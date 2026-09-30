<?php

namespace App\Providers;

use App\Models\Policy;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Schema::defaultStringLength(191);
    }

    public function boot(): void
    {
        // `/policies/{policy}` (and its rules, versions, approval...) only
        // resolves for a policy this user's university may see - its own or a
        // platform default. Without this, any policies-permission holder could
        // open another university's policy just by changing the id in the URL.
        Route::bind('policy', function (string $value) {
            return Policy::visibleTo(auth()->user()?->university_id)->findOrFail($value);
        });

        // Register Spatie Permission Blade directives manually
        Blade::directive('role', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$expression})): ?>";
        });

        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('hasanyrole', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasAnyRole({$expression})): ?>";
        });

        Blade::directive('endhasanyrole', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('hasrole', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$expression})): ?>";
        });

        Blade::directive('endhasrole', function () {
            return '<?php endif; ?>';
        });
    }
}
