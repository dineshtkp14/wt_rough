<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\ExecutableFinder;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup
        {--path= : Custom backup directory}
        {--mysqldump= : Full path to mysqldump executable}
        {--keep=0 : Delete old backups and keep this many latest files. 0 keeps all backups}';

    protected $description = 'Create a MySQL database backup file';

    public function handle(): int
    {
        $connection = config('database.default');
        $databaseConfig = config("database.connections.{$connection}");

        if (($databaseConfig['driver'] ?? null) !== 'mysql') {
            $this->error('Only MySQL backups are supported by this command.');

            return self::FAILURE;
        }

        $database = $databaseConfig['database'] ?? null;

        if (!$database) {
            $this->error('Database name is missing.');

            return self::FAILURE;
        }

        $backupDirectory = $this->option('path') ?: storage_path('app/backups');
        File::ensureDirectoryExists($backupDirectory);

        $filename = sprintf(
            '%s_%s.sql',
            preg_replace('/[^A-Za-z0-9_-]+/', '_', $database),
            now()->format('Y_m_d_H_i_s')
        );

        $backupPath = $backupDirectory . DIRECTORY_SEPARATOR . $filename;
        $mysqldump = $this->findMysqldump();

        if (!$mysqldump) {
            $this->error('mysqldump was not found. Add it to PATH or pass --mysqldump="C:\path\to\mysqldump.exe".');

            return self::FAILURE;
        }

        $command = [
            $mysqldump,
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--events',
            '--default-character-set=utf8mb4',
            '--host=' . ($databaseConfig['host'] ?? '127.0.0.1'),
            '--port=' . ($databaseConfig['port'] ?? '3306'),
            '--user=' . ($databaseConfig['username'] ?? ''),
        ];

        if (!empty($databaseConfig['password'])) {
            $command[] = '--password=' . $databaseConfig['password'];
        }

        if (!empty($databaseConfig['unix_socket'])) {
            $command[] = '--socket=' . $databaseConfig['unix_socket'];
        }

        $command[] = $database;

        $this->info('Creating database backup...');

        $exitCode = $this->runBackupProcess($command, $backupPath);

        if ($exitCode !== 0) {
            if (File::exists($backupPath) && File::size($backupPath) === 0) {
                File::delete($backupPath);
            }

            return self::FAILURE;
        }

        $this->info('Backup created successfully: ' . $backupPath);
        $this->info('Size: ' . $this->formatBytes(File::size($backupPath)));

        $this->deleteOldBackups($backupDirectory, (int) $this->option('keep'));

        return self::SUCCESS;
    }

    private function findMysqldump(): ?string
    {
        if ($this->option('mysqldump')) {
            return $this->option('mysqldump');
        }

        if (env('MYSQLDUMP_PATH')) {
            return env('MYSQLDUMP_PATH');
        }

        $found = (new ExecutableFinder())->find('mysqldump');

        if ($found) {
            return $found;
        }

        $commonPaths = [
            'C:\xampp\mysql\bin\mysqldump.exe',
            'C:\wamp64\bin\mysql\mysql8.0.31\bin\mysqldump.exe',
            'C:\laragon\bin\mysql\mysql-8.0\bin\mysqldump.exe',
        ];

        foreach ($commonPaths as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function runBackupProcess(array $command, string $backupPath): int
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['file', $backupPath, 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes, base_path());

        if (!is_resource($process)) {
            $this->error('Could not start mysqldump process.');

            return 1;
        }

        fclose($pipes[0]);
        $errorOutput = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            $this->error('Backup failed.');

            if ($errorOutput) {
                $this->line(trim($errorOutput));
            }
        }

        return $exitCode;
    }

    private function deleteOldBackups(string $backupDirectory, int $keep): void
    {
        if ($keep <= 0) {
            return;
        }

        $backups = collect(File::files($backupDirectory))
            ->filter(fn ($file) => str_ends_with($file->getFilename(), '.sql'))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values();

        $backups->slice($keep)->each(fn ($file) => File::delete($file->getPathname()));
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / 1024 / 1024, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }
}
