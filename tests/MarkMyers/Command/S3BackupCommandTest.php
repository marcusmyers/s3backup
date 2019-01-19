<?php namespace Tests\MarkMyers\Command;

use MarkMyers\S3BackupCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Application;

class S3BackupCommandTest extends \PHPUnit\Framework\TestCase
{
  public function testExecute()
  {
    $app = new Application();
    $app->add(new S3BackupCommand());

    $command = $app->find('backup');
    $commandTester = new CommandTester($command);
    $commandTester->execute(array(
      'command'  => $command->getName(),
      'folder' => '/Users/mmyers/tests',
    ));
  }
}
