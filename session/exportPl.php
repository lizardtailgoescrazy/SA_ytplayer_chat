<?php
	session_start();
		if(isset($_SESSION["sid"])){
		$filename = $_SESSION["sid"]."/lawl";
		$fin = fopen($filename, 'r');
		if($fin == false){
			print("Could not open file !");
			exit();
		}
		while(!feof($fin)){
			$buffer = fgets($fin);
			if($buffer != ""){
					$twig = explode(";", $buffer);
					print("http://www.youtube.com/watch?v=".$twig[1]."\n");
			}
		}
		header("Content-Disposition: attachment; filename=\"sugaryAsphaltPlaylist.txt\""); 
		header("Content-Type: text/csv");
	}
?>