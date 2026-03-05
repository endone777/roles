<?php

namespace Endone777\Roles;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class RolesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/roles.php' => config_path('roles.php'),
        ], 'roles-config');

        $this->publishes([
            __DIR__ . '/../migrations/' => database_path('migrations'),
        ], 'roles-migrations');

        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        $this->registerBladeExtensions();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/roles.php', 'roles');
    }

    protected function registerBladeExtensions(): void
    {
        Blade::directive('role', function ($expression) {
            return "<?php if (auth()->check() && auth()->user()->hasRole({$expression})): ?>";
        });

        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('permission', function ($expression) {
            return "<?php if (auth()->check() && auth()->user()->hasPermission({$expression})): ?>";
        });

        Blade::directive('endpermission', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('level', function ($expression) {
            $level = trim($expression, '()');
            return "<?php if (auth()->check() && auth()->user()->level() >= {$level}): ?>";
        });

        Blade::directive('endlevel', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('allowed', function ($expression) {
            return "<?php if (auth()->check() && auth()->user()->allowed({$expression})): ?>";
        });

        Blade::directive('endallowed', function () {
            return '<?php endif; ?>';
        });
    }
}
