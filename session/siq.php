<?php
	session_start();
	if(isset($_SESSION["sid"])){
		$no = file_get_contents($_SESSION["sid"]."/no", LOCK_EX);
		$cPlay = file_get_contents($_SESSION["sid"]."/currentlyPlaying", LOCK_EX);
		print($no-$cPlay);
	}
?>