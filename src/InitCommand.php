<?php namespace MarkMyers;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
  private $config_file;
  private $config_path;

  public function __construct()
  {
    $this->config_path = getenv('HOME')."/.s3backup";
    $this->config_file = $this->config_path."/config.json";
    parent::__construct();
  }

  public function configure()
  {
    $this->setName('init')
         ->setDescription('Initialize command to create the config file');
  }

  public function execute(InputInterface $input, OutputInterface $output)
  {
    file_exists($this->config_path) || mkdir($this->config_path);
    if (!file_exists($this->config_file)) {
      $h = fopen($this->config_file, 'w');
      fwrite($h, $this->createConfigFile());
       $output->writeln("<info>The configuration file has been created.</info>");
    } else {
       $output->writeln("<info>Your configuration file already exists.</info>");
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
