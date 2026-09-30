<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class ExportSystemStructure extends Command
{
    protected $signature = 'system:export';

    protected $description = 'Export database schema, routes, controllers, models, and views for analysis';

    public function handle()
    {
        $output = [];

        // 1. DATABASE TABLES (schema)
        $output[] = '=== DATABASE TABLES ===';
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableKey = "Tables_in_{$databaseName}";

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            $columns = DB::select("DESCRIBE {$tableName}");
            $output[] = "\nTable: {$tableName}";
            foreach ($columns as $col) {
                $output[] = "  - {$col->Field} ({$col->Type}) Null:{$col->Null} Key:{$col->Key}";
            }
        }

        // 2. ROUTES
        $output[] = "\n\n=== ROUTES ===";
        $routes = Route::getRoutes();
        foreach ($routes as $route) {
            $methods = implode('|', $route->methods());
            $uri = $route->uri();
            $action = $route->getActionName();
            $middleware = implode(', ', $route->middleware());
            $output[] = "{$methods} {$uri} → {$action} [Middleware: {$middleware}]";
        }

        // 3. CONTROLLERS
        $output[] = "\n\n=== CONTROLLERS ===";
        $controllerPath = app_path('Http/Controllers');
        if (File::exists($controllerPath)) {
            $controllers = File::allFiles($controllerPath);
            foreach ($controllers as $controller) {
                $relativePath = $controller->getRelativePathname();
                $output[] = "- {$relativePath}";
            }
        } else {
            $output[] = 'No controllers found.';
        }

        // 4. MODELS
        $output[] = "\n\n=== MODELS ===";
        $modelPath = app_path('Models');
        if (File::exists($modelPath)) {
            $models = File::allFiles($modelPath);
            foreach ($models as $model) {
                $relativePath = $model->getRelativePathname();
                $output[] = "- {$relativePath}";
            }
        } else {
            $output[] = 'No Models folder found (check app/ directory for model files).';
        }

        // Also check app/ directly for models (Laravel <8 sometimes puts models in app/)
        $appModels = File::files(app_path());
        foreach ($appModels as $file) {
            $filename = $file->getFilename();
            if (str_ends_with($filename, '.php') && ! str_contains($filename, 'Controller')) {
                $output[] = "- (app/) {$filename}";
            }
        }

        // 5. VIEWS (Blade files)
        $output[] = "\n\n=== VIEWS ===";
        $viewPath = resource_path('views');
        if (File::exists($viewPath)) {
            $views = File::allFiles($viewPath);
            foreach ($views as $view) {
                $relativePath = $view->getRelativePathname();
                $output[] = "- {$relativePath}";
            }
        } else {
            $output[] = 'No views found.';
        }

        // 6. MIGRATIONS (list only)
        $output[] = "\n\n=== MIGRATIONS ===";
        $migrationPath = database_path('migrations');
        if (File::exists($migrationPath)) {
            $migrations = File::files($migrationPath);
            foreach ($migrations as $migration) {
                $output[] = "- {$migration->getFilename()}";
            }
        } else {
            $output[] = 'No migrations found.';
        }

        // 7. SEEDERS
        $output[] = "\n\n=== SEEDERS ===";
        $seederPath = database_path('seeders');
        if (File::exists($seederPath)) {
            $seeders = File::files($seederPath);
            foreach ($seeders as $seeder) {
                $output[] = "- {$seeder->getFilename()}";
            }
        } else {
            $output[] = 'No seeders found.';
        }

        // 8. ENVIRONMENT INFO
        $output[] = "\n\n=== ENVIRONMENT ===";
        $output[] = 'Laravel Version: '.app()->version();
        $output[] = 'PHP Version: '.phpversion();
        $output[] = 'DB Connection: '.config('database.default');
        $output[] = 'App Environment: '.app()->environment();

        // Output to console and also save to file
        $report = implode("\n", $output);
        $reportFile = storage_path('system_export_'.date('Y-m-d_His').'.txt');
        File::put($reportFile, $report);

        $this->info("System structure exported to: {$reportFile}");
        $this->info('Total lines: '.count($output));

        // Also display first 200 lines in console
        $this->line("\n".substr($report, 0, 10000));
    }
}
