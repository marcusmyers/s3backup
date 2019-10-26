<?php

namespace App\Support;

use Aws\S3\S3Client;

class BackupToS3
{
    public function __invoke($file)
    {
    	if(file_exists(getenv('HOME')."/.s3backup/config.json")){
		    $config = json_decode(file_get_contents(getenv('HOME')."/.s3backup/config.json"));
	    }

        print sprintf("RUNNING: Uploading  %s to AWS S3' \n", $file);
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'credentials' => [
	            'key'    => $config->aws->credentials->key,
	            'secret' => $config->aws->credentials->secret,
	        ],
        ]);

        $nonPathFile = collect(explode("/", $file))->last();
        $folderKey = collect(explode("_",$nonPathFile))->first();
        try {
            $result = $s3->putObject([
                'Bucket' => $config->aws->bucket,
                'Key'    => $folderKey."/".$nonPathFile,
                'Body'   => fopen($file, 'r')
            ]);
        } catch (S3Exception $e) {
            echo "There was an error uploading the file.\n";
        }

        return $file;
    }
}