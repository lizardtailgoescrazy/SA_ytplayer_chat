<?php
session_start();
	$no = file_get_contents($_SESSION["sid"]."/no", LOCK_EX);
	if($no == 0){
		file_put_contents($_SESSION["sid"]."/startTime", time()+2, LOCK_EX);
	}
	$content = $no.";".$_GET['vid']."\n";
	if(!file_put_contents($_SESSION["sid"]."/lawl", $content, FILE_APPEND | LOCK_EX)){
		echo "Unable to output file !";
	}
	$no++;
	file_put_contents($_SESSION["sid"]."/no", $no, LOCK_EX);
?>