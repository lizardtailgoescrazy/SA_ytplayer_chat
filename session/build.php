<?php
session_start();
//Need to add verification of valid id, dunno if client side or server side both reqired
	if(isset($_SESSION["sid"])){
		$no = file_get_contents($_SESSION["sid"]."/no", LOCK_EX);
		if($no == 0){
			file_put_contents($_SESSION["sid"]."/startTime", time()+2, LOCK_EX);
			//chmod($_SESSION["sid"]."/startTime", 0777);
		}
		$content = $no.";".$_GET['vid'].";".$_SESSION["nick"]."\n";
		if(!file_put_contents($_SESSION["sid"]."/lawl", $content, FILE_APPEND | LOCK_EX)){
			echo "ERROR: Unable to write to disk !";
			exit();
		}
		//chmod($_SESSION["sid"]."/lawl", 0777);
		$no++;
		file_put_contents($_SESSION["sid"]."/no", $no, LOCK_EX);
	}
?>