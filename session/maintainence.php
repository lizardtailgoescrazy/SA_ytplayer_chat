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
	$scriptName = "maintainence.php";
	$scriptPath = __FILE__;
	$scriptPath = substr($scriptPath, 0, strlen($scriptPath) - strlen($scriptName));
	//print($scriptPath."\n");
	$logFile = $scriptPath."logMaintainence.log";
	chdir($scriptPath);
	file_put_contents($logFile, "Changed directory to $scriptPath.\n", FILE_APPEND);
	
	$dir = $scriptPath;
	$files = scandir($dir, 1);
	$time = date("d-m-Y H:i:s");
	file_put_contents($logFile, "\n\n\n***** $time => PERFORMING CLEANUP !!! *****\n", FILE_APPEND);
	foreach ($files as $key) {
		if(($key != ".") && ($key !="..")){
			if(is_dir($dir."/".$key)){
				$pplHere = file_get_contents($dir."/".$key."/peopleHere");
				file_put_contents($logFile, "Session[$key]: ".$pplHere, FILE_APPEND);
				if($pplHere <= 0){
					file_put_contents($logFile, "    - Deleting this session...!", FILE_APPEND);
					deleteDir($dir."/".$key);
				}
				file_put_contents($logFile, "\n", FILE_APPEND);
			}
		}
	}
	file_put_contents($logFile, "\n***** $time => CLEANUP COMPLETED !!! *****\n\n\n\n", FILE_APPEND);
?>
craft