<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSchemaController extends Controller
{
    /**
     * Display database schema viewer
     */
    public function index()
    {
        // Get all tables
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map('current', $tables);

        $schema = [];
        $relationships = [];

        foreach ($tableNames as $tableName) {
            // Get columns for each table
            $columns = DB::select("SHOW FULL COLUMNS FROM {$tableName}");

            // Get foreign keys
            $foreignKeys = DB::select("
                SELECT 
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = '{$tableName}'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            $schema[$tableName] = [
                'columns' => $columns,
                'foreign_keys' => $foreignKeys,
                'row_count' => DB::table($tableName)->count(),
            ];

            // Build relationships for diagram
            foreach ($foreignKeys as $fk) {
                $relationships[] = [
                    'from_table' => $tableName,
                    'from_column' => $fk->COLUMN_NAME,
                    'to_table' => $fk->REFERENCED_TABLE_NAME,
                    'to_column' => $fk->REFERENCED_COLUMN_NAME,
                ];
            }
        }

        // Get migration history
        $migrations = DB::table('migrations')->orderBy('batch', 'desc')->get();

        $stats = [
            'total_tables' => count($tableNames),
            'total_migrations' => $migrations->count(),
            'latest_batch' => $migrations->max('batch') ?? 0,
            'total_columns' => collect($schema)->sum(fn ($s) => count($s['columns'])),
            'total_rows' => collect($schema)->sum('row_count'),
            'total_foreign_keys' => collect($schema)->sum(fn ($s) => count($s['foreign_keys'])),
            'total_relationships' => count($relationships),
            'total_indexes' => collect($tableNames)->sum(fn ($t) => count(DB::select("SHOW INDEX FROM `{$t}`"))),
        ];

        return view('admin.database-schema.index', compact('schema', 'relationships', 'tableNames', 'migrations', 'stats'));
    }

    /**
     * Get table details via AJAX
     */
    public function getTableDetails($tableName)
    {
        // SHOW/DESCRIBE statements can't use parameter binding for identifiers,
        // so we whitelist against tables that actually exist before interpolating.
        if (! Schema::hasTable($tableName)) {
            abort(404);
        }

        $columns = DB::select("SHOW FULL COLUMNS FROM `{$tableName}`");

        $foreignKeys = DB::select('
            SELECT
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ', [$tableName]);

        $indexes = DB::select("SHOW INDEX FROM `{$tableName}`");

        $sampleData = DB::table($tableName)->limit(5)->get();

        return response()->json([
            'columns' => $columns,
            'foreign_keys' => $foreignKeys,
            'indexes' => $indexes,
            'sample_data' => $sampleData,
            'row_count' => DB::table($tableName)->count(),
        ]);
    }

    /**
     * Export schema as JSON
     */
    public function exportJson()
    {
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map('current', $tables);

        $schema = [];

        foreach ($tableNames as $tableName) {
            $columns = DB::select("SHOW FULL COLUMNS FROM {$tableName}");

            $foreignKeys = DB::select("
                SELECT 
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = '{$tableName}'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            $schema[$tableName] = [
                'columns' => $columns,
                'foreign_keys' => $foreignKeys,
                'row_count' => DB::table($tableName)->count(),
            ];
        }

        return response()->json($schema, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="database-schema.json"',
        ]);
    }
}
