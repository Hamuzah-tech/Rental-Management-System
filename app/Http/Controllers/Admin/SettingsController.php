<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        try {
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

        } catch (\Exception $e) {
            Log::error('Failed to load settings page', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->withErrors(['error' => 'Failed to load settings. Please try again.']);
        }
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

            // Get database configuration dynamically
            $connection = config('database.default');
            $database = config("database.connections.{$connection}.database");
            
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

                // Get table data using chunking to avoid memory issues
                $sql .= "-- ---------------------------------------------\n";
                $sql .= "-- Data for `$tableName`\n";
                $sql .= "-- ---------------------------------------------\n";
                
                $rowCount = 0;
                DB::table($tableName)
                    ->orderBy('id')
                    ->chunk(500, function ($chunk) use (&$sql, $tableName, &$rowCount) {
                        foreach ($chunk as $row) {
                            $rowArray = (array)$row;
                            $columns = array_keys($rowArray);
                            
                            // Clean column names
                            $columns = array_map(function($col) {
                                return '`' . $col . '`';
                            }, $columns);
                            
                            // Use PDO quote for safe value escaping
                            $values = array_map(function($value) use ($tableName) {
                                if (is_null($value)) {
                                    return 'NULL';
                                }
                                // Use PDO quote for proper escaping
                                return DB::getPdo()->quote($value);
                            }, $rowArray);
                            
                            $sql .= "INSERT INTO `$tableName` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                            $rowCount++;
                        }
                    });
                
                if ($rowCount > 0) {
                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            $sql .= "COMMIT;\n";

            // Save the backup file
            File::put($fullPath, $sql);

            // Log the backup creation
            Log::info('Database backup created', [
                'admin_id' => auth()->id(),
                'filename' => $filename,
                'size' => File::size($fullPath),
                'tables' => count($tables)
            ]);

            return redirect()
                ->route('admin.settings.index')
                ->with('success', 'Database backup created successfully. File: ' . $filename);

        } catch (\Exception $e) {
            Log::error('Database backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Database backup failed. Please try again.');
        }
    }

    /**
     * Download a backup file.
     */
    public function download($file)
    {
        try {
            // Security: Prevent directory traversal
            if (strpos($file, '..') !== false || strpos($file, '/') !== false || strpos($file, '\\') !== false) {
                Log::warning('Invalid backup file download attempt', [
                    'file' => $file,
                    'admin_id' => auth()->id(),
                    'ip' => request()->ip()
                ]);
                abort(404, 'Invalid file name.');
            }

            // Only allow .sql files
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'sql') {
                Log::warning('Attempted to download non-SQL file', [
                    'file' => $file,
                    'admin_id' => auth()->id()
                ]);
                abort(404, 'Invalid file type.');
            }

            $backupPath = storage_path('app/backups/' . $file);

            if (!File::exists($backupPath)) {
                Log::warning('Backup file not found for download', [
                    'file' => $file,
                    'admin_id' => auth()->id()
                ]);
                abort(404, 'Backup file not found.');
            }

            Log::info('Backup file downloaded', [
                'admin_id' => auth()->id(),
                'filename' => $file,
                'size' => File::size($backupPath)
            ]);

            return response()->download($backupPath, $file, [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="' . $file . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Backup download failed', [
                'file' => $file,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.settings.index')
                ->withErrors(['error' => 'Failed to download backup. Please try again.']);
        }
    }

    /**
     * Delete a backup file.
     */
    public function delete($file)
    {
        try {
            // Security: Prevent directory traversal
            if (strpos($file, '..') !== false || strpos($file, '/') !== false || strpos($file, '\\') !== false) {
                Log::warning('Invalid backup file deletion attempt', [
                    'file' => $file,
                    'admin_id' => auth()->id(),
                    'ip' => request()->ip()
                ]);
                
                return redirect()
                    ->route('admin.settings.index')
                    ->withErrors(['error' => 'Invalid file name.']);
            }

            // Only allow .sql files
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'sql') {
                Log::warning('Attempted to delete non-SQL file', [
                    'file' => $file,
                    'admin_id' => auth()->id()
                ]);
                
                return redirect()
                    ->route('admin.settings.index')
                    ->withErrors(['error' => 'Invalid file type.']);
            }

            $backupPath = storage_path('app/backups/' . $file);

            if (!File::exists($backupPath)) {
                Log::warning('Backup file not found for deletion', [
                    'file' => $file,
                    'admin_id' => auth()->id()
                ]);
                
                return redirect()
                    ->route('admin.settings.index')
                    ->withErrors(['error' => 'Backup file not found.']);
            }

            // Get file size before deletion for logging
            $fileSize = File::size($backupPath);
            
            File::delete($backupPath);

            Log::info('Backup file deleted', [
                'admin_id' => auth()->id(),
                'filename' => $file,
                'size' => $fileSize
            ]);

            return redirect()
                ->route('admin.settings.index')
                ->with('success', 'Backup file deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Backup deletion failed', [
                'file' => $file,
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return redirect()
                ->route('admin.settings.index')
                ->withErrors(['error' => 'Failed to delete backup. Please try again.']);
        }
    }
}