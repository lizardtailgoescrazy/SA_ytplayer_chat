<?php
	session_start();
	if(isset($_SESSION["sid"])){
		$no = file_get_contents($_SESSION["sid"]."/no", LOCK_EX);
		$cPlay = file_get_contents($_SESSION["sid"]."/currentlyPlaying", LOCK_EX);
		if($no == 0){
			print("ERROR_1");
		}
		else if($cPlay == $no){
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
						if($_GET['mode'] == "next"){
							$t = time();
							file_put_contents($_SESSION["sid"]."/startTime", $t, LOCK_EX);
							print("0;".$twig[1].";".$twig[2]);
						}
						else if($_GET['mode'] == "entry"){
							$playedTime = file_get_contents($_SESSION["sid"]."/startTime", LOCK_EX);
							$seekTime = time() - $playedTime;
							print("$seekTime;".$twig[1].";".$twig[2]);
						}
						break;
					}
				$counter++;
			}
		}
	}
?>