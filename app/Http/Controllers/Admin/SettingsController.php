<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $backupPath = storage_path('app/backups');

        // Create folder if it doesn't exist
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $files = collect(File::files($backupPath))
            ->sortByDesc(function ($file) {
                return $file->getMTime();
            });

        return view('admin.settings.index', compact('files'));
    }

    /**
     * Create a database backup using Laravel's DB facade.
     */
    public function backup()
    {
        try {
            $backupPath = storage_path('app/backups');

            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $filename = 'backup_' . now()->format('Y_m_d_His') . '.sql';
            $fullPath = $backupPath . DIRECTORY_SEPARATOR . $filename;

            // Get database configuration
            $database = config('database.connections.mysql.database');
            
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $database;
            
            $sql = "-- =============================================\n";
            $sql .= "-- Database Backup\n";
            $sql .= "-- Database: " . $database . "\n";
            $sql .= "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n";
            $sql .= "-- =============================================\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $sql .= "SET AUTOCOMMIT=0;\n";
            $sql .= "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n\n";

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Get create table syntax
                $createTable = DB::select("SHOW CREATE TABLE `$tableName`");
                $sql .= "-- ---------------------------------------------\n";
                $sql .= "-- Table structure for `$tableName`\n";
                $sql .= "-- ---------------------------------------------\n";
                $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

                // Get table data
                $rows = DB::table($tableName)->get();
                
                if ($rows->count() > 0) {
                    $sql .= "-- ---------------------------------------------\n";
                    $sql .= "-- Data for `$tableName`\n";
                    $sql .= "-- ---------------------------------------------\n";
                    
                    foreach ($rows->chunk(100) as $chunk) {
                        foreach ($chunk as $row) {
                            $rowArray = (array)$row;
                            $columns = array_keys($rowArray);
                            
                            // Clean column names (remove table prefix if any)
                            $columns = array_map(function($col) {
                                return '`' . str_replace($col, $col, $col) . '`';
                            }, $columns);
                            
                            $values = array_map(function($value) {
                                if (is_null($value)) {
                                    return 'NULL';
                                }
                                return "'" . addslashes($value) . "'";
                            }, $rowArray);
                            
                            $sql .= "INSERT INTO `$tableName` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                        }
                    }
                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            $sql .= "COMMIT;\n";

            // Save the backup file
            File::put($fullPath, $sql);

            return redirect()
                ->route('admin.settings.index')
                ->with('success', 'Database backup created successfully. File: ' . $filename);

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file.
     */
    public function download($file)
    {
        $backupPath = storage_path('app/backups/' . $file);

        // Security: Prevent directory traversal
        if (strpos($file, '..') !== false || strpos($file, '/') !== false || strpos($file, '\\') !== false) {
            abort(404, 'Invalid file name.');
        }

        if (!File::exists($backupPath)) {
            abort(404, 'Backup file not found.');
        }

        return response()->download($backupPath, $file, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $file . '"',
        ]);
    }

    /**
     * Delete a backup file.
     */
    public function delete($file)
    {
        $backupPath = storage_path('app/backups/' . $file);

        // Security: Prevent directory traversal
        if (strpos($file, '..') !== false || strpos($file, '/') !== false || strpos($file, '\\') !== false) {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Invalid file name.');
        }

        if (!File::exists($backupPath)) {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Backup file not found.');
        }

        File::delete($backupPath);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Backup file deleted successfully.');
    }
}