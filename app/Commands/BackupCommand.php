<?php

namespace App\Commands;

use App\Support\BackupToS3;
use App\Support\ZipDirectory;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use League\Pipeline\Pipeline;

class BackupCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'backup
                            {folder? : Full path of the directory to backup}
    ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Backup the passed in folder';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(isset($this->config) && !empty($this->config->directories)){
            $directory = $this->config->directories;
        } else {
            $directory = $this->argument('folder') ? $this->argument('folder') : getcwd();
        }

        $pipeline = (new Pipeline)
                    ->pipe(new ZipDirectory)
                    ->pipe(new BackupToS3);
        $pipeline->process($directory);

        $this->notify('Backup', "The $directory was backed up to S3");
    }
}
