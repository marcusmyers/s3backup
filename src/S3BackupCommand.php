<?php namespace MarkMyers;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Alchemy\Zippy\Zippy;
use Comodojo\Zip\Zip;
use Aws\S3\S3Client;
use Aws\Exception\S3Exception;

class S3BackupCommand extends Command
{
  private $config;

  public function configure()
  {
    if(file_exists(getenv('HOME')."/.s3backup/config.json")){
      $configFile = file_get_contents(getenv('HOME')."/.s3backup/config.json");
      $this->config = json_decode($configFile);
    }

    $this->setName('backup')
         ->setDescription('Backup the passed in folder')
         ->addArgument('folder', InputArgument::OPTIONAL, 'Full path of the directory to backup', getenv('HOME'));
  }

  public function execute(InputInterface $input, OutputInterface $output)
  {
    if(isset($this->config) && !empty($this->config->directories)){
      $folder = $this->config->directories;
    } else {
      $folder = $input->getArgument('folder');
    }
    if(is_array($folder)){
      $archName = $this->compress($folder, true);
    } else {
      $archName = $this->compress($folder);
    }

    $this->upload($archName);
    unlink($archName);
  }

  protected function errExit(OutputInterface $output, $errText, $exit_code)
  {
    $output->writeln("<error>$errText</error>");
    exit($exit_code);
  }

  protected function successExit(OutputInterface $output, $successText)
  {
    $output->writeln("<info>$successText</info>");
  }

  /**
   * Upload archive to S3
   *
   * @param string $file
   */
  private function upload($file)
  {
    $folderKey = $this->getDirectoryName($file);
    $s3 = new S3Client([
      'version' => 'latest',
      'region'  => 'us-east-1',
      'credentials' => [
          'key'    => $this->config->aws->credentials->key,
          'secret' => $this->config->aws->credentials->secret,
      ],
    ]);

    try {
      $result = $s3->putObject([
      'Bucket' => $this->getBucketName(),
      'Key'    => $this->config->aws->file_prefix ."_".$folderKey,
      'Body'   => fopen($file, 'r')
      ]);
    } catch (S3Exception $e) {
      echo "There was an error uploading the file.\n";
    }
  }

  private function getBucketName()
  {
    return $this->config->aws->bucket;
  }

  /**
   * Get the directory name from the full path
   *
   * @param string $directory
   * @return string
   */
  private function getDirectoryName($directory)
  {
    $arrExplode = explode("/", $directory);
    $name = array_pop($arrExplode);
    return $name;
  }

  /**
   * Recursively copy folder to tmp directory to prepare for backup
   *
   * @param string $directory
   */
  private function copyForBackup($directory)
  {
    $dirName = $this->getDirectoryName($directory);
    $arrOut = [];
    exec("cp -rf $directory /tmp/$dirName", $arrOut, $exit);
    if($exit) {
      $this->cleanupExit($dirName, $exit[0]);
    }
  }

  /**
   * cleanup the tmp directory exit with status code
   *
   * @param string $directory
   * @param int $exit_code
   */
  private function cleanupExit($directory, $exit_code)
  {
    exec("rm -rf /tmp/$directory");
    exit($exit_code);
  }

  /**
   * Make archive filename
   *
   * @param string $filename
   */
  private function makeFilename($filename)
  {
    if(isset($this->config)){
      $filename = $this->config->aws->file_prefix . "_". $filename;
    }
    return getcwd().'/'.$filename.'-'.date('Y-m-d').'-'.md5(time().uniqid()).'.zip';
  }

  /**
   * Compress folder for backup
   *
   * @param mix $directory
   */
  private function compress($directory, $boolArray = false)
  {
    if($this->config){
      $filename = $this->config->filename;
    } else {
      $filename = ($boolArray ? "archive" : $directory);
    }
    $archiveName = $this->makeFilename($filename);
    $zip = Zip::create($archiveName);
    if($boolArray) {
      foreach($directory as $dir){
        $zip->add($dir);
      }
    } else {
      $zip->add($directory);
    }
    return $archiveName;
  }
}
