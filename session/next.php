<?php
session_start();
if(isset($_SESSION["sid"])){
	$no = file_get_contents($_SESSION["sid"]."/no", LOCK_EX);
	$cPlay = file_get_contents($_SESSION["sid"]."/currentlyPlaying", LOCK_EX);
	//print("$cPlay - $no <br>");
	if($cPlay == $no){
		print("ERROR_2");
	}
	else{
		$filename = $_SESSION["sid"]."/lawl";
		$fin = fopen($filename, 'r');
		if($fin == false){
			print("Could not open file !");
			exit();
		}
		$counter = 0;
		while(!feof($fin)){
			$buffer = fgets($fin);
				if($counter == $cPlay){
					$twig = explode(";", $buffer);
					$fromFile = rtrim($twig[1]);
					$fromSite = rtrim($_GET["cPlay"]);
					if($fromFile === $fromSite){
						//print("$fromFile & $fromSite are cool adding 1 to currently playing...");
						$cPlay = file_put_contents($_SESSION["sid"]."/currentlyPlaying", $cPlay+1, LOCK_EX);
					}
					else{
						//print("$fromFile & $fromSite NOT cool. NOT adding 1 to currently playing...");
					}
					break;
				}
			$counter++;
		}
	}
}

?>