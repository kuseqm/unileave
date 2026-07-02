<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DevController extends Controller
{
    public function exportState()
    {
        $state = [
            'exported_at' => now()->toDateTimeString(),
            'laravel_version' => app()->version(),
            'php_version' => phpversion(),
            
            // 1. COMPLETED FEATURES
            'completed_features' => $this->getCompletedFeatures(),
            
            // 2. PROJECT STRUCTURE
            'project_structure' => $this->getProjectStructure(),
            
            // 3. DATABASE SCHEMA
            'database_schema' => $this->getDatabaseSchema(),
            
            // 4. MODELS
            'models' => $this->getModels(),
            
            // 5. CONTROLLERS
            'controllers' => $this->getControllers(),
            
            // 6. ROUTES
            'routes' => $this->getRoutes(),
            
            // 7. VIEWS
            'views' => $this->getViews(),
            
            // 8. SAMPLE DATA COUNTS
            'data_counts' => $this->getDataCounts(),
            
            // 9. RECENT SAMPLE ENTRIES
            'sample_data' => $this->getSampleData(),
            
            // 10. NEXT STEPS / TODO
            'pending_tasks' => $this->getPendingTasks(),
            
            // 11. ERRORS/CONCERNS
            'potential_issues' => $this->getPotentialIssues(),
        ];
        
        // Return as JSON for easy copying
        return response()->json($state, 200, [
            'Content-Type' => 'application/json',
            'X-Info' => 'Copy this entire response to share your project state'
        ])->setEncodingOptions(JSON_PRETTY_PRINT);
    }
    
    private function getCompletedFeatures()
    {
        $features = [];
        
        // Check Schools module
        if (File::exists(app_path('Http/Controllers/Admin/SchoolController.php'))) {
            $controller = file_get_contents(app_path('Http/Controllers/Admin/SchoolController.php'));
            $features['schools'] = [
                'index' => str_contains($controller, 'index'),
                'create' => str_contains($controller, 'create'),
                'store' => str_contains($controller, 'store'),
                'edit' => str_contains($controller, 'edit'),
                'update' => str_contains($controller, 'update'),
                'destroy' => str_contains($controller, 'destroy'),
            ];
        }
        
        // Check Views
        $features['views'] = [
            'schools_index' => File::exists(resource_path('views/admin/schools/index.blade.php')),
            'schools_create' => File::exists(resource_path('views/admin/schools/create.blade.php')),
            'schools_edit' => File::exists(resource_path('views/admin/schools/edit.blade.php')),
        ];
        
        return $features;
    }
    
    private function getProjectStructure()
    {
        $directories = [
            'app/Http/Controllers' => $this->listPhpFiles(app_path('Http/Controllers')),
            'app/Models' => $this->listPhpFiles(app_path('Models')),
            'resources/views/admin' => $this->listBladeFiles(resource_path('views/admin')),
            'database/migrations' => $this->listMigrationFiles(database_path('migrations')),
        ];
        
        return $directories;
    }
    
    private function getDatabaseSchema()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $schema = [];
            
            foreach ($tables as $table) {
                $tableName = reset($table);
                $columns = Schema::getColumnListing($tableName);
                $schema[$tableName] = [
                    'columns' => $columns,
                    'count' => DB::table($tableName)->count(),
                ];
            }
            
            return $schema;
        } catch (\Exception $e) {
            return ['error' => 'Could not fetch schema: ' . $e->getMessage()];
        }
    }
    
    private function getModels()
    {
        $modelsPath = app_path('Models');
        if (!File::exists($modelsPath)) {
            return [];
        }
        
        $models = [];
        foreach (File::files($modelsPath) as $file) {
            $modelName = $file->getFilenameWithoutExtension();
            $content = file_get_contents($file->getPathname());
            
            $models[$modelName] = [
                'fillable' => $this->extractFillable($content),
                'relationships' => $this->extractRelationships($content),
            ];
        }
        
        return $models;
    }
    
    private function getControllers()
    {
        $controllersPath = app_path('Http/Controllers');
        if (!File::exists($controllersPath)) {
            return [];
        }
        
        $controllers = [];
        $adminPath = $controllersPath . '/Admin';
        
        if (File::exists($adminPath)) {
            foreach (File::files($adminPath) as $file) {
                $controllers['Admin/' . $file->getFilenameWithoutExtension()] = [
                    'methods' => $this->extractMethods(file_get_contents($file->getPathname())),
                ];
            }
        }
        
        return $controllers;
    }
    
    private function getRoutes()
    {
        $routesFile = base_path('routes/web.php');
        if (!File::exists($routesFile)) {
            return ['error' => 'Routes file not found'];
        }
        
        $content = file_get_contents($routesFile);
        preg_match_all("/Route::(get|post|put|patch|delete|resource)\(['\"]([^'\"]+)['\"]/", $content, $matches);
        
        $routes = [];
        foreach ($matches[2] as $index => $route) {
            $routes[] = [
                'method' => $matches[1][$index] ?? 'unknown',
                'uri' => $route,
            ];
        }
        
        return $routes;
    }
    
    private function getViews()
    {
        $viewsPath = resource_path('views/admin');
        if (!File::exists($viewsPath)) {
            return [];
        }
        
        $views = [];
        foreach (File::allFiles($viewsPath) as $file) {
            $relativePath = $file->getRelativePathname();
            $views[$relativePath] = [
                'size' => $file->getSize(),
                'last_modified' => date('Y-m-d H:i:s', $file->getMTime()),
            ];
        }
        
        return $views;
    }
    
    private function getDataCounts()
    {
        try {
            $tables = ['schools', 'campuses', 'departments', 'users', 'faculty_members'];
            $counts = [];
            
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    $counts[$table] = DB::table($table)->count();
                }
            }
            
            return $counts;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    private function getSampleData()
    {
        try {
            $samples = [];
            
            if (Schema::hasTable('schools')) {
                $samples['schools'] = DB::table('schools')->latest()->take(3)->get();
            }
            
            if (Schema::hasTable('campuses')) {
                $samples['campuses'] = DB::table('campuses')->latest()->take(3)->get();
            }
            
            return $samples;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    private function getPendingTasks()
    {
        // You can manually update this list based on what you know needs to be done
        return [
            'Complete School/Faculty CRUD operations',
            'Add validation rules for School form',
            'Create Department management module',
            'Set up proper authentication/authorization',
            'Add search/filter functionality to schools index',
            'Create API endpoints if needed',
            'Write tests for School module',
            // Add more as you progress
        ];
    }
    
    private function getPotentialIssues()
    {
        $issues = [];
        
        // Check for missing relationships
        $schoolModel = app_path('Models/School.php');
        if (File::exists($schoolModel)) {
            $content = file_get_contents($schoolModel);
            if (!str_contains($content, 'campus()')) {
                $issues[] = 'School model missing campus relationship';
            }
            if (!str_contains($content, 'dean()')) {
                $issues[] = 'School model missing dean relationship';
            }
        }
        
        return $issues;
    }
    
    // Helper methods
    private function listPhpFiles($path)
    {
        if (!File::exists($path)) return [];
        return array_map(fn($f) => $f->getFilename(), File::files($path));
    }
    
    private function listBladeFiles($path)
    {
        if (!File::exists($path)) return [];
        return array_map(fn($f) => $f->getRelativePathname(), File::allFiles($path));
    }
    
    private function listMigrationFiles($path)
    {
        if (!File::exists($path)) return [];
        return array_map(fn($f) => $f->getFilename(), File::files($path));
    }
    
    private function extractFillable($content)
    {
        preg_match('/protected \$fillable = \[(.*?)\]/s', $content, $matches);
        if (isset($matches[1])) {
            preg_match_all("/'([^']+)'/", $matches[1], $fillable);
            return $fillable[1] ?? [];
        }
        return [];
    }
    
    private function extractRelationships($content)
    {
        $relationships = [];
        $patterns = [
            'belongsTo' => '/function\s+(\w+)\s*\(\)\s*{\s*return\s+\$this->belongsTo\(/',
            'hasMany' => '/function\s+(\w+)\s*\(\)\s*{\s*return\s+\$this->hasMany\(/',
            'belongsToMany' => '/function\s+(\w+)\s*\(\)\s*{\s*return\s+\$this->belongsToMany\(/',
        ];
        
        foreach ($patterns as $type => $pattern) {
            preg_match_all($pattern, $content, $matches);
            foreach ($matches[1] ?? [] as $match) {
                $relationships[] = "$type: $match";
            }
        }
        
        return $relationships;
    }
    
    private function extractMethods($content)
    {
        preg_match_all('/function\s+(\w+)\s*\([^)]*\)/', $content, $matches);
        return $matches[1] ?? [];
    }
}