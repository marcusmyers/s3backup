<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class InitCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Initialize command to create the config file';

    private $config_file;
    private $config_path;

    public function __construct()
    {
        if ( env('S3CONFIG') ){
            $this->config_path = env('S3CONFIG')."/.s3backup";
        } else {
            $this->config_path = env('HOME')."/.s3backup";    
        }
        
        $this->config_file = $this->config_path."/config.json";
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        file_exists($this->config_path) || mkdir($this->config_path);

        if (!file_exists($this->config_file)) {
          $h = fopen($this->config_file, 'w');
          fwrite($h, $this->createConfigFile());
           $this->info("The configuration file has been created.");
        } else {
           $this->info("Your configuration file already exists.");
        }
    }

    private function createConfigFile()
    {
        $config = [
          "aws" => [
            "credentials" => [
              "key" => "",
              "secret" => ""
            ],
            "bucket" => "my-backup-bucket",
            "file_prefix" => "backup"
          ],
          "filename" => "name_for_backup_file",
          "directories" => []
        ];
        return json_encode($config, JSON_PRETTY_PRINT);
    }
}
