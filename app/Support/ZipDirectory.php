<?php

namespace App\Support;

use ZanySoft\Zip\Zip;

class ZipDirectory {
	public function __invoke($directory)
	{
		if (file_exists(getenv('HOME')."/.s3backup/config.json")){
		    $config = json_decode(file_get_contents(getenv('HOME')."/.s3backup/config.json"));
	    }

		print sprintf("RUNNING: Compressing %s' \n", $directory);
		
		if (isset($config)) {
			$filename = $config->aws->file_prefix . "_". $config->filename;
		} else {
			$filename = collect(explode("/", $directory))->last();
		}

		$zipName = "/tmp/" . $filename . "_" . date('Y-m-d') .'-'.md5(time().uniqid()). ".zip";
		$zip = Zip::create($zipName);
		if (is_array($directory)) {
			foreach ($directory as $dir) {
				$zip->add($dir);
			}
		} else {
			$zip->add($directory);
		}
		return $zip->getPath() . "/" . $zip->getZipFile();
	}
}