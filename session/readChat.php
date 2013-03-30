<?php
session_start();
$filename = $_SESSION["sid"]."/chatLog";
print(file_get_contents($filename, LOCK_EX));
?>