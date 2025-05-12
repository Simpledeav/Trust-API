<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Str;
use App\Models\UserSettings;
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
        $users = User::whereDoesntHave('wallet')->get();

        foreach ($users as $user) {
            Wallet::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'balance' => 0,
            ]);

            $this->info("Creating wallet for user: {$user->id}");
        }

        $num = User::whereDoesntHave('wallet')->count();

        $this->info('all cleared - (' . $num . ') left');
        //  $backupPath = storage_path('app/backups');
     
        //  if (!file_exists($backupPath)) {
        //      mkdir($backupPath, 0755, true);
        //  }
     
        //  $filename = 'backup-' . now()->format('Y-m-d_H-i-s') . '.sql';
        //  $filePath = $backupPath . DIRECTORY_SEPARATOR . $filename;
     
        //  $db = config('database.connections.mysql');
        //  $command = "mysqldump --no-tablespaces --user=\"{$db['username']}\" --password=\"{$db['password']}\" --host=\"{$db['host']}\" \"{$db['database']}\" > \"{$filePath}\"";
     
        //  exec($command, $output, $result);
     
        //  if ($result === 0 && file_exists($filePath)) {
        //      // Get file size in readable format (e.g., "2.45 MB")
        //      $fileSize = round(filesize($filePath) / 1048576, 2) . ' MB';
     
        //      // Format the date (e.g., "7 April 2025, 3:15 PM")
        //      $backupDate = now()->format('j F Y, g:i A');
     
        //      // Send mail with data
        //      Mail::to(env('BACKUP_MAIL'))->send(
        //          new \App\Mail\DatabaseBackup($filePath, $filename, $fileSize, $backupDate)
        //      );
     
        //      $this->info('Backup created and emailed successfully.');
        //  } else {
        //      $this->error('Database backup failed.');
        //  }
     }
}

