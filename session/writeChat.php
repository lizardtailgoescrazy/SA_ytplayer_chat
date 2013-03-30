<?php
	session_start();
	$filename = $_SESSION["sid"]."/chatLog.html";
	if(isset($_SESSION['nick'])){
		$text = stripslashes(htmlspecialchars($_POST['text']));
		$msg = "<p><b>[".$_SESSION["nick"]."]</b>: $text </p>";

		print(file_put_contents($filename, $msg, FILE_APPEND | LOCK_EX));
	}
?>
