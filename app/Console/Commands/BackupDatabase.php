<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup-email';

    /**
     * The console command description.
     *
     * @var string
     */
    
    protected $description = 'Backup the DB and send as email attachment';

    /**
     * Execute the console command.
     */
    
    public function handle()
    {
        $backupPath = storage_path('app/backups');
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $filename = 'backup-' . now()->format('Y-m-d_H-i-s') . '.sql';
        $filePath = storage_path("app/backups/{$filename}");

        $db = config('database.connections.mysql');
        // $command = "mysqldump --user=\"{$db['username']}\" --password=\"{$db['password']}\" --host=\"{$db['host']}\" \"{$db['database']}\" > \"{$filePath}\"";
        $command = "mysqldump --no-tablespaces --user=\"{$db['username']}\" --password=\"{$db['password']}\" --host=\"{$db['host']}\" \"{$db['database']}\" > \"{$filePath}\"";


        $result = null;
        $output = null;

        exec($command, $output, $result);

        if ($result === 0) {
            Mail::to('sandaiv001@gmail.com')->send(new \App\Mail\DatabaseBackup($filePath, $filename));
            $this->info('Backup created and emailed successfully.');
        } else {
            $this->error('Database backup failed.');
        }
    }
}
