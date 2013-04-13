<?php

	//Run this file in a CRON tab to do clean up of sessions, you NEED to update folder where your sessions will be created !

	function deleteDir($dirPath) {
		if(!is_dir($dirPath)) {
			print("    This is not a directory !\n");
			return false;
		}
		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			$dirPath .= '/';
		}
		$files = glob($dirPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}

	$dir = '/hugedisk/Tanayk/temp/yttt';
	$files = scandir($dir, 1);
	$time = date("d-m-Y H:i:s");
	print("\n\n\n***** $time => PERFORMING CLEANUP !!! *****\n");
	foreach ($files as $key) {
		if(($key != ".") && ($key !="..")){
			if(is_dir($dir."/".$key)){
				$pplHere = file_get_contents($dir."/".$key."/peopleHere");
				print("Session[$key]: ".$pplHere);
				if($pplHere <= 0){
					print(" - Deleting this session...!");
					deleteDir($dir."/".$key);
				}
				print("\n");
			}
		}
	}
?>